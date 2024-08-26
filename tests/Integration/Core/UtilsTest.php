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

final class UtilsTest extends IntegrationTestCase
{
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
}
