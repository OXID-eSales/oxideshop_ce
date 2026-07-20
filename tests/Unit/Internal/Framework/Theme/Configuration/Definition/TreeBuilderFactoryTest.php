<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition\TreeBuilderFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;

final class TreeBuilderFactoryTest extends TestCase
{
    #[DataProvider('invalidTypedValueProvider')]
    public function testRejectsInvalidTypedValues(array $configuration): void
    {
        $this->expectException(InvalidTypeException::class);

        (new Processor())->process((new TreeBuilderFactory())->create(), [$configuration]);
    }

    public static function invalidTypedValueProvider(): array
    {
        return [
            'activated must be boolean' => [
                [
                    'source' => 'Application/views/testTheme',
                    'activated' => 'true',
                ],
            ],
            'position must be integer' => [
                [
                    'source' => 'Application/views/testTheme',
                    'themeSettings' => [
                        'testSetting' => ['position' => '1'],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('invalidSourceProvider')]
    public function testRequiresNonEmptySource(array $configuration): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->process((new TreeBuilderFactory())->create(), [$configuration]);
    }

    public static function invalidSourceProvider(): array
    {
        return [
            'missing source' => [[]],
            'empty source' => [['source' => '']],
        ];
    }
}
