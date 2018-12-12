<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper\MetaDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorInterface;
use PHPUnit\Framework\TestCase;

class MetaDataMapperTest extends TestCase
{
    private $metaDataValidatorStub;

    protected function setUp()
    {
        parent::setUp();

        $this->metaDataValidatorStub = $this->getMockBuilder(MetaDataValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metaDataValidatorStub->method('validate');
    }


    /**
     * @dataProvider dataProviderInvalidData
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $invalidData
     */
    public function testFromDataWillThrowExceptionOnInvalidParameterFormat($invalidData)
    {
        $metaDataDataMapper = new MetaDataMapper($this->metaDataValidatorStub);
        $metaDataDataMapper->fromData($invalidData);
    }

    public function dataProviderInvalidData(): array
    {
        return [
            'all mandatory keys are missing'    => [[]],
            'key metaDataVersion is missing'    => [[MetaDataProvider::METADATA_MODULE_DATA => '']],
            'key moduleData version is missing' => [[MetaDataProvider::METADATA_METADATA_VERSION => '']],
        ];
    }
}
