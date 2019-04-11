<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use oxOnlineModulesNotifierRequest;
use oxOnlineModuleVersionNotifier;
use oxOnlineModuleVersionNotifierCaller;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @group module
 * @package Integration\Modules
 */
class OnlineModuleNotifierTest extends BaseModuleTestCase
{
    public function setUp()
    {
        parent::setUp();
        ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')
            ->generate();
    }

    protected function tearDown()
    {
        $this->removeTestModules();

        parent::tearDown();
    }

    /**
     * Tests if module was activated.
     */
    public function testVersionNotify()
    {
        $this->installModule('extending_1_class');
        $this->activateModule('extending_1_class');

        $this->installModule('extending_1_class_3_extensions');
        $this->activateModule('extending_1_class_3_extensions');

        $this->installModule('with_everything');

        /** @var oxOnlineModuleVersionNotifierCaller|MockObject $oCaller */
        $oCaller = $this->getMock(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller::class, array('doRequest'), array(), '', false);
        $oCaller->method('doRequest')->with($this->equalTo($this->getExpectedRequest()));

        $oNotifier = new oxOnlineModuleVersionNotifier($oCaller, oxNew('oxModuleList'));
        $oNotifier->versionNotify();
    }

    /**
     * Returns formed request which should be returned during testing.
     *
     * @return oxOnlineModulesNotifierRequest
     */
    protected function getExpectedRequest()
    {
        $oRequest = oxNew('oxOnlineModulesNotifierRequest');

        $sShopUrl = $this->getConfig()->getShopUrl();
        $oRequest->edition = $this->getConfig()->getEdition();
        $oRequest->version = $this->getConfig()->getVersion();
        $oRequest->shopUrl = $sShopUrl;
        $oRequest->pVersion = '1.1';
        $oRequest->productId = 'eShop';

        $modules = new \StdClass();
        $modules->module = array();

        $aModulesInfo = array();
        $aModulesInfo[] = array('id' => 'extending_1_class', 'version' => '1.0', 'activeInShop' => array($sShopUrl));
        $aModulesInfo[] = array('id' => 'extending_1_class_3_extensions', 'version' => '1.0', 'activeInShop' => array($sShopUrl));
        $aModulesInfo[] = array('id' => 'with_everything', 'version' => '1.0', 'activeInShop' => array());

        foreach ($aModulesInfo as $aModuleInfo) {
            $module = new \StdClass();
            $module->id = $aModuleInfo['id'];
            $module->version = $aModuleInfo['version'];
            $module->activeInShops = new \StdClass();
            $module->activeInShops->activeInShop = $aModuleInfo['activeInShop'];
            $modules->module[] = $module;
        }

        $oRequest->modules = $modules;

        return $oRequest;
    }

    private function installModule(string $moduleId)
    {
        $installService = ContainerFactory::getInstance()->getContainer()->get(ModuleInstallerInterface::class);

        $package = new OxidEshopPackage($moduleId, __DIR__ . '/TestData/modules/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $installService->install($package);
    }

    private function activateModule(string $moduleId)
    {
        $activationService = ContainerFactory::getInstance()->getContainer()->get(ModuleActivationBridgeInterface::class);

        $activationService->activate($moduleId, 1);
    }

    private function removeTestModules()
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->container->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
    }
}
