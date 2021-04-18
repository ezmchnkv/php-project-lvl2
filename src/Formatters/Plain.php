<?php

declare(strict_types=1);

namespace Differ\Formatters\Plain;

/**
 * @param array<int, mixed> $ast
 * @param string $parent
 * @return string
 */
function format(array $ast, string $parent = ''): string
{
    $formatted = array_map(function (array $node) use ($parent): string {
        $property = "{$parent}{$node['key']}";

        if ($node['type'] === 'nested') {
            return format($node['children'], "{$property}.");
        }

        if ($node['type'] === 'deleted') {
            return "Property '{$property}' was removed";
        }

        if ($node['type'] === 'changed') {
            $oldValue = stringify($node['oldValue']);
            $newValue = stringify($node['newValue']);
            return "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
        }

        if ($node['type'] === 'added') {
            $newValue = stringify($node['newValue']);
            return "Property '{$property}' was added with value: {$newValue}";
        }

        return '';
    }, $ast);

    return implode("\n", array_filter($formatted));
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
