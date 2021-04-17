<?php

declare(strict_types=1);

namespace Differ\Differ;

use InvalidArgumentException;
use RuntimeException;

use function Differ\Parser\parse;

/**
 * @param string $key
 * @param string $type
 * @param mixed $oldValue
 * @param mixed|null $newValue
 * @return array<string, mixed>
 */
function makeNode(string $key, string $type, mixed $oldValue, mixed $newValue = null): array
{
    return [
        "key" => $key,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue,
    ];
}

/**
 * @param string $filepath1
 * @param string $filepath2
 * @return string
 */
function genDiff(string $filepath1, string $filepath2): string
{
    $diff = [];

    $data1 = getData($filepath1);
    $data2 = getData($filepath2);

    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $keys = array_values(array_unique(array_merge($keys1, $keys2)));
    usort($keys, fn($a, $b) => $a <=> $b);

    foreach ($keys as $key) {
        if (!property_exists($data1, $key)) {
            $diff[] = makeNode($key, 'added', null, $data2->$key);
            continue;
        }

        if (!property_exists($data2, $key)) {
            $diff[] = makeNode($key, 'deleted', $data1->$key);
            continue;
        }

        if ($data1->$key !== $data2->$key) {
            $diff[] = makeNode($key, 'changed', $data1->$key, $data2->$key);
            continue;
        }

        $diff[] = makeNode($key, 'unchanged', $data1->$key, $data2->$key);
    }

    return format($diff);
}

/**
 * @param string $filepath
 * @return \stdClass
 */
function getData(string $filepath): \stdClass
{
    if (!file_exists($filepath)) {
        throw new InvalidArgumentException("File $filepath does not exists");
    }

    $content = file_get_contents($filepath);
    if (false === $content) {
        throw new RuntimeException("Cannot read file $filepath");
    }

    $format = pathinfo($filepath, PATHINFO_EXTENSION);
    return parse($content, $format);
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
    $formatted = [];
    foreach ($data as ['key' => $key, 'type' => $type, 'oldValue' => $oldValue, 'newValue' => $newValue]) {
        $oldValue = formatValue($oldValue);
        $newValue = formatValue($newValue);

        if ($type === 'deleted') {
            $formatted[] = "  - $key: $oldValue";
        }

        if ($type === 'unchanged') {
            $formatted[] = "    $key: $oldValue";
        }

        if ($type === 'changed') {
            $formatted[] = "  - $key: $oldValue";
            $formatted[] = "  + $key: $newValue";
        }

        if ($type === 'added') {
            $formatted[] = "  + $key: $newValue";
        }
    }

    return "{\n" . implode("\n", $formatted) . "\n}";
}
