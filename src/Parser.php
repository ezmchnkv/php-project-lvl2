<?php

declare(strict_types=1);

namespace Differ\Parser;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @param string $format
 * @return callable
 */
function getParser(string $format): callable
{
    return match ($format) {
        'json' => static fn(string $data): object => json_decode($data, false, 512, JSON_THROW_ON_ERROR),
        'yaml', 'yml' => static fn(string $data): object => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new RuntimeException("The extension $format not supported"),
    };
}
