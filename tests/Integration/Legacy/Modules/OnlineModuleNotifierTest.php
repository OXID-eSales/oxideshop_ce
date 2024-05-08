<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Core\OnlineModuleVersionNotifier;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\Facts\Facts;
use oxOnlineModulesNotifierRequest;
use oxOnlineModuleVersionNotifierCaller;
use PHPUnit\Framework\MockObject\MockObject;
use StdClass;

final class OnlineModuleNotifierTest extends BaseModuleTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
    }

    /**
     * Tests if module was activated.
     */
    public function testVersionNotify(): void
    {
        $this->installModule('extending_1_class');
        $this->activateModule('extending_1_class');

        $this->installModule('extending_1_class_3_extensions');
        $this->activateModule('extending_1_class_3_extensions');

        $this->installModule('with_everything');

        /** @var oxOnlineModuleVersionNotifierCaller|MockObject $oCaller */
        $oCaller = $this->getMockBuilder(OnlineModuleVersionNotifierCaller::class)->disableOriginalConstructor()
            ->getMock();
        $oCaller->expects($this->once())
            ->method('doRequest')
            ->with($this->equalTo($this->getExpectedRequest()));

        $oNotifier = new OnlineModuleVersionNotifier($oCaller);
        $oNotifier->versionNotify();
    }

    /**
     * Returns formed request which should be returned during testing.
     *
     * @return oxOnlineModulesNotifierRequest
     */
    private function getExpectedRequest()
    {
        $oRequest = oxNew('oxOnlineModulesNotifierRequest');

        $sShopUrl = Registry::getConfig()->getShopUrl();
        $oRequest->edition = (new Facts())->getEdition();
        $oRequest->version = ShopVersion::getVersion();
        $oRequest->shopUrl = $sShopUrl;
        $oRequest->pVersion = '1.1';
        $oRequest->productId = 'eShop';

        $modules = new StdClass();
        $modules->module = [];

        $aModulesInfo = [];
        $aModulesInfo[] = [
            'id' => 'extending_1_class',
            'version' => '1.0',
            'activeInShop' => [$sShopUrl],
        ];
        $aModulesInfo[] = [
            'id' => 'extending_1_class_3_extensions',
            'version' => '1.0',
            'activeInShop' => [$sShopUrl],
        ];
        $aModulesInfo[] = [
            'id' => 'with_everything',
            'version' => '1.0',
            'activeInShop' => [],
        ];

        foreach ($aModulesInfo as $aModuleInfo) {
            $module = new StdClass();
            $module->id = $aModuleInfo['id'];
            $module->version = $aModuleInfo['version'];
            $module->activeInShops = new StdClass();
            $module->activeInShops->activeInShop = $aModuleInfo['activeInShop'];
            $modules->module[] = $module;
        }

        $oRequest->modules = $modules;

        return $oRequest;
    }

    private function installModule(string $moduleId): void
    {
        $installService = ContainerFactory::getInstance()->getContainer()->get(ModuleInstallerInterface::class);

        $package = new OxidEshopPackage(__DIR__ . '/TestData/modules/' . $moduleId);
        $installService->install($package);
    }

    private function activateModule(string $moduleId): void
    {
        $activationService = ContainerFactory::getInstance()->getContainer()->get(
            ModuleActivationBridgeInterface::class
        );

        $activationService->activate($moduleId, 1);
    }
}
