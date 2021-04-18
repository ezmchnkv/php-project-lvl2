<?php

declare(strict_types=1);

namespace Differ\Formatters\Stylish;

/**
 * @param array<int, array> $ast
 * @param int $depth
 * @return string
 */
function format(array $ast, int $depth = 1): string
{
    $indent = str_repeat(' ', 4 * ($depth - 1));

    $formatted = [];
    foreach ($ast as $node) {
        if ($node['type'] === 'nested') {
            $formatted[] = "{$indent}    $node[key]: " . format($node['children'], 1 + $depth);
        }

        if ($node['type'] === 'deleted') {
            $oldValue = stringify($node['oldValue'], $depth);
            $formatted[] = "{$indent}  - $node[key]: $oldValue";
        }

        if ($node['type'] === 'unchanged') {
            $oldValue = stringify($node['oldValue'], $depth);
            $formatted[] = "{$indent}    $node[key]: $oldValue";
        }

        if ($node['type'] === 'changed') {
            $oldValue = stringify($node['oldValue'], $depth);
            $newValue = stringify($node['newValue'], $depth);
            $formatted[] = "{$indent}  - $node[key]: $oldValue";
            $formatted[] = "{$indent}  + $node[key]: $newValue";
        }

        if ($node['type'] === 'added') {
            $newValue = stringify($node['newValue'], $depth);
            $formatted[] = "{$indent}  + $node[key]: $newValue";
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
