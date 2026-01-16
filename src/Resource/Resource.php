<?php

namespace CodesWholesaleApi\Resource;

/**
 * Resource base class
 */
abstract class Resource
{
    protected \stdClass $data;

    public function __construct(\stdClass $data)
    {
        $this->data = $data;
    }

    protected function str(string $key): ?string
    {
        return isset($this->data->{$key}) ? (string) $this->data->{$key} : null;
    }

    protected function int(string $key): ?int
    {
        return isset($this->data->{$key}) ? (int) $this->data->{$key} : null;
    }

    protected function float(string $key): ?float
    {
        return isset($this->data->{$key}) ? (float) $this->data->{$key} : null;
    }

    protected function bool(string $key): ?bool
    {
        return isset($this->data->{$key}) ? (bool) $this->data->{$key} : null;
    }

    /**
     * Returns list of objects (stdClass) from JSON array field.
     *
     * @return array<int, \stdClass>
     */
    protected function list(string $key): array
    {
        $v = $this->data->{$key} ?? [];

        if (!is_array($v)) {
            return [];
        }

        // jen stdClass prvky + reindex (0..n)
        $out = [];
        foreach ($v as $item) {
            if ($item instanceof \stdClass) {
                $out[] = $item;
            }
        }

        return $out;
    }

    protected function obj(string $key): ?\stdClass
    {
        $v = $this->data->{$key} ?? null;
        return ($v instanceof \stdClass) ? $v : null;
    }

    /**
     * Returns scalar array (e.g. list of strings) from JSON array field.
     * No filtering is done (keeps ints/strings/bools/null if API sends them).
     */
    protected function scalarArray(string $key): array
    {
        $v = $this->data->{$key} ?? [];
        return is_array($v) ? array_values($v) : [];
    }

    public function raw(): \stdClass
    {
        return $this->data;
    }

    /**
     * Deep-convert underlying JSON object to associative array.
     */
    public function toArray(): array
    {
        $json = json_encode($this->data, JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            // fail fast – kdyby se někdy objevily neenkódovatelné hodnoty
            throw new \RuntimeException('Failed to JSON-encode Resource: ' . json_last_error_msg());
        }

        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }
}
