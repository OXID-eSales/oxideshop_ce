<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use PHPUnit\Framework\Attributes\Group;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleDependencyDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

#[Group('module-dependency')]
final class ModuleDependencyDaoTest extends TestCase
{
    use ContainerTrait;

    private string $testModuleWithDependenciesPath = __DIR__ . '/Fixtures/TestModuleWithDependencies';
    private string $testModuleWithEmptyDependencyYamlPath = __DIR__ . '/Fixtures/TestModuleWithEmptyDependencyFile';

    public function testReturnEmptyArrayIfNoDependencyYaml(): void
    {
        $pathResolverStub = $this->createStub(ModulePathResolverInterface::class);
        $pathResolverStub
            ->method('getFullModulePathFromConfiguration')
            ->willReturn('no-module-path');
        $moduleDependencyDao = new ModuleDependencyDao(
            $this->get(FileStorageFactoryInterface::class),
            $pathResolverStub,
            $this->get(BasicContextInterface::class)
        );
        $moduleDependencies = $moduleDependencyDao->get('module-id');

        $this->assertEmpty($moduleDependencies->getRequiredModuleIds());
    }

    public function testReturnEmptyArrayIfDependencyYamlEmpty(): void
    {
        $pathResolverStub = $this->createStub(ModulePathResolverInterface::class);
        $pathResolverStub
            ->method('getFullModulePathFromConfiguration')
            ->willReturn($this->testModuleWithEmptyDependencyYamlPath);
        $moduleDependencyDao = new ModuleDependencyDao(
            $this->get(FileStorageFactoryInterface::class),
            $pathResolverStub,
            $this->get(BasicContextInterface::class)
        );
        $moduleDependencies = $moduleDependencyDao->get('module-id');

        $this->assertEmpty($moduleDependencies->getRequiredModuleIds());
    }

    public function testReturnAnArrayOfDependencies(): void
    {
        $pathResolverStub = $this->createStub(ModulePathResolverInterface::class);
        $pathResolverStub
            ->method('getFullModulePathFromConfiguration')
            ->willReturn($this->testModuleWithDependenciesPath);
        $moduleDependencyDao = new ModuleDependencyDao(
            $this->get(FileStorageFactoryInterface::class),
            $pathResolverStub,
            $this->get(BasicContextInterface::class)
        );
        $moduleDependencies = $moduleDependencyDao->get('module-id');

        $this->assertCount(3, $moduleDependencies->getRequiredModuleIds());
    }
}
