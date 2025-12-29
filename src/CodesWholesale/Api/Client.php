<?php

namespace CodesWholesale\Api;

use CodesWholesale\Auth\TokenNormalizer;
use CodesWholesale\Config\CodesWholesaleConfig;
use CodesWholesale\Storage\StorageInterface;

final class Client
{
    /** @var CodesWholesaleConfig */
    private $config;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var StorageInterface */
    private $storage;

    /** @var TokenNormalizer */
    private $normalizer;

    /** @var int */
    private $timeoutSeconds;

    /** @var string */
    private $userAgent;

    public function __construct(
        CodesWholesaleConfig $config,
        StorageInterface $storage,
        string $clientId,
        string $clientSecret,
        TokenNormalizer $normalizer = null,
        int $timeoutSeconds = 20,
        string $userAgent = 'CodesWholesaleClient/1.0'
    ) {
        $this->config = $config;
        $this->storage = $storage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->normalizer = $normalizer ?: new TokenNormalizer();
        $this->timeoutSeconds = $timeoutSeconds;
        $this->userAgent = $userAgent;
    }

    public function get(string $path, array $query = array()): array
    {
        return $this->request('GET', $path, null, $query);
    }

    public function post(string $path, array $body = array(), array $query = array()): array
    {
        return $this->request('POST', $path, $body, $query);
    }

    public function put(string $path, array $body = array(), array $query = array()): array
    {
        return $this->request('PUT', $path, $body, $query);
    }

    public function delete(string $path, array $query = array()): array
    {
        return $this->request('DELETE', $path, null, $query);
    }

    public function request(string $method, string $path, array $body = null, array $query = array()): array
    {
        return $this->requestWithAuthRetry($method, $path, $body, $query, true);
    }

    private function requestWithAuthRetry(string $method, string $path, array $body = null, array $query = array(), bool $allowRetry = true): array
    {
        $token = $this->getValidAccessToken();

        $headers = array(
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        );

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        // důležité: neházej výjimku hned, chceme vidět status (kvůli 401)
        $res = $this->curlJson(
            $method,
            $this->config->getApiBaseUrl() . $this->normalizePath($path),
            $headers,
            $body,
            $query,
            false
        );

        // Pokud token “umřel” mezi requesty → 401 → refresh + retry 1x
        if ($res['status'] === 401 && $allowRetry) {
            $this->storage->clearToken();

            // druhý pokus už bez dalšího retry
            return $this->requestWithAuthRetry($method, $path, $body, $query, false);
        }

        // Pokud je to pořád chyba, teď už to vyhoď jako výjimku
        if ($res['status'] < 200 || $res['status'] >= 300) {
            $msg = (is_array($res['data']) && isset($res['data']['message']))
                ? $res['data']['message']
                : 'Request failed';

            throw new \RuntimeException("CodesWholesale API error. HTTP {$res['status']}. {$msg}. Body: {$res['raw']}");
        }

        return $res;
    }


    // --- Token handling ---

    private function getValidAccessToken(): string
    {
        $cached = $this->storage->getToken();
        if (is_array($cached) && !empty($cached['access_token'])) {
            return (string)$cached['access_token'];
        }

        $raw = $this->fetchOAuthToken();
        $normalized = $this->normalizer->normalize($raw);
        $this->storage->saveToken($normalized);

        return (string)$normalized['access_token'];
    }

    private function fetchOAuthToken(): array
    {
        $query = http_build_query(array(
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ), '', '&', PHP_QUERY_RFC3986);

        $url = $this->config->getApiBaseUrl() . '/oauth/token?' . $query;

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // žádné body, parametry jsou v query
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
            ),
        ));

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("OAuth token request failed: cURL error {$errno}: {$error}");
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new \RuntimeException("OAuth token response is not valid JSON. HTTP {$status}. Body: {$response}");
        }

        if ($status < 200 || $status >= 300) {
            $msg = $data['message'] ?? ($data['error_description'] ?? ($data['error'] ?? 'Unknown OAuth error'));

            throw new \RuntimeException("OAuth token request failed. HTTP {$status}. {$msg}");
        }

        return $data;
    }


    // --- HTTP helper ---

    private function curlJson(string $method, string $url, array $headers, array $body = null, array $query = array(), bool $throwOnHttpError = true): array
    {
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
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
        ));

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("HTTP request failed: cURL error {$errno}: {$error}");
        }

        $data = null;
        if ($response !== '' && $response !== null) {
            $decoded = json_decode($response, true);
            $data = is_array($decoded) ? $decoded : null;
        }

        if ($throwOnHttpError && ($status < 200 || $status >= 300)) {
            $msg = is_array($data) && isset($data['message']) ? $data['message'] : 'Request failed';
            throw new \RuntimeException("CodesWholesale API error. HTTP {$status}. {$msg}. Body: {$response}");
        }

        return array(
            'status' => $status,
            'headers' => array(),
            'data' => $data !== null ? $data : $response,
            'raw' => (string)$response,
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
