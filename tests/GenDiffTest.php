<?php

declare(strict_types=1);

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

/**
 * @covers ::\Differ\Differ\genDiff()
 * @covers ::\Differ\Differ\getData()
 * @covers ::\Differ\Parser\getParser()
 * @covers ::\Differ\Parser\parse()
 * @covers ::\Differ\Ast\build()
 * @covers ::\Differ\Ast\makeNode()
 * @covers ::\Differ\Ast\makeNestedNode()
 * @covers ::\Differ\Formatters\Plain\stringify()
 * @covers ::\Differ\Formatters\Plain\format()
 * @covers ::\Differ\Formatters\Stylish\stringify()
 * @covers ::\Differ\Formatters\Stylish\format()
 * @covers ::\Differ\Formatters\format()
 */
class GenDiffTest extends TestCase
{
    public function testPlainJson(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/plainDiff');

        $this->assertEquals($expected, genDiff($file1, $file2, 'plain'));
    }

    public function testPlainYml(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.yml';
        $file2 = __DIR__ . '/fixtures/file2.yml';

        $expected = file_get_contents(__DIR__ . '/fixtures/plainDiff');

        $this->assertEquals($expected, genDiff($file1, $file2, 'plain'));
    }

    public function testJson(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/diff');

        $this->assertEquals($expected, genDiff($file1, $file2));
    }

    public function testYml(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.yml';
        $file2 = __DIR__ . '/fixtures/file2.yml';

        $expected = file_get_contents(__DIR__ . '/fixtures/diff');

        $this->assertEquals($expected, genDiff($file1, $file2));
    }
}
