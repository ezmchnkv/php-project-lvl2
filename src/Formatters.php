<?php

declare(strict_types=1);

namespace Differ\Formatters;

use RuntimeException;

/**
 * @param string $format
 * @param array<int, mixed> $diff
 * @return string
 */
function format(string $format, array $diff): string
{
    return match ($format) {
        'plain' => Plain\format($diff),
        'stylish' => Stylish\format($diff),
    default => throw new RuntimeException("The format $format unsupported"),
    };
}
