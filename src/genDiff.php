<?php

declare(strict_types=1);

namespace Differ\Differ;

use InvalidArgumentException;
use RuntimeException;

use function Differ\Ast\build;
use function Differ\Parser\parse;
use function Differ\Formatters\format;

/**
 * @param string $filepath1
 * @param string $filepath2
 * @param string $format
 * @return string
 */
function genDiff(string $filepath1, string $filepath2, string $format = 'stylish'): string
{
    $data1 = getData($filepath1);
    $data2 = getData($filepath2);

    $diff = build($data1, $data2);
    return format($format, $diff);
}

/**
 * @param string $filepath
 * @return object
 */
function getData(string $filepath): object
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
