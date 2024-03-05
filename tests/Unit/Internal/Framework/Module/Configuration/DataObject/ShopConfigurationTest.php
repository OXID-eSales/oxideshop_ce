<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

final class ShopConfigurationTest extends TestCase
{
    private ShopConfiguration $shopConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopConfiguration = new ShopConfiguration();
    }

    public function testGetModuleConfiguration(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModuleId');

        $this->shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $this->assertSame($moduleConfiguration, $this->shopConfiguration->getModuleConfiguration('testModuleId'));
    }

    public function testGetModuleConfigurations(): void
    {
        $moduleConfiguration1 = new ModuleConfiguration();
        $moduleConfiguration1->setId('firstModule');

        $moduleConfiguration2 = new ModuleConfiguration();
        $moduleConfiguration2->setId('secondModule');

        $this->shopConfiguration
             ->addModuleConfiguration($moduleConfiguration1)
             ->addModuleConfiguration($moduleConfiguration2);

        $this->assertSame(
            [
                'firstModule'   => $moduleConfiguration1,
                'secondModule'  => $moduleConfiguration2,
            ],
            $this->shopConfiguration->getModuleConfigurations()
        );
    }

    public function testHasModuleConfiguration(): void
    {
        $testModuleId = 'testModuleId';

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($testModuleId);

        $this->assertFalse(
            $this->shopConfiguration->hasModuleConfiguration($testModuleId)
        );

        $this->shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->assertTrue(
            $this->shopConfiguration->hasModuleConfiguration($testModuleId)
        );
    }

    public function testGetModuleConfigurationThrowsExceptionIfModuleIdNotPresent(): void
    {
        $this->expectException(ModuleConfigurationNotFoundException::class);
        $this->shopConfiguration->getModuleConfiguration('moduleIdNotPresent');
    }

    public function testDeleteModuleConfiguration(): void
    {
        $testModuleId = 'testModuleId';

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($testModuleId);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfiguration->deleteModuleConfiguration($testModuleId);

        $this->assertFalse($shopConfiguration->hasModuleConfiguration($testModuleId));
    }

    public function testDeleteModuleConfigurationRemovesModuleExtensionFromChain(): void
    {
        $moduleExtensionToStay = new ClassExtension(
            'shopClass',
            'moduleExtensionToStay'
        );
        $moduleConfigurationToStay = new ModuleConfiguration();
        $moduleConfigurationToStay->setId('moduleToStay');
        $moduleConfigurationToStay->addClassExtension($moduleExtensionToStay);

        $moduleExtensionToDelete = new ClassExtension(
            'shopClass',
            'moduleExtensionToDelete'
        );
        $moduleConfigurationToDelete = new ModuleConfiguration();
        $moduleConfigurationToDelete->setId('moduleToDelete');
        $moduleConfigurationToDelete->addClassExtension($moduleExtensionToDelete);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration
            ->addModuleConfiguration($moduleConfigurationToDelete)
            ->addModuleConfiguration($moduleConfigurationToStay);

        $shopConfiguration->getClassExtensionsChain()->addExtension($moduleExtensionToStay);
        $shopConfiguration->getClassExtensionsChain()->addExtension($moduleExtensionToDelete);

        $shopConfiguration->deleteModuleConfiguration('moduleToDelete');

        $expectedClassExtensionChain = new ClassExtensionsChain();
        $expectedClassExtensionChain->addExtension($moduleExtensionToStay);

        $this->assertEquals(
            $expectedClassExtensionChain,
            $shopConfiguration->getClassExtensionsChain()
        );
    }

    public function testDeleteModuleConfigurationThrowsExceptionIfModuleIdNotPresent(): void
    {
        $this->expectException(ModuleConfigurationNotFoundException::class);
        $this->shopConfiguration->deleteModuleConfiguration('moduleIdNotPresent');
    }

    public function testChains(): void
    {
        $chain = new ClassExtensionsChain();

        $this->shopConfiguration->setClassExtensionsChain($chain);

        $this->assertSame(
            $chain,
            $this->shopConfiguration->getClassExtensionsChain()
        );
    }

    public function testDefaultChains(): void
    {
        $chain = new ClassExtensionsChain();

        $this->assertEquals(
            $chain,
            $this->shopConfiguration->getClassExtensionsChain()
        );
    }
}
