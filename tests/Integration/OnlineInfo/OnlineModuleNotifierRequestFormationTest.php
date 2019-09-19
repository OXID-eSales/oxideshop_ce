<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use \oxOnlineModuleVersionNotifier;
use \oxOnlineModuleVersionNotifierCaller;
use \oxRegistry;
use \oxSimpleXml;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\Service\ApplicationServerService
 */
class OnlineModuleNotifierRequestFormationTest extends \OxidTestCase
{
    private $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
    }

    public function tearDown()
    {
        $this->removeTestModules();
        parent::tearDown();
    }

    public function testRequestFormation()
    {
        $this->installModule('extending_1_class');
        $this->activateModule('extending_1_class');

        $this->installModule('extending_1_class_3_extensions');
        $this->activateModule('extending_1_class_3_extensions');

        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('sClusterId', array('generated_unique_cluster_id'));
        $sEdition = $oConfig->getEdition();
        $sVersion = $oConfig->getVersion();
        $sShopUrl = $oConfig->getShopUrl();

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<omvnRequest>';
        $sXml .=   '<pVersion>1.1</pVersion>';
        $sXml .=   '<modules>';
        $sXml .=     '<module>';
        $sXml .=       '<id>extending_1_class</id>';
        $sXml .=       '<version>1.0</version>';
        $sXml .=       "<activeInShops><activeInShop>$sShopUrl</activeInShop></activeInShops>";
        $sXml .=     '</module>';
        $sXml .=     '<module>';
        $sXml .=       '<id>extending_1_class_3_extensions</id>';
        $sXml .=       '<version>1.0</version>';
        $sXml .=       "<activeInShops><activeInShop>$sShopUrl</activeInShop></activeInShops>";
        $sXml .=     '</module>';
        $sXml .=   '</modules>';
        $sXml .=   '<clusterId>generated_unique_cluster_id</clusterId>';
        $sXml .=   "<edition>$sEdition</edition>";
        $sXml .=   "<version>$sVersion</version>";
        $sXml .=   "<shopUrl>$sShopUrl</shopUrl>";
        $sXml .=   '<productId>eShop</productId>';
        $sXml .= '</omvnRequest>'."\n";

        $curlMock = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['execute','getStatusCode','setParameters'])
            ->getMock();
        $curlMock->expects($this->any())->method('execute')->will($this->returnValue(true));
        $curlMock->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));
        $curlMock->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $sXml)));

        $oEmailBuilder = oxNew(OnlineServerEmailBuilder::class);
        $oOnlineModuleVersionNotifierCaller = new oxOnlineModuleVersionNotifierCaller($curlMock, $oEmailBuilder, new oxSimpleXml());

        $oOnlineModuleVersionNotifier = new oxOnlineModuleVersionNotifier($oOnlineModuleVersionNotifierCaller, oxNew(ModuleList::class));

        $oOnlineModuleVersionNotifier->versionNotify();
    }

    private function installModule(string $moduleId)
    {
        $installService = ContainerFactory::getInstance()->getContainer()->get(ModuleInstallerInterface::class);

        $package = new OxidEshopPackage($moduleId, __DIR__ . '/../Modules/TestData/modules/' . $moduleId);
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
