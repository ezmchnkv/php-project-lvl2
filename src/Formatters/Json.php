<?php

declare(strict_types=1);

namespace Differ\Formatters\Json;

/**
 * @param array<int, mixed> $ast
 * @return string
 */
function format(array $ast): string
{
    return json_encode($ast, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
}
