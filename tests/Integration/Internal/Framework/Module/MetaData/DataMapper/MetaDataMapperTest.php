<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Test\Integration\Internal\Framework\Module\MetaData\DataMapper;

use PHPUnit\Framework\Attributes\DataProvider;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class MetaDataMapperTest extends TestCase
{
    use ContainerTrait;

    #[DataProvider('missingMetaDataKeysDataProvider')]
    public function testFromDataWillThrowExceptionOnInvalidParameterFormat(array $invalidData): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->get(MetaDataToModuleConfigurationDataMapperInterface::class)->fromData($invalidData);
    }

    public static function missingMetaDataKeysDataProvider(): array
    {
        return [
            'all mandatory keys are missing'    => [[]],
            'key metaDataVersion is missing'    => [[MetaDataProvider::METADATA_MODULE_DATA => '']],
            'key moduleData version is missing' => [[MetaDataProvider::METADATA_METADATA_VERSION => '']],
        ];
    }

    public function testSettingPositionIsConvertedToInt(): void
    {
        $moduleConfiguration = $this->get(MetaDataToModuleConfigurationDataMapperInterface::class)->fromData(
            [
                'metaDataVersion' => '2.1',
                'metaDataFilePath' => 'some-path',
                'moduleData' => [
                    'id' => 'some',
                    'settings' => [
                        [
                            'name'  => 'setting',
                            'type'  => 'bool',
                            'value' => 'true',
                            'position' => '2'
                        ],
                    ]
                ]
            ]
        );

        $this->assertSame(
            2,
            $moduleConfiguration->getModuleSetting('setting')->getPositionInGroup()
        );
    }
}
