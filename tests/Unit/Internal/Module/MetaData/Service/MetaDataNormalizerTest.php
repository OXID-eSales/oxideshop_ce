<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData\Service;

use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class MetaDataNormalizerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData\Service
 */
class MetaDataNormalizerTest extends TestCase
{
    public function testNormalizeMetaData()
    {
        $metaData =
            [
                'ID'          => 'value1',
                'DESCRIPTION' => [
                    'DE' => 'value1',
                    'EN' => 'value2',
                ],
                'EXTEND'      => [
                    '\ShopNameSpace\ShopClass1' => '\ModuleNameSpace\ModuleClass1',
                    '\ShopNameSpace\ShopClass2' => '\ModuleNameSpace\ModuleClass2'
                ],
                'CONTROLLERS' => [
                    'CamelCaseKey_1' => '\ModuleNameSpace\ModuleClass1',
                    'CamelCaseKey_2' => '\ModuleNameSpace\ModuleClass2'
                ],
                'SETTINGS'    => [
                    [
                        'GROUP'       => 'value1',
                        'NAME'        => 'value1',
                        'TYPE'        => 'value1',
                        'VALUE'       => 'value1',
                        'CONSTRAINTS' => 'value1',
                        'POSITION'    => 1
                    ],
                    [
                        'GROUP'       => 'value2',
                        'NAME'        => 'value2',
                        'TYPE'        => 'value2',
                        'VALUE'       => 'value2',
                        'CONSTRAINTS' => 'value2',
                        'POSITION'    => 2
                    ],
                ],
                'BLOCKS'      => [
                    [
                        'TEMPLATE' => 'value1',
                        'BLOCK'    => 'value1',
                        'FILE'     => 'value1',
                        'POSITION' => 'value1'
                    ],
                    [
                        'TEMPLATE' => 'value2',
                        'BLOCK'    => 'value2',
                        'FILE'     => 'value2',
                        'POSITION' => 'value2'
                    ],
                ],
                'FILES' => [
                    'className' => 'dir/filename.php',
                ],
            ];
        $expectedNormalizedData = [
            'id'          => 'value1',
            'description' => [
                'de' => 'value1',
                'en' => 'value2',
            ],
            'extend'      => [
                '\ShopNameSpace\ShopClass1' => '\ModuleNameSpace\ModuleClass1',
                '\ShopNameSpace\ShopClass2' => '\ModuleNameSpace\ModuleClass2'
            ],
            'controllers' => [
                'CamelCaseKey_1' => '\ModuleNameSpace\ModuleClass1',
                'CamelCaseKey_2' => '\ModuleNameSpace\ModuleClass2'
            ],
            'settings'    => [
                [
                    'group'       => 'value1',
                    'name'        => 'value1',
                    'type'        => 'value1',
                    'value'       => 'value1',
                    'constraints' => ['value1'],
                    'position'    => 1
                ],
                [
                    'group'       => 'value2',
                    'name'        => 'value2',
                    'type'        => 'value2',
                    'value'       => 'value2',
                    'constraints' => ['value2'],
                    'position'    => 2
                ],
            ],
            'blocks'      => [
                [
                    'template' => 'value1',
                    'block'    => 'value1',
                    'file'     => 'value1',
                    'position' => 'value1'
                ],
                [
                    'template' => 'value2',
                    'block'    => 'value2',
                    'file'     => 'value2',
                    'position' => 'value2'
                ],
            ],
            'files' => [
                'classname' => 'dir/filename.php',
            ],
        ];

        $metaDataNormalizer = new MetaDataNormalizer();
        $normalizedData = $metaDataNormalizer->normalizeData($metaData);

        $this->assertEquals($expectedNormalizedData, $normalizedData);
    }

    public function testNormalizerConvertsModuleSettingConstraintsToArray()
    {
        $metadata = [
            'settings' => [
                ['constraints' => '1|2|3'],
                ['constraints' => 'le|la|les'],
            ]
        ];

        $this->assertSame(
            [
                'settings' => [
                    ['constraints' => ['1', '2', '3']],
                    ['constraints' => ['le', 'la', 'les']],
                ]
            ],
            (new MetaDataNormalizer())->normalizeData($metadata)
        );
    }
}
