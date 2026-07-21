<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition\EnvironmentTreeBuilderFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class EnvironmentTreeBuilderFactoryTest extends TestCase
{
    #[DataProvider('supportedSettingValueProvider')]
    public function testAcceptsSupportedSettingValues(mixed $value): void
    {
        $configuration = [
            'themeSettings' => [
                'testSetting' => ['value' => $value],
            ],
        ];

        $processedConfiguration = (new Processor())->process(
            (new EnvironmentTreeBuilderFactory())->create(),
            [$configuration]
        );

        $this->assertSame($value, $processedConfiguration['themeSettings']['testSetting']['value']);
    }

    public static function supportedSettingValueProvider(): array
    {
        return [
            'string' => ['value'],
            'empty string' => [''],
            'boolean' => [false],
            'integer' => [0],
            'array' => [['first', 'second']],
        ];
    }

    #[DataProvider('invalidConfigurationProvider')]
    public function testRejectsInvalidConfiguration(array $configuration): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->process(
            (new EnvironmentTreeBuilderFactory())->create(),
            [$configuration]
        );
    }

    public static function invalidConfigurationProvider(): array
    {
        return [
            'missing value' => [
                [
                    'themeSettings' => [
                        'testSetting' => [],
                    ],
                ],
            ],
            'null value' => [
                [
                    'themeSettings' => [
                        'testSetting' => ['value' => null],
                    ],
                ],
            ],
            'unsupported setting property' => [
                [
                    'themeSettings' => [
                        'testSetting' => [
                            'value' => 'value',
                            'type' => 'str',
                        ],
                    ],
                ],
            ],
            'unsupported root property' => [
                [
                    'source' => 'Application/views/testTheme',
                ],
            ],
        ];
    }
}
