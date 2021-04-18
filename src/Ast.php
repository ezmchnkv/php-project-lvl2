<?php

declare(strict_types=1);

namespace Differ\Ast;

/**
 * @param object $data1
 * @param object $data2
 * @return array<int, array>
 */
function build(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $keys = array_values(array_unique(array_merge($keys1, $keys2)));
    usort($keys, fn($a, $b) => $a <=> $b);

    $ast = [];
    foreach ($keys as $key) {
        if (!property_exists($data1, $key)) {
            $ast[] = makeNode($key, 'added', null, $data2->$key);
            continue;
        }

        if (!property_exists($data2, $key)) {
            $ast[] = makeNode($key, 'deleted', $data1->$key);
            continue;
        }

        if (is_object($data1->$key) && is_object($data2->$key)) {
            $ast[] = makeNestedNode($key, build($data1->$key, $data2->$key));
            continue;
        }

        if ($data1->$key !== $data2->$key) {
            $ast[] = makeNode($key, 'changed', $data1->$key, $data2->$key);
            continue;
        }
        $ast[] = makeNode($key, 'unchanged', $data1->$key, $data2->$key);
    }

    return $ast;
}

/**
 * @param string $key
 * @param string $type
 * @param mixed|null $oldValue
 * @param mixed|null $newValue
 * @return array<string, mixed>
 */
function makeNode(string $key, string $type, mixed $oldValue = null, mixed $newValue = null): array
{
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
    ];
}

/**
 * @param string $key
 * @param array<int, array> $children
 * @return array<string, mixed>
 */
function makeNestedNode(string $key, array $children): array
{
    return [
        'key' => $key,
        'type' => 'nested',
        'children' => $children,
    ];
}
