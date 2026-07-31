<?php

namespace CodesWholesaleApi\Service;

use CodesWholesaleApi\Resource\CodeItem;

final class ImageCodeWriter
{
    public function write(CodeItem $code, string $directory): string
    {
        if (!$code->isImage()) throw new \InvalidArgumentException('Only image codes can be written.');

        $content = $code->getCode();
        if ($content === null || $content === '') throw new \RuntimeException('Image code has no content.');

        $filename = $code->getFilename();
        if ($filename === null || $filename === '') throw new \RuntimeException('Image code has no filename.');
        $filename = basename($filename);

        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new \RuntimeException('Failed to create image code directory: ' . $directory);
        }

        $decoded = base64_decode($content, true);
        if ($decoded === false) throw new \RuntimeException('Image code contains invalid base64 data.');

        $path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $filename;
        $handle = @fopen($path, 'xb');
        if ($handle === false) throw new \RuntimeException('Image code file already exists or cannot be created.');

        $complete = false;
        try {
            if (fwrite($handle, $decoded) !== strlen($decoded)) {
                throw new \RuntimeException('Unable to write the complete image code.');
            }
            $complete = true;
        } finally {
            fclose($handle);
            if (!$complete) @unlink($path);
        }

        return $path;
    }
}
