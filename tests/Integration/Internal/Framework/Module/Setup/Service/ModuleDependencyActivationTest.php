<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use PHPUnit\Framework\Attributes\Group;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\DependencyValidationException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

#[Group('module-dependency')]
final class ModuleDependencyActivationTest extends IntegrationTestCase
{
    private int $shopId = 1;
    private string $testDependentModulePath = __DIR__ . '/Fixtures/TestDependentModule';
    private string $testDependentModuleId = 'test-dependent-module';
    private string $testModuleWithDependencyPath = __DIR__ . '/Fixtures/TestModuleWithDependency';
    private string $testModuleWithDependencyId = 'test-module-with-dependency';
    private string $testMissingDependencyModuleId = 'test-missing-dependency-module';
    private string $testMissingDependencyModulePath = __DIR__ . '/Fixtures/TestMissingDependencyModule';

    public function setup(): void
    {
        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->install(new OxidEshopPackage($this->testDependentModulePath));
        $moduleInstaller->install(new OxidEshopPackage($this->testModuleWithDependencyPath));

        parent::setUp();
    }

    public function tearDown(): void
    {
        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->uninstall(new OxidEshopPackage($this->testModuleWithDependencyPath));
        $moduleInstaller->uninstall(new OxidEshopPackage($this->testDependentModulePath));

        parent::tearDown();
    }

    public function testSuccessfulActivation(): void
    {
        $moduleActivation = $this->get(ModuleActivationBridgeInterface::class);
        $moduleActivation->activate($this->testDependentModuleId, $this->shopId);
        $moduleActivation->activate($this->testModuleWithDependencyId, $this->shopId);

        $this->assertTrue(
            $this->get(ModuleActivationBridgeInterface::class)->isActive(
                $this->testModuleWithDependencyId,
                $this->shopId
            )
        );
    }

    public function testActivationExceptionThrownIfDependentModuleIsInactive(): void
    {
        $this->expectException(DependencyValidationException::class);
        $this->expectExceptionMessage(sprintf('to be activated: "%s"', $this->testDependentModuleId));

        $this->get(ModuleActivationBridgeInterface::class)->activate(
            $this->testModuleWithDependencyId,
            $this->shopId
        );
    }

    public function testSuccessfulDeactivation(): void
    {
        $moduleActivation = $this->get(ModuleActivationBridgeInterface::class);
        $moduleActivation->activate($this->testDependentModuleId, $this->shopId);
        $moduleActivation->activate($this->testModuleWithDependencyId, $this->shopId);

        $moduleActivation->deactivate($this->testModuleWithDependencyId, $this->shopId);
        $moduleActivation->deactivate($this->testDependentModuleId, $this->shopId);

        $this->assertFalse($moduleActivation->isActive($this->testModuleWithDependencyId, $this->shopId));
        $this->assertFalse($moduleActivation->isActive($this->testDependentModuleId, $this->shopId));
    }

    public function testDeactivationExceptionThrownIfItIsADependencyOfAnotherModule(): void
    {
        $this->expectException(DependencyValidationException::class);
        $this->expectExceptionMessage(sprintf('to be deactivated: "%s"', $this->testModuleWithDependencyId));

        $moduleActivation = $this->get(ModuleActivationBridgeInterface::class);
        $moduleActivation->activate($this->testDependentModuleId, $this->shopId);
        $moduleActivation->activate($this->testModuleWithDependencyId, $this->shopId);

        $this->get(ModuleActivationBridgeInterface::class)->deactivate(
            $this->testDependentModuleId,
            $this->shopId
        );
    }

    public function testDependencyValidatorShouldFireEarlier(): void
    {
        $this->expectException(DependencyValidationException::class);

        $moduleInstaller = $this->get(ModuleInstallerInterface::class);
        $moduleInstaller->install(new OxidEshopPackage($this->testMissingDependencyModulePath));

        $moduleActivation = $this->get(ModuleActivationBridgeInterface::class);
        $moduleActivation->activate($this->testMissingDependencyModuleId, $this->shopId);

        $moduleInstaller->uninstall(new OxidEshopPackage($this->testMissingDependencyModulePath));
    }
}
