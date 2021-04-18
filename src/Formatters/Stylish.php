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

    $formatted = array_reduce($ast, function (array $acc, array $node) use ($indent, $depth): array {
        if ($node['type'] === 'nested') {
            $newAcc = [...$acc, "{$indent}    {$node['key']}: " . format($node['children'], 1 + $depth)];
        } elseif ($node['type'] === 'deleted') {
            $oldValue = stringify($node['oldValue'], $depth);
            $newAcc = [...$acc, "{$indent}  - {$node['key']}: {$oldValue}"];
        } elseif ($node['type'] === 'unchanged') {
            $oldValue = stringify($node['oldValue'], $depth);
            $newAcc = [...$acc, "{$indent}    {$node['key']}: {$oldValue}"];
        } elseif ($node['type'] === 'changed') {
            $oldValue = stringify($node['oldValue'], $depth);
            $newValue = stringify($node['newValue'], $depth);
            $newAcc = [...$acc, "{$indent}  - $node[key]: $oldValue", "{$indent}  + $node[key]: $newValue"];
        } elseif ($node['type'] === 'added') {
            $newValue = stringify($node['newValue'], $depth);
            $newAcc = [...$acc, "{$indent}  + $node[key]: $newValue"];
        } else {
            $newAcc = $acc;
        }

        return $newAcc;
    }, []);

    return "{\n" . implode("\n", $formatted) . "\n$indent}";
}

function stringify(mixed $value, int $depth = 1): string
{
    if (is_bool($value)) {
        $res = $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        $res = 'null';
    } elseif (is_object($value)) {
        $keys = array_keys(get_object_vars($value));
        $indent = str_repeat(' ', 4 * $depth);

        $formatted = array_map(function (string $key) use ($indent, $value, $depth): string {
            $formattedValue = stringify($value->$key, 1 + $depth);
            return "{$indent}    {$key}: {$formattedValue}";
        }, $keys);
        $formattedString = implode("\n", $formatted);
        $res = "{\n$formattedString\n$indent}";
    } else {
        $res = (string) $value;
    }

    return $res;
}
