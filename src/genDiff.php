<?php

declare(strict_types=1);

namespace Differ\Differ;

use InvalidArgumentException;

function genDiff(string $file1, string $file2): string
{
    $diff = [];

    if (!file_exists($file1) || false === ($content1 = file_get_contents($file1))) {
        throw new InvalidArgumentException("Invalid file: $file1");
    }

    if (!file_exists($file2) || false === ($content2 = file_get_contents($file2))) {
        throw new InvalidArgumentException("Invalid file: $file2");
    }

    $data1 = json_decode($content1, true);
    $data2 = json_decode($content2, true);

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
