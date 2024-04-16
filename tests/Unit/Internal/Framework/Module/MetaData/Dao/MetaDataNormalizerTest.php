<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Dao;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataNormalizer;
use PHPUnit\Framework\TestCase;

final class MetaDataNormalizerTest extends TestCase
{
    public function testNormalizeMetaData(): void
    {
        $metaData =
            [
                'id'          => 'value1',
                'settings'    => [
                    [
                        'constraints' => 'value1',
                    ],
                ],
            ];
        $expectedNormalizedData = [
            'id'          => 'value1',
            'settings'    => [
                [
                    'constraints' => ['value1'],
                ],
            ],
        ];

        $metaDataNormalizer = new MetaDataNormalizer();
        $normalizedData = $metaDataNormalizer->normalizeData($metaData);

        $this->assertEquals($expectedNormalizedData, $normalizedData);
    }

    public function testNormalizerConvertsModuleSettingConstraintsToArray(): void
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

    #[DataProvider('multiLanguageFieldDataProvider')]
    public function testNormalizerConvertsMultiLanguageFieldToArrayWithDefaultLanguageIfItIsString(string $fieldName, string $value): void
    {
        $metadata = [
            $fieldName => $value,
        ];

        $this->assertSame(
            [
                $fieldName => [
                    'en' => $value,
                ]
            ],
            (new MetaDataNormalizer())->normalizeData($metadata)
        );
    }

    #[DataProvider('multiLanguageFieldDataProvider')]
    public function testNormalizerConvertsMultiLanguageFieldToArrayWithCustomLanguageIfItIsStringAndLangOptionIsSet(string $fieldName, string $value): void
    {
        $metadata = [
            $fieldName => $value,
            'lang'  => 'esperanto',
        ];

        $this->assertSame(
            [
                $fieldName => [
                    'esperanto' => $value,
                ],
                'lang'  => 'esperanto',
            ],
            (new MetaDataNormalizer())->normalizeData($metadata)
        );
    }

    public static function multiLanguageFieldDataProvider(): array
    {
        return [
            ['title', 'some value'],
            ['description', 'some value'],
        ];
    }
}
