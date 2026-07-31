<?php

namespace CodesWholesaleApi\Resource\Exceptions;

final class ResourceMappingException extends \UnexpectedValueException
{
    public static function invalidType(string $field, string $expected, mixed $actual): self
    {
        return new self(sprintf(
            'Invalid resource field "%s": expected %s, got %s.',
            $field,
            $expected,
            get_debug_type($actual)
        ));
    }
}
