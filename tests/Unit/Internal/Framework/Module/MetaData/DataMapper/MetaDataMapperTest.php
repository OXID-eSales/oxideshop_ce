<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper\MetaDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidatorInterface;
use PHPUnit\Framework\TestCase;

class MetaDataMapperTest extends TestCase
{
    private $metaDataValidatorStub;

    /**
     * @dataProvider missingMetaDataKeysDataProvider
     *
     * @expectedException \InvalidArgumentException
     *
     * @param array $invalidData
     */
    public function testFromDataWillThrowExceptionOnInvalidParameterFormat(array $invalidData)
    {
        $metaDataDataMapper = new MetaDataMapper($this->metaDataValidatorStub);
        $metaDataDataMapper->fromData($invalidData);
    }

    public function missingMetaDataKeysDataProvider(): array
    {
        return [
            'all mandatory keys are missing'    => [[]],
            'key metaDataVersion is missing'    => [[MetaDataProvider::METADATA_MODULE_DATA => '']],
            'key moduleData version is missing' => [[MetaDataProvider::METADATA_METADATA_VERSION => '']],
        ];
    }

    public function testMetadataFilesMapping()
    {
        $metadata = [
            MetaDataProvider::METADATA_METADATA_VERSION => '0',
            MetaDataProvider::METADATA_FILEPATH         => '',
            MetaDataProvider::METADATA_MODULE_DATA      => [
                MetaDataProvider::METADATA_ID       => 'id',
                MetaDataProvider::METADATA_FILES    => [
                    'name' => 'path',
                ]
            ]
        ];
        $metaDataDataMapper = new MetaDataMapper($this->metaDataValidatorStub);
        $moduleConfiguration = $metaDataDataMapper->fromData($metadata);

        $classes = [];

        foreach ($moduleConfiguration->getClassesWithoutNamespace() as $class) {
            $classes[$class->getShopClass()] = $class->getModuleClass();
        }

        $this->assertSame(
            [
                'name' => 'path',
            ],
            $classes
        );
    }

    public function testSettingPositionIsConvertedToInt(): void
    {
        $metaDataDataMapper = new MetaDataMapper($this->metaDataValidatorStub);
        $moduleConfiguration = $metaDataDataMapper->fromData(
            [
                'metaDataVersion' => '1.1',
                'metaDataFilePath' => 'sdasd',
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

    protected function setUp()
    {
        parent::setUp();

        $this->metaDataValidatorStub = $this->getMockBuilder(MetaDataSchemaValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metaDataValidatorStub->method('validate');
    }
}
