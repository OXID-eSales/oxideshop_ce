<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataObject;

use DomainException;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ChainGroup;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;
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
        $testModuleId = 'testModuleId';
        $this->shopConfiguration->setModuleConfiguration($testModuleId, $moduleConfiguration);
        $this->assertSame($moduleConfiguration, $this->shopConfiguration->getModuleConfiguration($testModuleId));
    }

    public function testGetModuleConfigurations()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->shopConfiguration->setModuleConfiguration('firstModule', $moduleConfiguration);
        $this->shopConfiguration->setModuleConfiguration('secondModule', $moduleConfiguration);

        $this->assertSame(
            [
                'firstModule'   => $moduleConfiguration,
                'secondModule'  => $moduleConfiguration,
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

    public function testChainGroups()
    {
        $chainGroup = new ChainGroup();

        $this
            ->shopConfiguration
            ->setChainGroup('classes', $chainGroup);

        $this->assertSame(
            $chainGroup,
            $this->shopConfiguration->getChainGroup('classes')
        );
    }
}
