<?php

declare(strict_types=1);

namespace Differ\Builder;

use JetBrains\PhpStorm\ArrayShape;

/**
 * @param object $data1
 * @param object $data2
 * @return array<int, array>
 */
function buildDiff(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $keys = array_values(array_unique(array_merge($keys1, $keys2)));
    usort($keys, fn($a, $b) => $a <=> $b);

    $diff = [];
    foreach ($keys as $key) {
        if (!property_exists($data1, $key)) {
            $diff[] = makeNode($key, 'added', null, $data2->$key);
            continue;
        }

        if (!property_exists($data2, $key)) {
            $diff[] = makeNode($key, 'deleted', $data1->$key);

            continue;
        }

        if (is_object($data1->$key) && is_object($data2->$key)) {
            $diff[] = makeNode($key, 'nested', null, null, buildDiff($data1->$key, $data2->$key));
            continue;
        }

        if ($data1->$key !== $data2->$key) {
            $diff[] = makeNode($key, 'changed', $data1->$key, $data2->$key);
            continue;
        }
        $diff[] = makeNode($key, 'unchanged', $data1->$key, $data2->$key);
    }

    return $diff;
}

/**
 * @param string $key
 * @param string $type
 * @param mixed|null $oldValue
 * @param mixed|null $newValue
 * @param array<int, array>|null $children
 * @return array<string, mixed>
 */
function makeNode(
    string $key,
    string $type,
    mixed $oldValue = null,
    mixed $newValue = null,
    array $children = null
): array {
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}
