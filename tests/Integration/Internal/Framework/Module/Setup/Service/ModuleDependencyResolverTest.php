<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleDependencyDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleDependencies;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleDependencyResolver;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * @group module-dependency
 */
final class ModuleDependencyResolverTest extends IntegrationTestCase
{
    public function testIndependentModuleCanBeDeactivated(): void
    {
        $moduleDependencyDao = $this->createStub(ModuleDependencyDaoInterface::class);
        $moduleDependencyDao
            ->method('get')
            ->willReturn(new ModuleDependencies([]));
        $moduleConfigurationDao = $this->createStub(ModuleConfigurationDaoInterface::class);
        $resolver = new ModuleDependencyResolver(
            $moduleDependencyDao,
            $moduleConfigurationDao
        );

        $this->assertTrue($resolver->canDeactivateModule('module-id-1', 1));
    }

    public function testIndependentModuleNotTriggerGetModuleDependenciesMethod(): void
    {
        $moduleDependencyDao = $this->createMock(ModuleDependencyDaoInterface::class);
        $moduleDependencyDao
            ->expects($this->never())
            ->method('get');
        $moduleConfigurationDao = $this->createStub(ModuleConfigurationDaoInterface::class);
        $resolver = new ModuleDependencyResolver(
            $moduleDependencyDao,
            $moduleConfigurationDao
        );

        $resolver->canDeactivateModule('module-id-1', 1);
    }

    public function testDependentModuleCanNotDeactivate(): void
    {
        $moduleDependencyDao = $this->createMock(ModuleDependencyDaoInterface::class);
        $moduleDependencyDao
            ->expects($this->once())
            ->method('get')
            ->willReturn(new ModuleDependencies(['modules' => ['module-id-2', 'module-id-3']]));
        $moduleConfigurationDao = $this->createStub(ModuleConfigurationDaoInterface::class);
        $moduleConfigurationDao
            ->method('getAll')
            ->willReturn([
                $this->getActiveModuleConfig('module-id-1'),
                $this->getActiveModuleConfig('module-id-2'),
                $this->getActiveModuleConfig('module-id-3'),
                $this->getActiveModuleConfig('module-id-4'),
            ]);
        $resolver = new ModuleDependencyResolver(
            $moduleDependencyDao,
            $moduleConfigurationDao
        );

        $this->assertFalse($resolver->canDeactivateModule('module-id-2', 1));
    }

    private function getActiveModuleConfig(string $moduleId): ModuleConfiguration
    {
        return (new ModuleConfiguration())
            ->setId($moduleId)
            ->setActivated(true);
    }
}
