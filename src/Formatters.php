<?php

declare(strict_types=1);

namespace Differ\Formatters;

use RuntimeException;

/**
 * @param string $format
 * @param array<int, mixed> $ast
 * @return string
 */
function format(string $format, array $ast): string
{
    return match ($format) {
        'json' => Json\format($ast),
        'plain' => Plain\format($ast),
        'stylish' => Stylish\format($ast),
    default => throw new RuntimeException("The format $format unsupported"),
    };
}
