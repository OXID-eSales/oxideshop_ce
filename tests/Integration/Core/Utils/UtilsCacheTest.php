<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Utils;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class UtilsCacheTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testCacheResetShouldNotRemoveCacheFilesFromSubdirectories(): void
    {
        $context = ContainerFacade::get(ContextInterface::class);

        $cachedTestPhpFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir', 'test_cache_file.php');
        $cachedTestTxtFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir2', 'test_cache_file.txt');

        $filesystem = ContainerFacade::get('oxid_esales.symfony.file_system');

        $filesystem->dumpFile($cachedTestPhpFile, '');
        $filesystem->dumpFile($cachedTestTxtFile, '');

        $utils = Registry::getUtils();
        $utils->oxResetFileCache();

        $this->assertFileExists($cachedTestPhpFile);
        $this->assertFileExists($cachedTestTxtFile);
    }

    public function testSeoIsActiveWithDefaultConfig(): void
    {
        $isActive = oxNew(Utils::class)->seoIsActive();

        $this->assertTrue($isActive);
    }

    public function testSeoIsActiveWithModifiedConfig(): void
    {
        $this->setParameter('oxid_seo_mode', false);
        $this->replaceContainerInstance();

        $isActive = oxNew(Utils::class)->seoIsActive();

        $this->assertFalse($isActive);
    }

    public function testSeoIsActiveWithInvalidSeoModeValue(): void
    {
        Registry::getConfig()->setConfigParam('aSeoModes', new \stdClass());

        $isActive = oxNew(Utils::class)->seoIsActive();

        $this->assertTrue($isActive);
    }

    public function testSeoIsActiveWithSingleSeoMode(): void
    {
        $currentShopId = 1;
        Registry::getConfig()->setConfigParam('aSeoModes', [$currentShopId => [1 => false]]);

        $isActive = oxNew(Utils::class)->seoIsActive(languageId: 1);

        $this->assertFalse($isActive);
    }

    public function testSeoIsActiveWithReset(): void
    {
        $currentShopId = 1;
        $utils = oxNew(Utils::class);
        $utils->seoIsActive();
        Registry::getConfig()->setConfigParam('aSeoModes', [$currentShopId => [1 => false]]);

        $valueCached = $utils->seoIsActive(languageId: 1);
        $valueAfterReset = $utils->seoIsActive(reset: true, languageId: 1);

        $this->assertTrue($valueCached);
        $this->assertFalse($valueAfterReset);
    }

    public function testSeoIsActiveWithMultipleSeoModes(): void
    {
        $currentShopId = 1;
        Registry::getConfig()->setConfigParam('aSeoModes', [
            $currentShopId => [1 => true, 2 => false],
            2 => [1 => 0, 2 => true],
        ]);

        $this->assertTrue(oxNew(Utils::class)->seoIsActive(languageId: 1));
        $this->assertFalse(oxNew(Utils::class)->seoIsActive(languageId: 2));
        $this->assertFalse(oxNew(Utils::class)->seoIsActive(shopId: 2, languageId: 1));
        $this->assertTrue(oxNew(Utils::class)->seoIsActive(shopId: 2, languageId: 2));
        $this->assertTrue(oxNew(Utils::class)->seoIsActive(shopId: 2, languageId: 3));
    }
}
