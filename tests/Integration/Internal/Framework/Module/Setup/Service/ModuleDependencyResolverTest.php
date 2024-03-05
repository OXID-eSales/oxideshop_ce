<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use PHPUnit\Framework\Attributes\Group;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleDependencyDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleDependencies;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleDependencyResolver;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

#[Group('module-dependency')]
final class ModuleDependencyResolverTest extends IntegrationTestCase
{
    public function testIndependentModuleDoesNotHaveUnresolvedModuleDependenciesDuringActivationProcess(): void
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
        $unresolvedDependencies = $resolver->getUnresolvedActivationDependencies('module-id-1', 1);

        $this->assertFalse($unresolvedDependencies->hasModuleDependencies());
    }

    public function testIndependentModuleNotTriggerGetModuleDependenciesMethod(): void
    {
        $moduleDependencyDao = $this->createMock(ModuleDependencyDaoInterface::class);
        $moduleDependencyDao
            ->expects($this->never())
            ->method('get');
        $moduleConfigurationDao = $this->createStub(ModuleConfigurationDaoInterface::class);
        $moduleConfigurationDao
            ->method('getAll')
            ->willReturn([
                $this->getActiveModuleConfig('module-id-1'),
            ]);
        $moduleDependencyResolver = new ModuleDependencyResolver(
            $moduleDependencyDao,
            $moduleConfigurationDao
        );

        $moduleDependencyResolver->getUnresolvedDeactivationDependencies('module-id-1', 1);
    }

    public function testDependentModuleHasUnresolvedModuleDependenciesDuringDeactivationProcess(): void
    {
        $moduleDependencyDao = $this->createStub(ModuleDependencyDaoInterface::class);
        $moduleDependencyDao
            ->method('get')
            ->willReturnCallback(function ($moduleId): ModuleDependencies {
                return $this->getModuleDependenciesCallback($moduleId);
            });
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

        $unresolvedDependencies = $resolver->getUnresolvedDeactivationDependencies('module-id-2', 1);

        $this->assertTrue($unresolvedDependencies->hasModuleDependencies());
        $this->assertEquals(['module-id-1', 'module-id-4'], $unresolvedDependencies->getModuleIds());
    }

    public function getModuleDependenciesCallback($moduleId): ModuleDependencies
    {
        if ($moduleId === 'module-id-1') {
            return new ModuleDependencies(['modules' => ['module-id-2', 'module-id-3']]);
        }

        if ($moduleId === 'module-id-4') {
            return new ModuleDependencies(['modules' => ['module-id-2']]);
        }

        return new ModuleDependencies();
    }

    private function getActiveModuleConfig(string $moduleId): ModuleConfiguration
    {
        return (new ModuleConfiguration())
            ->setId($moduleId)
            ->setActivated(true);
    }
}
