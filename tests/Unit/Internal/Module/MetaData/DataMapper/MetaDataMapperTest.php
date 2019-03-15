<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper\MetaDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorInterface;
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
            MetaDataProvider::METADATA_CHECKSUM         => '',
            MetaDataProvider::METADATA_MODULE_DATA      => [
                MetaDataProvider::METADATA_ID       => 'id',
                MetaDataProvider::METADATA_FILES    => [
                    'name' => 'path',
                ]
            ]
        ];
        $metaDataDataMapper = new MetaDataMapper($this->metaDataValidatorStub);
        $moduleConfiguration = $metaDataDataMapper->fromData($metadata);

        $this->assertSame(
            [
                'name' => 'path',
            ],
            $moduleConfiguration->getSetting(ModuleSetting::CLASSES_WITHOUT_NAMESPACE)->getValue()
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->metaDataValidatorStub = $this->getMockBuilder(MetaDataValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metaDataValidatorStub->method('validate');
    }
}
