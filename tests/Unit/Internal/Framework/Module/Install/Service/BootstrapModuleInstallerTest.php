<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\BootstrapModuleInstaller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BootstrapModuleInstallerTest extends TestCase
{
    public function testInstallTriggersAllInstallers(): void
    {
        $path = 'packagePath';
        $package = new OxidEshopPackage($path);

        $moduleFilesInstaller = $this->createMock(ModuleFilesInstallerInterface::class);
        $moduleFilesInstaller
            ->expects($this->once())
            ->method('install')
            ->with($package);

        $moduleProjectConfigurationInstaller = $this->createMock(ModuleConfigurationInstallerInterface::class);
        $moduleProjectConfigurationInstaller
            ->expects($this->once())
            ->method('install')
            ->with($path);


        $moduleInstaller = new BootstrapModuleInstaller($moduleFilesInstaller, $moduleProjectConfigurationInstaller);
        $moduleInstaller->install($package);
    }

    #[DataProvider('moduleInstallMatrixDataProvider')]
    public function testIsInstalled(bool $filesInstalled, bool $projectConfigurationInstalled, bool $moduleInstalled): void
    {
        $moduleFilesInstaller = $this->createStub(ModuleFilesInstallerInterface::class);
        $moduleFilesInstaller->method('isInstalled')->willReturn($filesInstalled);

        $moduleProjectConfigurationInstaller = $this->createStub(ModuleConfigurationInstallerInterface::class);
        $moduleProjectConfigurationInstaller->method('isInstalled')->willReturn($projectConfigurationInstalled);

        $moduleInstaller = new BootstrapModuleInstaller($moduleFilesInstaller, $moduleProjectConfigurationInstaller);

        $this->assertSame(
            $moduleInstalled,
            $moduleInstaller->isInstalled(new OxidEshopPackage('somePath'))
        );
    }

    public static function moduleInstallMatrixDataProvider(): array
    {
        return [
            [
                'filesInstalled'                => false,
                'projectConfigurationInstalled' => false,
                'moduleInstalled'               => false,
            ],
            [
                'filesInstalled'                => true,
                'projectConfigurationInstalled' => false,
                'moduleInstalled'               => false,
            ],
            [
                'filesInstalled'                => false,
                'projectConfigurationInstalled' => true,
                'moduleInstalled'               => false,
            ],
            [
                'filesInstalled'                => true,
                'projectConfigurationInstalled' => true,
                'moduleInstalled'               => true,
            ],
        ];
    }
}
