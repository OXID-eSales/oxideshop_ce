<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\WordwrapLogic;
use PHPUnit\Framework\TestCase;

final class WordwrapLogicTest extends TestCase
{
    private WordwrapLogic $wordWrapLogic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wordWrapLogic = new WordwrapLogic();
    }

    public static function nonAsciiProvider(): array
    {
        return [
            ["HÖ\nHÖ", "HÖ HÖ", 2],
            ["HÖ\na\nHÖ\na", "HÖa HÖa", 2, "\n", true],
            ["HÖa\na\nHÖa\na", "HÖaa HÖaa", 3, "\n", true],
            ["HÖa\nHÖa", "HÖa HÖa", 2]
        ];
    }

    #[DataProvider('nonAsciiProvider')]
    public function testWordWrapWithNonAscii(
        string $expected,
        string $string,
        int $length = 80,
        string $wrapper = "\n",
        bool $cut = false
    ): void {
        self::assertEquals($expected, $this->wordWrapLogic->wordWrap($string, $length, $wrapper, $cut));
    }

    public static function asciiProvider(): array
    {
        return [
            ["aaa\naaa", 'aaa aaa', 2],
            ["aa\na\naa\na", 'aaa aaa', 2, "\n", true],
            ["aaa\naaa a", 'aaa aaa a', 5],
            ["aaa\naaa", 'aaa aaa', 5, "\n", true],
            ["  \naaa\n  \naaa", '   aaa    aaa', 2],
            ["  \naa\na \n \naa\na", '   aaa    aaa', 2, "\n", true],
            ["  \naaa  \n aaa", '   aaa    aaa', 5],
            ["  \naaa  \n aaa", '   aaa    aaa', 5, "\n", true],
            [
                "Pellentesq\nue nisl\nnon\ncondimentu\nm cursus.\n \nconsectetu\nr a diam\nsit.\n finibus\ndiam eu\nlibero\nlobortis.\neu   ex  \nsit",
                "Pellentesque nisl non condimentum cursus.\n  consectetur a diam sit.\n finibus diam eu libero lobortis.\neu   ex   sit",
                10,
                "\n",
                true
            ]
        ];
    }

    #[DataProvider('asciiProvider')]
    public function testWordWrapAscii(string $expected, string $string, int $length = 80, string $wrapper = "\n", bool $cut = false): void
    {
        self::assertEquals($expected, $this->wordWrapLogic->wordWrap($string, $length, $wrapper, $cut));
    }
}
