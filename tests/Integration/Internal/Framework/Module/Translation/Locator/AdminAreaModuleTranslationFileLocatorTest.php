<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Translation\Locator;

use Prophecy\Prophecy\ObjectProphecy;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator\AdminAreaModuleTranslationFileLocator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Filesystem\Filesystem;

final class AdminAreaModuleTranslationFileLocatorTest extends IntegrationTestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    /** @var ModulesDataProviderInterface */
    private ObjectProphecy $activeModulesDataProvider;

    private Filesystem $filesystem;

    private string $adminThemeName;

    public function setUp(): void
    {
        parent::setUp();

        $this->activeModulesDataProvider = $this->prophesize(ModulesDataProviderInterface::class);
        $this->filesystem = new Filesystem();
        $this->adminThemeName = 'admin';
    }

    public function testLocateWithEmptyPaths(): void
    {
        $this->activeModulesDataProvider->getModulePaths()->willReturn([]);

        $modulesLangFileLocator = new AdminAreaModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem,
            $this->adminThemeName
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame([], $paths);
    }

    public function testLocateForAdminLang(): void
    {
        $modulePath = __DIR__ . '/Fixtures/module-name-2';
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);

        $modulesLangFileLocator = new AdminAreaModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem,
            $this->adminThemeName
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame(
            [
                "$modulePath/views/admin/de/de1_lang.php",
                "$modulePath/views/admin/de/de2_lang.php",
                "$modulePath/views/admin/de/module_options.php"
            ],
            $paths
        );
    }

    public function testLocateWithApplicationFolder(): void
    {
        $modulePath = __DIR__ . '/Fixtures/module-name-4';
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);

        $modulesLangFileLocator = new AdminAreaModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem,
            $this->adminThemeName
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame(
            [
                "$modulePath/Application/views/admin/de/de1_lang.php",
                "$modulePath/Application/views/admin/de/module_options.php"
            ],
            $paths
        );
    }
}
