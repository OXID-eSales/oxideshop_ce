<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ThemePackageInstaller;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemePackageInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $themePackagePath = __DIR__ . '/Fixtures/test-theme-package-installation';
    private string $packageName = 'test-vendor/test-theme-package-installation';
    private string $themeId = 'test-theme-package-installation';

    public function testThemeConfigurationIsInstalledAfterInstallProcess(): void
    {
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertTrue(
            $this->get(ThemeConfigurationDaoInterface::class)->exists(
                $this->themeId,
                $this->get(BasicContextInterface::class)->getDefaultShopId()
            )
        );
    }

    public function testThemeInstallDoesNotUseMainContainer(): void
    {
        ContainerFactory::resetContainer();
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertFileDoesNotExist(
            $this->get(ContextInterface::class)->getContainerCacheFilePath(
                $this->get(ContextInterface::class)->getCurrentShopId()
            )
        );
    }

    private function getPackageInstaller(string $packageName, array $extra = []): ThemePackageInstaller
    {
        $package = new Package($packageName, '1.0.0', '1.0.0');
        $package->setExtra($extra);

        return new ThemePackageInstaller(
            new NullIO(),
            $this->get(BasicContextInterface::class)->getSourcePath(),
            $package
        );
    }
}
