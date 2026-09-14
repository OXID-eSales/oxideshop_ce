<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\MetaData\Definition;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Definition\MetaDataTreeBuilderFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;

final class MetaDataTreeBuilderFactoryTest extends TestCase
{
    public function testAppliesDefaultsForOptionalFields(): void
    {
        $processed = $this->process(['id' => 'testTheme']);

        $this->assertSame(
            [
                'id' => 'testTheme',
                'version' => '',
                'title' => '',
                'description' => '',
                'thumbnail' => '',
                'author' => '',
                'parentTheme' => '',
                'parentVersions' => [],
            ],
            $processed
        );
    }

    public function testKeepsQuotedVersions(): void
    {
        $processed = $this->process(['id' => 'child', 'version' => '1.0', 'parentVersions' => ['1.0', '1.1']]);

        $this->assertSame('1.0', $processed['version']);
        $this->assertSame(['1.0', '1.1'], $processed['parentVersions']);
    }

    #[DataProvider('invalidTypedValueProvider')]
    public function testRejectsInvalidTypedValues(array $metaData): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->process($metaData);
    }

    public static function invalidTypedValueProvider(): array
    {
        return [
            'numeric version' => [['id' => 'testTheme', 'version' => 1.0]],
            'numeric parent version' => [['id' => 'testTheme', 'parentVersions' => [1.0]]],
            'boolean parent theme' => [['id' => 'testTheme', 'parentTheme' => false]],
        ];
    }

    #[DataProvider('invalidIdProvider')]
    public function testRequiresNonEmptyId(array $metaData): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process($metaData);
    }

    public static function invalidIdProvider(): array
    {
        return [
            'missing id' => [[]],
            'empty id' => [['id' => '']],
        ];
    }

    private function process(array $metaData): array
    {
        return (new Processor())->process((new MetaDataTreeBuilderFactory())->create(), [$metaData]);
    }
}
