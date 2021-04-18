<?php

declare(strict_types=1);

namespace Differ\Formatters\Plain;

/**
 * @param array<int, mixed> $diff
 * @param string $parent
 * @return string
 */
function format(array $diff, string $parent = ''): string
{
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
        $property = "$parent$key";

        $oldValue = stringify($oldValue);
        $newValue = stringify($newValue);

        if ($type === 'nested') {
            $formatted[] = format($children, "$property.");
        }

        if ($type === 'deleted') {
            $formatted[] = "Property '{$property}' was removed";
        }

        if ($type === 'changed') {
            $formatted[] = "Property '{$property}' was updated. From $oldValue to $newValue";
        }

        if ($type === 'added') {
            $formatted[] = "Property '{$property}' was added with value: $newValue";
        }
    }

    return implode("\n", $formatted);
}

function stringify(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_object($value)) {
        return '[complex value]';
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return (string) $value;
}
