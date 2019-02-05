<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstaller;
use PHPUnit\Framework\TestCase;

class ModuleInstallerTest extends TestCase
{

    public function testInstallTriggersAllInstallers()
    {
        $path = 'packagePath';

        $moduleFilesInstaller = $this->getMockBuilder(ModuleFilesInstallerInterface::class)->getMock();
        $moduleFilesInstaller
            ->expects($this->once())
            ->method('forceCopy')
            ->with($path);

        $moduleProjectConfigurationInstaller = $this->getMockBuilder(ModuleConfigurationInstallerInterface::class)->getMock();
        $moduleProjectConfigurationInstaller
            ->expects($this->once())
            ->method('install')
            ->with($path);

        $moduleInstaller = new ModuleInstaller($moduleFilesInstaller, $moduleProjectConfigurationInstaller);
        $moduleInstaller->install($path);
    }

    /**
     * @dataProvider moduleInstallMatrixDataProvider
     *
     * @param bool $filesInstalled
     * @param bool $projectConfigurationInstalled
     * @param bool $moduleInstalled
     */
    public function testIsInstalled(bool $filesInstalled, bool $projectConfigurationInstalled, bool $moduleInstalled)
    {
        $moduleFilesInstaller = $this->getMockBuilder(ModuleFilesInstallerInterface::class)->getMock();
        $moduleFilesInstaller->method('isInstalled')->willReturn($filesInstalled);

        $moduleProjectConfigurationInstaller = $this->getMockBuilder(ModuleConfigurationInstallerInterface::class)->getMock();
        $moduleProjectConfigurationInstaller->method('isInstalled')->willReturn($projectConfigurationInstalled);

        $moduleInstaller = new ModuleInstaller($moduleFilesInstaller, $moduleProjectConfigurationInstaller);

        $this->assertSame(
            $moduleInstalled,
            $moduleInstaller->isInstalled('somePath')
        );
    }

    public function moduleInstallMatrixDataProvider(): array
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
