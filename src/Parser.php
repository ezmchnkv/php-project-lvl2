<?php

declare(strict_types=1);

namespace Differ\Parser;

use RuntimeException;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * @param string $data
 * @param string $format
 * @return stdClass
 */
function parse(string $data, string $format): stdClass
{
    $parser = getParser($format);
    return $parser($data);
}

/**
 * @param string $format
 * @return callable
 */
function getParser(string $format): callable
{
    return match ($format) {
        'json' => static fn(string $data): stdClass => json_decode($data, false, 512, JSON_THROW_ON_ERROR),
        'yml' => static fn(string $data): stdClass => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
    default => throw new RuntimeException("The extension $format not supported"),
    };
}
