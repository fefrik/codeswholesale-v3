<?php
namespace CodesWholesaleApi\Resource\Exceptions;

class NoImagesFoundException extends \Exception
{
    /**
     * @var string
     */
    public $message = 'Images not found';
}