<?php

declare(strict_types=1);

namespace Differ\Differ;

use InvalidArgumentException;
use RuntimeException;

use function Differ\Builder\buildDiff;
use function Differ\Formatter\stylish;
use function Differ\Parser\parse;

/**
 * @param string $filepath1
 * @param string $filepath2
 * @return string
 */
function genDiff(string $filepath1, string $filepath2): string
{
    $data1 = getData($filepath1);
    $data2 = getData($filepath2);

    $diff = buildDiff($data1, $data2);
//    print_r($diff);exit;
    $res = stylish($diff);
//    print_r($res);exit;
    return $res;
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
