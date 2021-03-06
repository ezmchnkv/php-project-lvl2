<?php

declare(strict_types=1);

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public function dataProvider(): array
    {
        return [
            ['file1.json', 'file2.json', 'stylish'],
            ['file1.json', 'file2.json', 'plain'],
            ['file1.json', 'file2.json', 'json'],
            ['file1.yml', 'file2.yml', 'stylish'],
            ['file1.yml', 'file2.yml', 'plain'],
            ['file1.yml', 'file2.yml', 'json'],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $file1
     * @param string $file2
     * @param string $format
     */
    public function testgetDiff(string $file1, string $file2, string $format): void
    {
        $file1 = __DIR__ . "/fixtures/$file1";
        $file2 = __DIR__ . "/fixtures/$file2";

        $expected = file_get_contents(__DIR__ . "/fixtures/{$format}Diff");

        $this->assertEquals($expected, genDiff($file1, $file2, $format));
    }
}
