<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataObject;

use DomainException;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

class ShopConfigurationTest extends TestCase
{
    /** @var ShopConfiguration */
    private $shopConfiguration;

    protected function setUp()
    {
        parent::setUp();
        $this->shopConfiguration = new ShopConfiguration();
    }

    public function testGetModuleConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModuleId');

        $this->shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $this->assertSame($moduleConfiguration, $this->shopConfiguration->getModuleConfiguration('testModuleId'));
    }

    public function testGetModuleConfigurations()
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

    public function testGetModuleConfigurationThrowsExceptionIfModuleIdNotPresent()
    {
        $this->expectException(DomainException::class);
        $this->shopConfiguration->getModuleConfiguration('moduleIdNotPresent');
    }

    public function testDeleteModuleConfigurationThrowsExceptionIfModuleIdNotPresent()
    {
        $this->expectException(DomainException::class);
        $this->shopConfiguration->deleteModuleConfiguration('moduleIdNotPresent');
    }

    public function testChains()
    {
        $chain = new Chain();
        $chain->setName('classes');

        $this->shopConfiguration->addChain($chain);

        $this->assertSame(
            $chain,
            $this->shopConfiguration->getChain('classes')
        );
    }

    public function testDefaultChains()
    {
        $chain = new Chain();
        $chain->setName(Chain::CLASS_EXTENSIONS);

        $this->assertEquals(
            $chain,
            $this->shopConfiguration->getChain(Chain::CLASS_EXTENSIONS)
        );
    }

    public function testGetChainIfChainDoesNotExist()
    {
        $this->expectException(DomainException::class);
        $this->shopConfiguration->getChain('fakeChainId');
    }
}
