<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Resource\Exceptions\ResourceMappingException;

abstract class Resource
{
    private readonly \stdClass $data;

    public function __construct(\stdClass $data)
    {
        $this->data = self::cloneObject($data);
    }

    protected function str(string $key): ?string
    {
        $value = $this->value($key);
        if ($value === null) return null;
        if (!is_string($value)) throw ResourceMappingException::invalidType($key, 'string', $value);
        return $value;
    }

    protected function int(string $key): ?int
    {
        $value = $this->value($key);
        if ($value === null) return null;
        if (!is_int($value)) throw ResourceMappingException::invalidType($key, 'int', $value);
        return $value;
    }

    protected function float(string $key): ?float
    {
        $value = $this->value($key);
        if ($value === null) return null;
        if (!is_int($value) && !is_float($value)) {
            throw ResourceMappingException::invalidType($key, 'float', $value);
        }
        return (float) $value;
    }

    protected function bool(string $key): ?bool
    {
        $value = $this->value($key);
        if ($value === null) return null;
        if (!is_bool($value)) throw ResourceMappingException::invalidType($key, 'bool', $value);
        return $value;
    }

    protected function dateTime(string $key): ?\DateTimeImmutable
    {
        $value = $this->str($key);
        if ($value === null || $value === '') return null;

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            throw new ResourceMappingException('Invalid date in resource field "' . $key . '": ' . $value, 0, $e);
        }
    }

    /** @return \Generator<int, \stdClass, void, void> */
    protected function iterateObjects(string $key): \Generator
    {
        $value = $this->value($key);
        if ($value === null) return;
        if (!is_array($value)) throw ResourceMappingException::invalidType($key, 'array<object>', $value);

        foreach ($value as $index => $item) {
            if (!$item instanceof \stdClass) {
                throw ResourceMappingException::invalidType($key . '[' . $index . ']', 'object', $item);
            }
            yield $item;
        }
    }

    /** @return list<string> */
    protected function stringList(string $key): array
    {
        $value = $this->value($key);
        if ($value === null) return [];
        if (!is_array($value)) throw ResourceMappingException::invalidType($key, 'array<string>', $value);

        $result = [];
        foreach ($value as $index => $item) {
            if (!is_string($item)) {
                throw ResourceMappingException::invalidType($key . '[' . $index . ']', 'string', $item);
            }
            $result[] = $item;
        }
        return $result;
    }

    public function raw(): \stdClass
    {
        return self::cloneObject($this->data);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        /** @var array<string, mixed> $result */
        $result = json_decode(json_encode($this->data, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        return $result;
    }

    private function value(string $key): mixed
    {
        return property_exists($this->data, $key) ? $this->data->{$key} : null;
    }

    private static function cloneObject(\stdClass $data): \stdClass
    {
        $clone = json_decode(json_encode($data, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        if (!$clone instanceof \stdClass) {
            throw new \LogicException('Resource data must encode to a JSON object.');
        }
        return $clone;
    }
}
