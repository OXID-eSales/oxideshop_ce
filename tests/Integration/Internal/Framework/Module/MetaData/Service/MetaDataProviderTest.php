<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\ModuleIdNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataNormalizer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataValidatorInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class MetaDataProviderTest extends TestCase
{
    use ContainerTrait;

    /** @var MetaDataNormalizer */
    private $metaDataNormalizerStub;

    /** @var BasicContextInterface */
    private $contextStub;

    /** @var MetaDataValidatorInterface */
    private $validatorStub;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDataThrowsExceptionOnNonExistingFile()
    {
        $metaDataProvider = $this->createMetaDataProvider();
        $metaDataProvider->getData('non existing file');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDataThrowsExceptionOnDirectory()
    {
        $metaDataProvider = $this->createMetaDataProvider();
        $metaDataProvider->getData(__DIR__);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException
     *
     * @dataProvider missingMetaDataVariablesDataProvider
     *
     * @param string $metaDataContent
     *
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException
     */
    public function testGetDataThrowsExceptionOnMissingMetaDataVariables(string $metaDataContent)
    {
        $metaDataFilePath = $this->getPathToTemporaryFile();
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = $this->createMetaDataProvider();
        $metaDataProvider->getData($metaDataFilePath);
    }

    /**
     * @return string
     */
    private function getPathToTemporaryFile(): string
    {
        $temporaryFileHandle = tmpfile();

        return stream_get_meta_data($temporaryFileHandle)['uri'];
    }

    /**
     * @return array
     */
    public function missingMetaDataVariablesDataProvider(): array
    {
        return [
            ['<?php '],
            ['<?php $aModule = [];'],
            ['<?php $sMetadataVersion = "2.0";'],
        ];
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException
     */
    public function testGetDataProvidesConfiguredMetadataId()
    {
        $moduleId = 'test_module';
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = ["id" => "test_module"];
        ';

        $metaDataFilePath = $this->getPathToTemporaryFile();
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = $this->createMetaDataProvider();
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals(
            $moduleId,
            $metaData[MetaDataProvider::METADATA_MODULE_DATA][MetaDataProvider::METADATA_ID]
        );
    }

    public function testGetDataThrowsExceptionIfMetaDataIsNotConfigured()
    {
        $this->expectException(ModuleIdNotValidException::class);
        $metaDataFilePath = $this->getPathToTemporaryFile();
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = [];
        ';
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }

        $metaDataProvider = new MetaDataProvider(
            $this->metaDataNormalizerStub,
            $this->contextStub,
            $this->get(MetaDataValidatorInterface::class),
            $this->get(MetaDataConverterInterface::class)
        );
        $metaDataProvider->getData($metaDataFilePath);
    }

    public function testGetDataConvertsBackwardsCompatibleClasses()
    {
        $metaDataFilePath = $this->getPathToTemporaryFile();
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = [
                "id" => "MyModuleId",
                "extend" => [
                    "oxarticle"                 => \VendorNamespace\VendorClass1::class,
                    "OXORDER"                   => "VendorNamespace\\VendorClass2",
                    "EShopNamespace\\UserClass" => \VendorNamespace\VendorClass3::class,
                ]
            ];
        ';
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }

        $basicContext = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $basicContext->method('getBackwardsCompatibilityClassMap')->willReturn(
            [
                "oxarticle" => "EShopNamespace\\ArticleClass",
                "oxorder"   => "EShopNamespace\\OrderClass",
            ]
        );
        $metaDataProvider = new MetaDataProvider(
            $this->metaDataNormalizerStub,
            $basicContext,
            $this->validatorStub,
            $this->get(MetaDataConverterInterface::class)
        );
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals(
            [
                "EShopNamespace\\ArticleClass" => "VendorNamespace\\VendorClass1",
                "EShopNamespace\\OrderClass"   => "VendorNamespace\\VendorClass2",
                "EShopNamespace\\UserClass"    => "VendorNamespace\\VendorClass3",
            ],
            $metaData['moduleData']['extend']
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->metaDataNormalizerStub = $this->getMockBuilder(MetaDataNormalizer::class)->getMock();
        $this->metaDataNormalizerStub->method('normalizeData')->willReturnArgument(0);
        $this->contextStub = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $this->validatorStub = $this->getMockBuilder(MetaDataValidatorInterface::class)->getMock();
    }

    /**
     * @return MetaDataProvider
     */
    private function createMetaDataProvider(): MetaDataProvider
    {
        $metaDataProvider = new MetaDataProvider(
            $this->metaDataNormalizerStub,
            $this->contextStub,
            $this->validatorStub,
            $this->get(MetaDataConverterInterface::class)
        );
        return $metaDataProvider;
    }
}
