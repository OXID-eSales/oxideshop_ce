<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\Dao;

use PHPUnit\Framework\MockObject\MockObject;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\ModuleIdNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataNormalizer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataValidatorInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class MetaDataProviderTest extends TestCase
{
    use ContainerTrait;

    /** @var MockObject&\MetaDataNormalizer */
    private MockObject $metaDataNormalizerStub;

    /** @var MockObject&\BasicContextInterface */
    private MockObject $contextStub;

    /** @var MockObject&\MetaDataValidatorInterface */
    private MockObject $validatorStub;

    public function setUp(): void
    {
        parent::setUp();
        $this->metaDataNormalizerStub = $this->getMockBuilder(MetaDataNormalizer::class)->getMock();
        $this->metaDataNormalizerStub->method('normalizeData')->willReturnArgument(0);
        $this->contextStub = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $this->validatorStub = $this->getMockBuilder(MetaDataValidatorInterface::class)->getMock();
    }

    public function testGetDataThrowsExceptionOnNonExistingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $metaDataProvider = $this->createMetaDataProvider();

        $this->expectException(InvalidArgumentException::class);
        $metaDataProvider->getData('non existing file');
    }

    public function testGetDataThrowsExceptionOnDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $metaDataProvider = $this->createMetaDataProvider();
        $this->expectException(InvalidArgumentException::class);
        $metaDataProvider->getData(__DIR__);
    }

    #[DataProvider('missingMetaDataVariablesDataProvider')]
    public function testGetDataThrowsExceptionOnMissingMetaDataVariables(string $metaDataContent): void
    {
        $this->expectException(InvalidMetaDataException::class);
        $metaDataFilePath = $this->getPathToTemporaryFile();
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = $this->createMetaDataProvider();

        $this->expectException(InvalidMetaDataException::class);
        $metaDataProvider->getData($metaDataFilePath);
    }

    private function getPathToTemporaryFile(): string
    {
        $temporaryFileHandle = tmpfile();

        return stream_get_meta_data($temporaryFileHandle)['uri'];
    }

    public static function missingMetaDataVariablesDataProvider(): array
    {
        return [
            ['<?php '],
            ['<?php $aModule = [];'],
            ['<?php $sMetadataVersion = "2.0";'],
        ];
    }

    public function testGetDataProvidesConfiguredMetadataId(): void
    {
        $moduleId = 'test_module';
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = ["id" => "test_module"];
        ';

        $metaDataFilePath = $this->getPathToTemporaryFile();
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new RuntimeException('Could not write to ' . $metaDataFilePath);
        }
        $metaDataProvider = $this->createMetaDataProvider();
        $metaData = $metaDataProvider->getData($metaDataFilePath);

        $this->assertEquals(
            $moduleId,
            $metaData[MetaDataProvider::METADATA_MODULE_DATA][MetaDataProvider::METADATA_ID]
        );
    }

    public function testGetDataThrowsExceptionIfMetaDataIsNotConfigured(): void
    {
        $this->expectException(ModuleIdNotValidException::class);
        $metaDataFilePath = $this->getPathToTemporaryFile();
        $metaDataContent = '<?php
            $sMetadataVersion = "2.0";
            $aModule = [];
        ';
        if (false === file_put_contents($metaDataFilePath, $metaDataContent)) {
            throw new RuntimeException('Could not write to ' . $metaDataFilePath);
        }

        $metaDataProvider = new MetaDataProvider(
            $this->metaDataNormalizerStub,
            $this->contextStub,
            $this->get(MetaDataValidatorInterface::class),
            $this->get(MetaDataConverterInterface::class)
        );
        $metaDataProvider->getData($metaDataFilePath);
    }

    public function testGetDataConvertsBackwardsCompatibleClasses(): void
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
            throw new RuntimeException('Could not write to ' . $metaDataFilePath);
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

    private function createMetaDataProvider(): MetaDataProvider
    {
        return new MetaDataProvider(
            $this->metaDataNormalizerStub,
            $this->contextStub,
            $this->validatorStub,
            $this->get(MetaDataConverterInterface::class)
        );
    }
}
