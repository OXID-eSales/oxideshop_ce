<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command\ModuleCommandsTestCase;
use Symfony\Component\Filesystem\Path;

final class InstallModuleAssetsCommandTest extends ModuleCommandsTestCase
{
    use ContainerTrait;

    public function testInstallModuleAssets(): void
    {
        $testModulePackage = new OxidEshopPackage(Path::join($this->modulesPath, $this->moduleId));

        $this->installTestModule();

        $moduleFilesInstaller = $this->get(ModuleFilesInstallerInterface::class);
        $moduleFilesInstaller->uninstall($testModulePackage);

        $this->executeCommand('oe:module:install-assets');

        $this->assertTrue(
            $moduleFilesInstaller->isInstalled($testModulePackage)
        );
    }
}
