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
            $formatted = format($node['children'], "{$property}.");
        } elseif ($node['type'] === 'deleted') {
            $formatted = "Property '{$property}' was removed";
        } elseif ($node['type'] === 'changed') {
            $oldValue = stringify($node['oldValue']);
            $newValue = stringify($node['newValue']);
            $formatted = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
        } elseif ($node['type'] === 'added') {
            $newValue = stringify($node['newValue']);
            $formatted = "Property '{$property}' was added with value: {$newValue}";
        } else {
            $formatted = '';
        }

        return $formatted;
    }, $ast);

    return implode("\n", array_filter($formatted));
}

function stringify(mixed $value): string
{
    if (is_bool($value)) {
        $res = $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        $res = 'null';
    } elseif (is_object($value)) {
        $res = '[complex value]';
    } elseif (is_string($value)) {
        $res = "'$value'";
    } else {
        $res = (string) $value;
    }

    return $res;
}
