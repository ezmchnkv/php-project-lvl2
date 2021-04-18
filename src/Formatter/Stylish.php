<?php

declare(strict_types=1);

namespace Differ\Formatter\Stylish;

/**
 * @param array<int, array> $diff
 * @param int $depth
 * @return string
 */
function format(array $diff, int $depth = 1): string
{
    $indent = str_repeat(' ', 4 * ($depth - 1));

    $formatted = [];
    foreach (
        $diff as [
            'key' => $key,
            'type' => $type,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'children' => $children
        ]
    ) {
        $oldValue = stringify($oldValue, $depth);
        $newValue = stringify($newValue, $depth);

        if ($type === 'nested') {
            $formatted[] = "{$indent}    $key: " . format($children, 1 + $depth);
        }

        if ($type === 'deleted') {
            $formatted[] = "{$indent}  - $key: $oldValue";
        }

        if ($type === 'unchanged') {
            $formatted[] = "{$indent}    $key: $oldValue";
        }

        if ($type === 'changed') {
            $formatted[] = "{$indent}  - $key: $oldValue";
            $formatted[] = "{$indent}  + $key: $newValue";
        }

        if ($type === 'added') {
            $formatted[] = "{$indent}  + $key: $newValue";
        }
    }

    return "{\n" . implode("\n", $formatted) . "\n$indent}";
}

function stringify(mixed $value, int $depth = 1): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_object($value)) {
        $keys = array_keys(get_object_vars($value));
        $indent = str_repeat(' ', 4 * $depth);

        $formatted = [];
        foreach ($keys as $key) {
            $formattedValue = stringify($value->$key, 1 + $depth);
            $formatted[] = "$indent    $key: $formattedValue";
        }

        $formattedString = implode("\n", $formatted);
        return "{\n$formattedString\n$indent}";
    }

    return (string) $value;
}
