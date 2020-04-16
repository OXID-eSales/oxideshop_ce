<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Command;

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

        $this->executeCommand(
            [
                'command' => 'oe:module:apply-configuration',
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
    }
}
