<?php

declare(strict_types=1);

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testFirst(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/diff.json');

        $this->assertEquals($expected, genDiff($file1, $file2));
    }
}
