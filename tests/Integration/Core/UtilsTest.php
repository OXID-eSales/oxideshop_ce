<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

class UtilsTest extends IntegrationTestCase
{
    public function testCacheResetShouldNotRemoveCacheFilesFromSubdirectories(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $context = $container->get(ContextInterface::class);

        $cachedTestPhpFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir', 'test_cache_file.php');
        $cachedTestTxtFile = Path::join($context->getCacheDirectory(), 'myTestSubCacheDir2', 'test_cache_file.txt');

        $filesystem = $container->get('oxid_esales.symfony.file_system');

        $filesystem->dumpFile($cachedTestPhpFile, '');
        $filesystem->dumpFile($cachedTestTxtFile, '');

        $utils = Registry::getUtils();
        $utils->oxResetFileCache();

        $this->assertFileExists($cachedTestPhpFile);
        $this->assertFileExists($cachedTestTxtFile);
    }
}
