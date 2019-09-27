<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\WordwrapLogic;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class WordwrapLogicTest
 */
class WordwrapLogicTest extends UnitTestCase
{

    /** @var WordwrapLogic */
    private $wordWrapLogic;

    protected function setUp()
    {
        parent::setUp();
        $this->wordWrapLogic = new WordwrapLogic();
    }

    /**
     * Provides data for testWordWrapWithNonAscii
     *
     * @return array
     */
    public function nonAsciiProvider(): array
    {
        return [
            ["HÖ\nHÖ", "HÖ HÖ", 2],
            ["HÖ\na\nHÖ\na", "HÖa HÖa", 2, "\n", true],
            ["HÖa\na\nHÖa\na", "HÖaa HÖaa", 3, "\n", true],
            ["HÖa\nHÖa", "HÖa HÖa", 2]
        ];
    }

    /**
     * @param string $expected
     * @param string $string
     * @param int    $length
     * @param string $wrapper
     * @param bool   $cut
     *
     * @dataProvider nonAsciiProvider
     */
    public function testWordWrapWithNonAscii($expected, $string, $length = 80, $wrapper = "\n", $cut = false)
    {
        $this->assertEquals($expected, $this->wordWrapLogic->wordWrap($string, $length, $wrapper, $cut));
    }

    /**
     * Provides data for testWordWrapAscii
     *
     * @return array
     */
    public function asciiProvider(): array
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

    /**
     * @param string $expected
     * @param string $string
     * @param int    $length
     * @param string $wrapper
     * @param bool   $cut
     *
     * @dataProvider asciiProvider
     */
    public function testWordWrapAscii($expected, $string, $length = 80, $wrapper = "\n", $cut = false)
    {
        $this->assertEquals($expected, $this->wordWrapLogic->wordWrap($string, $length, $wrapper, $cut));
    }
}
