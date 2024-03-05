<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Template\Locator;

use Prophecy\Prophecy\ObjectProphecy;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Template\Locator\ModulesMenuFileLocator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Filesystem\Filesystem;

final class ModulesMenuFileLocatorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ActiveModulesDataProviderInterface */
    private ObjectProphecy $activeModulesDataProvider;
    /** @var Filesystem */
    private ObjectProphecy $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeModulesDataProvider = $this->prophesize(ActiveModulesDataProviderInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);
    }

    public function testLocateWithEmptyPaths(): void
    {
        $this->activeModulesDataProvider->getModulePaths()->willReturn([]);

        $paths = (new ModulesMenuFileLocator($this->activeModulesDataProvider->reveal(), $this->filesystem->reveal()))
            ->locate();

        $this->assertSame([], $paths);
    }

    public function testLocateWithActiveModuleAndExistingFile(): void
    {
        $modulePath = 'some-active-module/path';
        $menuPath = "$modulePath/menu.xml";
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);
        $this->filesystem->exists($menuPath)->willReturn(true);

        $paths = (new ModulesMenuFileLocator($this->activeModulesDataProvider->reveal(), $this->filesystem->reveal()))
            ->locate();

        $this->assertSame([$menuPath], $paths);
    }

    public function testLocateWithActiveModuleAndMissingFile(): void
    {
        $modulePath = 'some-active-module/path';
        $menuPath = "$modulePath/menu.xml";
        $this->activeModulesDataProvider->getModulePaths()->willReturn([$modulePath]);
        $this->filesystem->exists($menuPath)->willReturn(false);

        $paths = (new ModulesMenuFileLocator($this->activeModulesDataProvider->reveal(), $this->filesystem->reveal()))
            ->locate();

        $this->assertSame([], $paths);
    }

    public function testLocateWithMultipleFiles(): void
    {
        $modulePath1 = 'some-active-module/path-1';
        $menuPath1 = "$modulePath1/menu.xml";
        $modulePath2 = 'some-active-module/path-2';
        $menuPath2 = "$modulePath2/menu.xml";
        $modulePath3 = 'some-active-module/path-3';
        $menuPath3 = "$modulePath3/menu.xml";
        $this->activeModulesDataProvider->getModulePaths()->willReturn([
            $modulePath1,
            $modulePath2,
            $modulePath3,
        ]);
        $this->filesystem->exists($menuPath1)->willReturn(true);
        $this->filesystem->exists($menuPath2)->willReturn(false);
        $this->filesystem->exists($menuPath3)->willReturn(true);

        $paths = (new ModulesMenuFileLocator($this->activeModulesDataProvider->reveal(), $this->filesystem->reveal()))
            ->locate();

        $this->assertSame([$menuPath1, $menuPath3], $paths);
    }
}
