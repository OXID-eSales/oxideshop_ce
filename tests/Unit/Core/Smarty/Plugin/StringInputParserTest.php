<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty\Plugin;

use OxidEsales\EshopCommunity\Core\Smarty\Plugin\StringInputParser;
use PHPUnit\Framework\TestCase;

class StringInputParserTest extends TestCase
{
    /** @dataProvider arraysDataProvider */
    public function testParseArray(string $input, array $expected): void
    {
        $actual = (new StringInputParser())->parseArray($input);

        $this->assertSame($expected, $actual);
    }

    /** @dataProvider rangesDataProvider */
    public function testParseRange(string $input, array $expected): void
    {
        $actual = (new StringInputParser())->parseRange($input);

        $this->assertSame($expected, $actual);
    }

    public function arraysDataProvider(): array
    {
        return [
            [
                '[]',
                [],
            ],
            [
                'array()',
                [],
            ],
            [
                'array("abc", array())',
                [
                    'abc',
                    [],
                ]
            ],
            [
                'array(
                1,
                //comment
                /* Another comment array */
                []
                )',
                [
                    1,
                    [],
                ],
            ],
            [
                '[123]',
                [123],
            ],
            [
                'array("ab - c"=>123, [], array(1=>array("DEF\'")))',
                [
                    'ab - c' => 123,
                    [],
                    [1 => [
                        'DEF\'',
                        ],
                        ],
                    ],
            ],
            [
                "array('key_1_2_3' => ['array' => 'ARRAY', 'key' => '___', 'null' => null,'false' => false, 'true' => TRUE, 'ucFirstTrue' => True])",
                [
                    'key_1_2_3' => [
                        'array' => 'ARRAY',
                        'key' => '___',
                        'null' => null,
                        'false' => false,
                        'true' => true,
                        'ucFirstTrue' => true,
                    ]
                ],
            ],
            [
                '[
                    1, [],
                    2,
                    3,
                ]',
                [
                    1,
                    [],
                    2,
                    3,
                ],
            ],
        ];
    }

    public function rangesDataProvider(): array
    {
        return [
            [
                'range(1,5)',
                [1, 2, 3, 4, 5],
            ],
            [
                'RAnGE(50,   51)',
                [50, 51],
            ],
            [
                'range("A" , "C")',
                ['A', 'B', 'C'],
            ],
            [
                'range(1, 10, 3)',
                [1, 4, 7, 10],
            ],
            [
                'range(
                "A" ,
                 \'C\'
                 )',
                ['A', 'B', 'C'],
            ],
        ];
    }
}
