<?php

namespace CodesWholesaleApi\Resource;

use RuntimeException;

final class CodeItem extends Resource
{
    private const CODE_TEXT  = 'CODE_TEXT';
    private const CODE_IMAGE = 'CODE_IMAGE';
    private const PRE_ORDER  = 'PRE_ORDER';

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);
    }

    /** @return array<int, LinkItem> */
    public function getLinks(): array
    {
        return array_map(
            function (\stdClass $p) {
                return new LinkItem($p);
            },
            $this->list('links')
        );
    }

    /**
     * Check if the code is a text code
     *
     * @return bool True if code type is text
     */
    public function isText(): bool { return $this->getCodeType() === self::CODE_TEXT; }


    /**
     * Check if the code is an image code
     *
     * @return bool True if code type is image
     */
    public function isImage(): bool { return $this->getCodeType() === self::CODE_IMAGE; }

    /**
     * Check if the code is a pre-order code
     *
     * @return bool True if code type is pre-order
     */
    public function isPreOrder(): bool
    { return $this->getCodeType() === self::PRE_ORDER; }

    /**
     * Get the id for the code
     * @return string|null
     */
    public function getCodeId(): ?string { return $this->str('codeId'); }

    /**
     * Get the type of the code
     * @return string|null
     */
    public function getCodeType(): ?string { return $this->str('codeType'); }
    /**
     * CODE_IMAGE contains base64 string and CODE_TEXT is simple string
     */
    public function getCode(): ?string { return $this->str('code'); }
    public function getFilename(): ?string { return $this->str('filename'); }

    /**
     * Save image code as a file
     *
     * Downloads the image code using the client and saves it to the specified directory.
     *
     * @param string $saveDir Directory to save images
     *
     * @return string Full path of saved image
     * @throws RuntimeException
     */
    public function saveImageBase64(string $saveDir = __DIR__ . '/codes'): string {

        if (!$this->isImage()) {
            throw new RuntimeException("Only image codes can be downloaded.");
        }

        $content = $this->getCode();
        if (!$content) {
            throw new RuntimeException("Download link not found for the image code.");
        }

        $fileName = $this->getFilename();
        if (!$fileName) {
            throw new RuntimeException('Filename not found for the image code.');
        }

        $fileName = basename($fileName);
        $fullPath = self::prepareDirectory($saveDir, $fileName);

        $decoded = base64_decode($content, true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid base64 content for image code.');
        }

        if (file_put_contents($fullPath, $decoded) === false) {
            throw new RuntimeException("Not able to write image code!");
        }
        return $fullPath;
    }

    private static function prepareDirectory($whereToSaveDirectory, $fileName): string
    {
        if (!is_dir($whereToSaveDirectory)) {
            if (!mkdir($whereToSaveDirectory, 0755, true) && !is_dir($whereToSaveDirectory)) {
                throw new RuntimeException("Failed to create directory: {$whereToSaveDirectory}");
            }
        }
        return rtrim($whereToSaveDirectory, '/\\') . DIRECTORY_SEPARATOR . $fileName;
    }
}
