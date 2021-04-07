<?php

declare(strict_types=1);

namespace Differ\Differ;

use InvalidArgumentException;
use RuntimeException;

function genDiff(string $filepath1, string $filepath2): string
{
    $diff = [];

    $data1 = getData($filepath1);
    $data2 = getData($filepath2);

    foreach ($data1 as $key => $value) {
        if (!array_key_exists($key, $data2)) {
            $diff["- $key"] = $value;
        }
    }

    foreach ($data1 as $key => $value) {
        if (array_key_exists($key, $data2) && $value === $data2[$key]) {
            $diff["  $key"] = $value;
        }
    }

    foreach ($data1 as $key => $value) {
        if (array_key_exists($key, $data2) && $value !== $data2[$key]) {
            $diff["- $key"] = $value;
            $diff["+ $key"] = $data2[$key];
        }
    }

    foreach ($data2 as $key => $value) {
        if (!array_key_exists($key, $data1)) {
            $diff["+ $key"] = $value;
        }
    }

    return format($diff);
}

/**
 * @param string $filepath
 * @return array<mixed>
 * @throws \JsonException
 */
function getData(string $filepath): array
{
    if (!file_exists($filepath)) {
        throw new InvalidArgumentException("File $filepath does not exists");
    }

    $content = file_get_contents($filepath);
    if (false === $content) {
        throw new RuntimeException("Cannot read file $filepath");
    }

    return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
}

function formatValue(mixed $value): string
{
    if (is_bool($value)) {
        return true === $value ? "true" : "false";
    }

    return (string) $value;
}

/**
 * @param array<mixed> $data
 * @return string
 */
function format(array $data): string
{
    $result = ["{\n"];
    foreach ($data as $key => $value) {
        $value = formatValue($value);
        $result[] = "  $key: $value\n";
    }
    $result[] = "}";

    return implode('', $result);
}
