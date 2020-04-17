<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache\Command;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command\ModuleCommandsTestCase;

final class ClearCacheCommandTest extends ModuleCommandsTestCase
{

    public function testCacheClearing(): void
    {
        $tempDirectory = Registry::get(ConfigFile::class)->getVar('sCompileDir');

        $oxcMask = $tempDirectory . '*.*';
        $beforeOxc = glob($oxcMask);

        $templateMask = $tempDirectory . 'smarty/' . '*.php';
        $beforeTemplate = glob($templateMask);

        $sourcePath = Registry::get(ConfigFile::class)->getVar('sShopDir');
        $cacheDirectory = $sourcePath . '/cache/';
        $cacheMask = $cacheDirectory . '*.cache';
        $beforeCache = glob($cacheMask);

        $this->executeCommand(
            [
                'command' => 'oe:cache:clear',
            ]
        );

        $afterOxc = scandir($tempDirectory);
        foreach ($beforeOxc as $file) {
            $this->assertNotContains($file, $afterOxc);
        }

        $afterTemplate = scandir($tempDirectory);
        foreach ($beforeTemplate as $file) {
            $this->assertNotContains($file, $afterTemplate);
        }

        $afterCache = scandir($cacheDirectory);
        foreach ($beforeCache as $file) {
            $this->assertNotContains($file, $afterCache);
        }
    }
}
