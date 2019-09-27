<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyPrefiltersDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyResourcesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartySecuritySettingsDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartySettingsDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationFactory;

class SmartyConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $factory = new SmartyConfigurationFactory(
            $this->getSmartyContextMock(false),
            $this->getSmartySettingsDataProviderMock(),
            $this->getSmartySecuritySettingsDataProviderMock(),
            $this->getSmartyResourcesDataProviderMock(),
            $this->getSmartyPrefiltersDataProviderMock(),
            $this->getSmartyPluginsDataProviderMock()
        );
        $configuration = $factory->getConfiguration();

        $this->assertSame(['testSetting'], $configuration->getSettings());
        $this->assertSame([], $configuration->getSecuritySettings());
        $this->assertSame(['testResources'], $configuration->getResources());
        $this->assertSame(['testPlugins'], $configuration->getPlugins());
        $this->assertSame(['testPrefilters'], $configuration->getPrefilters());
    }

    public function testGetConfigurationWithSecuritySettingsOn()
    {
        $factory = new SmartyConfigurationFactory(
            $this->getSmartyContextMock(true),
            $this->getSmartySettingsDataProviderMock(),
            $this->getSmartySecuritySettingsDataProviderMock(),
            $this->getSmartyResourcesDataProviderMock(),
            $this->getSmartyPrefiltersDataProviderMock(),
            $this->getSmartyPluginsDataProviderMock()
        );
        $configuration = $factory->getConfiguration();

        $this->assertSame(['testSetting'], $configuration->getSettings());
        $this->assertSame(['testSecuritySetting'], $configuration->getSecuritySettings());
        $this->assertSame(['testResources'], $configuration->getResources());
        $this->assertSame(['testPlugins'], $configuration->getPlugins());
        $this->assertSame(['testPrefilters'], $configuration->getPrefilters());
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        return $smartyContextMock;
    }

    private function getSmartySettingsDataProviderMock(): SmartySettingsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartySettingsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getSettings')
            ->willReturn(['testSetting']);

        return $smartyContextMock;
    }

    private function getSmartySecuritySettingsDataProviderMock(): SmartySecuritySettingsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartySecuritySettingsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getSecuritySettings')
            ->willReturn(['testSecuritySetting']);

        return $smartyContextMock;
    }

    private function getSmartyResourcesDataProviderMock(): SmartyResourcesDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyResourcesDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getResources')
            ->willReturn(['testResources']);

        return $smartyContextMock;
    }

    private function getSmartyPluginsDataProviderMock(): SmartyPluginsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyPluginsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getPlugins')
            ->willReturn(['testPlugins']);

        return $smartyContextMock;
    }

    private function getSmartyPrefiltersDataProviderMock(): SmartyPrefiltersDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyPrefiltersDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getPrefilterPlugins')
            ->willReturn(['testPrefilters']);

        return $smartyContextMock;
    }
}
