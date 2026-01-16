<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Auth\TokenNormalizer;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Http\HttpResponse;
use CodesWholesaleApi\Storage\OAuth2\OAuthStorageInterface;

final class Client
{
    /** @var Config */
    private $config;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var OAuthStorageInterface */
    private $storage;

    /** @var TokenNormalizer */
    private $normalizer;

    /** @var int */
    private $timeoutSeconds;

    /** @var string */
    private $userAgent;

    public function __construct(
        Config                $config,
        OAuthStorageInterface $storage,
        string                $clientId,
        string                $clientSecret,
        TokenNormalizer       $normalizer = null,
        int                   $timeoutSeconds = 20,
        string                $userAgent = 'CodesWholesaleClient/1.0'
    ) {
        $this->config = $config;
        $this->storage = $storage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->normalizer = $normalizer ?: new TokenNormalizer();
        $this->timeoutSeconds = $timeoutSeconds;
        $this->userAgent = $userAgent;
    }

    // -------------------------
    // Public API
    // -------------------------

    public function get(string $path, array $query = []): ApiResponse
    {
        return $this->request('GET', $path, null, $query);
    }

    public function getData(string $path, array $query = []): \stdClass
    {
        return $this->get($path, $query)->getData();
    }

    public function post(string $path, array $body = [], array $query = []): ApiResponse
    {
        return $this->request('POST', $path, $body, $query);
    }

    public function put(string $path, array $body = [], array $query = []): ApiResponse
    {
        return $this->request('PUT', $path, $body, $query);
    }

    public function delete(string $path, array $query = []): ApiResponse
    {
        return $this->request('DELETE', $path, null, $query);
    }

    public function request(string $method, string $path, array $body = null, array $query = []): ApiResponse
    {
        return $this->requestWithAuthRetry($method, $path, $body, $query, true);
    }

    public function requestData(string $method, string $path, array $body = null, array $query = []): \stdClass
    {
        return $this->request($method, $path, $body, $query)->getData();
    }

    // -------------------------
    // Auth retry wrapper
    // -------------------------

    private function requestWithAuthRetry(
        string $method,
        string $path,
        array $body = null,
        array $query = [],
        bool $allowRetry = true
    ): ApiResponse {
        $token = $this->getValidAccessToken();

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ];

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        $http = $this->curlJson(
            $method,
            $this->config->getApiBaseUrl() . $this->normalizePath($path),
            $headers,
            $body,
            $query
        );

        // token umřel mezi requesty → 401 → refresh + retry 1x
        if ($http->isUnauthorized() && $allowRetry) {
            $this->storage->clearToken();
            return $this->requestWithAuthRetry($method, $path, $body, $query, false);
        }

        // error handling
        if (!$http->isSuccess()) {
            $json = $http->getJsonBody();

            $msg = 'Request failed';
            if ($json instanceof \stdClass) {
                $msg =
                    (isset($json->message) ? (string) $json->message : null)
                        ?: (isset($json->error_description) ? (string) $json->error_description : null)
                        ?: (isset($json->error) ? (string) $json->error : null)
                            ?: 'Request failed';
            } elseif (is_array($json)) {
                // fallback pro endpointy co vrátí top-level array
                $msg = 'Request failed';
            }

            throw new ApiException(
                $http,
                "CodesWholesale API error. HTTP {$http->getStatus()}. {$msg}. Body: {$http->getRawBody()}"
            );
        }

        // 2xx, ale není JSON → pro klienta je to kontraktová chyba
        if ($http->getJsonBody() === null) {
            throw new ApiException(
                $http,
                "CodesWholesale API returned non-JSON body. HTTP {$http->getStatus()}. Body: {$http->getRawBody()}"
            );
        }

        $json = $http->getJsonBody();
        if (!$json instanceof \stdClass) {
            // Kontrakt: pro Resource vrstvu chceme top-level objekt
            // (pokud některý endpoint vrací array, řeš ho separátně)
            throw new ApiException(
                $http,
                "CodesWholesale API returned unexpected JSON type (expected object). HTTP {$http->getStatus()}. Body: {$http->getRawBody()}"
            );
        }

        return new ApiResponse($http, $json);
    }

    // -------------------------
    // Token handling
    // -------------------------

    private function getValidAccessToken(): string
    {
        $cached = $this->storage->getToken();
        if (is_array($cached) && !empty($cached['access_token'])) {
            return (string) $cached['access_token'];
        }

        $raw = $this->fetchOAuthToken();
        $normalized = $this->normalizer->normalize($raw);
        $this->storage->saveToken($normalized);

        return (string) $normalized['access_token'];
    }

    private function fetchOAuthToken(): array
    {
        $query = http_build_query([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], '', '&', PHP_QUERY_RFC3986);

        $url = $this->config->getApiBaseUrl() . '/oauth/token?' . $query;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("OAuth token request failed: cURL error {$errno}: {$error}");
        }

        $data = json_decode((string) $response, true);
        if (!is_array($data)) {
            throw new \RuntimeException("OAuth token response is not valid JSON. HTTP {$status}. Body: {$response}");
        }

        if ($status < 200 || $status >= 300) {
            $msg = $data['message'] ?? ($data['error_description'] ?? ($data['error'] ?? 'Unknown OAuth error'));
            throw new \RuntimeException("OAuth token request failed. HTTP {$status}. {$msg}");
        }

        return $data;
    }

    // -------------------------
    // HTTP helper
    // -------------------------

    private function curlJson(
        string $method,
        string $url,
        array $headers,
        ?array $body = null,
        array $query = []
    ): HttpResponse {
        if (!empty($query)) {
            $url .= (strpos($url, '?') !== false ? '&' : '?')
                . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $payload = null;
        if ($body !== null) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE);
            if ($payload === false) {
                throw new \RuntimeException('Failed to JSON-encode request body: ' . json_last_error_msg());
            }
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException("HTTP request failed: cURL error {$errno}: {$error}");
        }

        // ✅ JSON decode: akceptuj object i array, a hlídej json_last_error
        $json = null;
        $trimmed = trim((string) $raw);
        if ($trimmed !== '') {
            $decoded = json_decode($trimmed); // vrací stdClass nebo array
            if (json_last_error() === JSON_ERROR_NONE) {
                $json = $decoded;
            }
        }

        return new HttpResponse(
            $status,
            (string) $raw,
            $json,
            []
        );
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        return ($path[0] === '/') ? $path : '/' . $path;
    }

}
