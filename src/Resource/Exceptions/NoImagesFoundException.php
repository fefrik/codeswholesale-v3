<?php

namespace CodesWholesaleApi\Resource\Exceptions;

final class NoImagesFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Images not found')
    {
        parent::__construct($message);
    }
}
