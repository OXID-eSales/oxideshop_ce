<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\MetaDataDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\MetaDataNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MetaDataDataProviderTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\MetaData
 */
class MetaDataDataProviderTest extends TestCase
{
    /** @var EventDispatcherInterface */
    private $eventDispatcherStub;

    /** @var MetaDataNormalizer */
    private $metaDataNormalizerStub;

    /** @var ShopAdapterInterface */
    private $shopAdapterStub;

    protected function setUp()
    {
        parent::setUp();

        $this->eventDispatcherStub = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->metaDataNormalizerStub = $this->getMockBuilder(MetaDataNormalizer::class)->getMock();
        $this->metaDataNormalizerStub->method('normalizeData')->willReturnArgument(0);
        $this->shopAdapterStub = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDataThrowsExceptionOnNonExistingFile()
    {
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaDataProvider->getData('non existing file');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDataThrowsExceptionOnDirectory()
    {
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaDataProvider->getData(__DIR__);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\InvalidMetaDataException
     *
     * @dataProvider providerMissingMetaDataVariables
     *
     * @param string $metaDataContent
     *
     * @throws \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\InvalidMetaDataException
     */
    public function testGetDataThrowsExceptionOnMissingMetaDataVariables(string $metaDataContent)
    {
        $metaDataFilePath = $this->getPathToTemporaryFile();
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaDataProvider->getData($metaDataFilePath);
    }

    /**
     * @return array
     */
    public function providerMissingMetaDataVariables(): array
    {
        return [
            ['<?php '],
            ['<?php $aModule = [];'],
            ['<?php $sMetadataVersion = "2.0";'],
        ];
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\InvalidMetaDataException
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
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals($moduleId, $metaData['moduleData']['id']);
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\InvalidMetaDataException
     */
    public function testGetDataProvidesDirectoryNameForMetadataIdIfMetaDataIsNotConfigured()
    {
        $metaDataFilePath = $this->getPathToTemporaryFile();
        $metaDataDir = trim(dirname($metaDataFilePath), DIRECTORY_SEPARATOR);
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = [];
        ';
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals($metaDataDir, $metaData['moduleData']['id']);
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\InvalidMetaDataException
     */
    public function testGetDataDispatchesEventIfMetaDataIsNotConfigured()
    {
        $metaDataFilePath = $this->getPathToTemporaryFile();
        $metaDataDir = trim(dirname($metaDataFilePath), DIRECTORY_SEPARATOR);
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = [];
        ';
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new \RuntimeException('Could not write to ' . $metaDataFilePath);
        }

        $this->eventDispatcherStub->expects($this->atLeastOnce())->method('dispatch');
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals($metaDataDir, $metaData['moduleData']['id']);
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
        $this->shopAdapterStub->method('getBackwardsCompatibilityClassMap')->willReturn(
            [
                "oxarticle" => "EShopNamespace\\ArticleClass",
                "oxorder"   => "EShopNamespace\\OrderClass",
            ]
        );
        $metaDataProvider = new MetaDataDataProvider($this->eventDispatcherStub, $this->metaDataNormalizerStub, $this->shopAdapterStub);
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals(
            [
                "EShopNamespace\\ArticleClass" => "VendorNamespace\\VendorClass1",
                "EShopNamespace\\OrderClass"   => "VendorNamespace\\VendorClass2",
                "EShopNamespace\\UserClass" => "VendorNamespace\\VendorClass3",
            ],
            $metaData['moduleData']['extend']
        );
    }

    /**
     * @return string
     */
    private function getPathToTemporaryFile(): string
    {
        $temporaryFileHandle = tmpfile();

        return stream_get_meta_data($temporaryFileHandle)['uri'];
    }
}
