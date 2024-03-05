<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Translation\Locator;

use Prophecy\Prophecy\ObjectProphecy;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator\FrontendModuleTranslationFileLocator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Filesystem\Filesystem;

final class FrontendModuleTranslationFileLocatorTest extends IntegrationTestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    /** @var ActiveModulesDataProviderInterface */
    private ObjectProphecy $activeModulesDataProvider;

    private Filesystem $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->activeModulesDataProvider = $this->prophesize(ActiveModulesDataProviderInterface::class);
        $this->filesystem = new Filesystem();
    }

    public function testLocateWithEmptyPaths(): void
    {
        $this->activeModulesDataProvider->getModulePaths()->willReturn([]);

        $modulesLangFileLocator = new FrontendModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame([], $paths);
    }

    public function testLocateWithExistingFile(): void
    {
        $modulePath = __DIR__ . '/Fixtures/module-name-1';
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);

        $modulesLangFileLocator = new FrontendModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame(
            [
                "$modulePath/translations/de/de1_lang.php",
                "$modulePath/translations/de/de2_lang.php"
            ],
            $paths
        );
    }

    public function testLocateWithApplicationFolder(): void
    {
        $modulePath = __DIR__ . '/Fixtures/module-name-3';
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);

        $modulesLangFileLocator = new FrontendModuleTranslationFileLocator(
            $this->activeModulesDataProvider->reveal(),
            $this->filesystem
        );

        $paths = $modulesLangFileLocator->locate('de');

        self::assertSame(
            [
                "$modulePath/Application/translations/de/de1_lang.php",
                "$modulePath/Application/translations/de/de2_lang.php"
            ],
            $paths
        );
    }
}
