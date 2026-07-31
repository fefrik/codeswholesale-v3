<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Enum\CodeType;
use CodesWholesaleApi\Service\ImageCodeWriter;

final class CodeItem extends Resource
{
    /** @return list<LinkItem> */
    public function getLinks(): array
    {
        return iterator_to_array($this->iterateLinks(), false);
    }

    /** @return \Generator<int, LinkItem, void, void> */
    public function iterateLinks(): \Generator
    {
        foreach ($this->iterateObjects('links') as $item) yield new LinkItem($item);
    }

    public function getType(): ?CodeType
    {
        $type = $this->getCodeType();
        return $type === null ? null : CodeType::tryFrom($type);
    }

    public function isText(): bool { return $this->getType() === CodeType::Text; }
    public function isImage(): bool { return $this->getType() === CodeType::Image; }
    public function isPreOrder(): bool { return $this->getType() === CodeType::PreOrder; }
    public function getCodeId(): ?string { return $this->str('codeId'); }
    public function getCodeType(): ?string { return $this->str('codeType'); }
    public function getCode(): ?string { return $this->str('code'); }
    public function getFilename(): ?string { return $this->str('filename'); }

    /** @deprecated Use ImageCodeWriter so resources stay free of filesystem side effects. */
    public function saveImageBase64(string $saveDir = __DIR__ . '/codes'): string
    {
        return (new ImageCodeWriter())->write($this, $saveDir);
    }
}
