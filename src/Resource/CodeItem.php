<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class CodeItem
{
    /** @var array */
    private $data;
    private $links;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->links = new Links($data['links'] ?? []);
    }

    /**
     * Check if the code is a text code
     *
     * @return bool True if code type is text
     */
    public function isText(): bool {
        return ($this->data['codeType'] ?? '') === 'CODE_TEXT';
    }


    /**
     * Check if the code is an image code
     *
     * @return bool True if code type is image
     */
    public function isImage(): bool {
        return ($this->data['codeType'] ?? '') === 'CODE_IMAGE';
    }

    /**
     * Check if the code is a pre-order code
     *
     * @return bool True if code type is pre-order
     */
    public function isPreOrder(): bool {
        return ($this->data['codeType'] ?? '') === 'PRE_ORDER';
    }

    public function getCodeId(): ?string { return $this->data['codeId'] ?? null; }
    public function getCodeType(): ?string { return $this->data['codeType'] ?? null; }
    public function getCode(): ?string { return $this->data['code'] ?? null; }
    public function getFilename(): ?string { return $this->data['filename'] ?? null; }

    public function getLinks(): Links
    {
        return $this->links;
    }

    /**
     * Save image code as a file
     *
     * Downloads the image code using the client and saves it to the specified directory.
     *
     * @param Client $client The CodesWholesale client
     * @param string $saveDir Directory to save images
     * @param string $baseUrl Base URL to remove from href
     *
     * @return string Full path of saved image
     * @throws Exception If the code is not an image or download fails
     */
    public function saveImageBase64(Client $client, string $saveDir = __DIR__ . '/codes', string $baseUrl = ''): string {

        if (!$this->isImage()) {
            throw new Exception("Only image codes can be downloaded.");
        }

        $links = $this->getLinks();
        $link = $links->first()->getHref();
        if (empty($link)) {
            throw new Exception("No link found for this image code.");
        }
        $endpoint = $baseUrl ? str_replace($baseUrl, '', $link) : $link;
        $data = $client->requestData('GET', $endpoint);

        if (empty($data['filename']) || empty($data['code'])) {
            throw new Exception("Invalid API response for image code.");
        }

        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

        $filepath = rtrim($saveDir, '/') . '/' . $data['filename'];
        $decoded = base64_decode($data['code']);

        if ($decoded === false) {
            throw new Exception("Failed to decode base64 image data.");
        }
        file_put_contents($filepath, $decoded);
        return $filepath;
    }

    public function toArray(): array { return $this->data; }
}
