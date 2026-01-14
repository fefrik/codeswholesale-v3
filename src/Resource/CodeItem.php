<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;
use Exception;

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
     * @throws Exception
     */
    public function saveImageBase64(Client $client, string $saveDir = __DIR__ . '/codes', string $baseUrl = ''): string {

        if (!$this->isImage()) {
            throw new Exception("Only image codes can be downloaded.");
        }

        if (!$this->getCode()) {
            throw new Exception("Download link not found for the image code.");
        }

        $fullPath = self::prepareDirectory($saveDir, $this->getFileName());
        $result = file_put_contents($fullPath, base64_decode($this->getCode()));

        if (!$result) {
            throw new Exception("Not able to write image code!");
        }
        return $fullPath;
    }

    private static function prepareDirectory($whereToSaveDirectory, $fileName): string
    {
        if (!file_exists($whereToSaveDirectory)) {
            mkdir($whereToSaveDirectory, 0755, true);
        }
        return $whereToSaveDirectory . "/" . $fileName;
    }

    public function toArray(): array { return $this->data; }
}
