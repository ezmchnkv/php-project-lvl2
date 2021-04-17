<?php

declare(strict_types=1);

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

/**
 * @covers ::\Differ\Differ\genDiff()
 * @covers ::\Differ\Differ\formatValue()
 * @covers ::\Differ\Differ\format()
 * @covers ::\Differ\Differ\getData()
 * @covers ::\Differ\Differ\makeNode()
 * @covers ::\Differ\Parser\getParser()
 * @covers ::\Differ\Parser\parse()
 */
class GenDiffTest extends TestCase
{
    public function testPlainJson(): void
    {
        $file1 = __DIR__ . '/fixtures/plain/json/file1.json';
        $file2 = __DIR__ . '/fixtures/plain/json/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/plain/json/diff.json');

        $this->assertEquals($expected, genDiff($file1, $file2));
    }

    public function testPlainYml(): void
    {
        $file1 = __DIR__ . '/fixtures/plain/yml/file1.yml';
        $file2 = __DIR__ . '/fixtures/plain/yml/file2.yml';

        $expected = file_get_contents(__DIR__ . '/fixtures/plain/yml/diff.yml');

        $this->assertEquals($expected, genDiff($file1, $file2));
    }
}
