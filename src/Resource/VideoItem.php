<?php

namespace CodesWholesaleApi\Resource;

final class VideoItem extends Resource
{
    public function hasAgeWarning(): ?bool { return $this->bool('ageWarning'); }
    public function getPreviewFrameUrl(): ?string { return $this->str('previewFrameURL'); }
    public function getTitle(): ?string { return $this->str('title'); }
    public function getUrl(): ?string { return $this->str('url'); }
}
