<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

class UtilsTest extends IntegrationTestCase
{
    public function testToFileCache(): void
    {
        $utils = Registry::getUtils();
        $key = "testCacheKey";
        $value = "testCacheValue";

        $utils->toFileCache($key, $value);

        $this->assertEquals($value, $utils->fromFileCache($key));
    }

    public function testToFileCacheOverrideValue(): void
    {
        $utils = Registry::getUtils();
        $Key = "testCacheKey";
        $value1 = "testCacheFirstValue";
        $value2 = "testCacheSecondValue";

        $utils->toFileCache($Key, $value1);

        $this->assertEquals($value1, $utils->fromFileCache($Key));

        $utils->toFileCache($Key, $value2);

        $this->assertEquals($value2, $utils->fromFileCache($Key));
    }

    public function testLangCache(): void
    {
        $utils = Registry::getUtils();
        $langCache = ['TEST' => 'test value'];
        $cacheName = 'testCacheName';

        $utils->setLangCache($cacheName, $langCache);

        $this->assertEquals($langCache, $utils->getLangCache($cacheName));
    }

    public function testDeleteLanguageCache(): void
    {
        $utils = Registry::getUtils();
        $keyLang1 = 'lang_1_0_0';
        $keyLang2 = 'lang_1_0_0';
        $testLang = ['key1' => 'testVal1', 'key2' => 'testVal2'];

        $utils->setLangCache($keyLang1, $testLang);
        $utils->setLangCache($keyLang2, $testLang);

        $utils->resetLanguageCache();

        $this->assertEmpty($utils->fromFileCache($keyLang1));
        $this->assertEmpty($utils->fromFileCache($keyLang2));
    }

    public function testDeleteMenuCache(): void
    {
        $utils = Registry::getUtils();
        $keyLang1 = 'lang_1_0_0';
        $keyLang2 = 'lang_1_0_0';
        $testLang = ['key1' => 'testVal1', 'key2' => 'testVal2'];

        $utils->setLangCache($keyLang1, $testLang);
        $utils->setLangCache($keyLang2, $testLang);

        $utils->resetLanguageCache();

        $this->assertEmpty($utils->fromFileCache($keyLang1));
        $this->assertEmpty($utils->fromFileCache($keyLang2));
    }

    public function testCacheResetShouldNotRemoveCacheFilesFromSubdirectories(): void
    {
        $utils = Registry::getUtils();
        $context = ContainerFacade::get(ContextInterface::class);

        $cachedTestPhpFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir', 'test_cache_file.php');
        $cachedTestTxtFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir2', 'test_cache_file.txt');
        $filesystem = ContainerFacade::get('oxid_esales.symfony.file_system');

        $filesystem->dumpFile($cachedTestPhpFile, '');
        $filesystem->dumpFile($cachedTestTxtFile, '');
        $utils->oxResetFileCache();

        $this->assertFileExists($cachedTestPhpFile);
        $this->assertFileExists($cachedTestTxtFile);
    }
}
