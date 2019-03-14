<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use \oxCurl;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\EshopCommunity\Core\Exception\SystemComponentException;
use \oxModule;
use \oxOnlineModuleVersionNotifier;
use \oxOnlineModuleVersionNotifierCaller;
use \oxRegistry;
use \oxSimpleXml;
use \oxSystemComponentException;
use \oxTestModules;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\Service\ApplicationServerService
 */
class OnlineModuleNotifierRequestFormationTest extends \OxidTestCase
{
    public function testRequestFormation()
    {
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
        $sXml .=       '<id>moduleId1</id>';
        $sXml .=       '<version>1.0</version>';
        $sXml .=       "<activeInShops><activeInShop>$sShopUrl</activeInShop></activeInShops>";
        $sXml .=     '</module>';
        $sXml .=     '<module>';
        $sXml .=       '<id>moduleId2</id>';
        $sXml .=       '<version>2.0</version>';
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

        $oModule1 = $this
            ->getMockBuilder(Module::class)
            ->setMethods(['isActive'])
            ->getMock();

        $oModule1->method('isActive')->willReturn(true);

        $oModule1->setModuleData(array(
            'id' => 'moduleId1',
            'version' => '1.0',
        ));

        $oModule2 = $this
            ->getMockBuilder(Module::class)
            ->setMethods(['isActive'])
            ->getMock();

        $oModule2->method('isActive')->willReturn(true);

        $oModule2->setModuleData(array(
            'id' => 'moduleId2',
            'version' => '2.0',
        ));

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getList'));
        $oModuleList->expects($this->any())->method('getList')->will($this->returnValue(array($oModule1, $oModule2)));
        /** @var oxModule $oModuleList */

        $oOnlineModuleVersionNotifier = new oxOnlineModuleVersionNotifier($oOnlineModuleVersionNotifierCaller, $oModuleList);

        $oOnlineModuleVersionNotifier->versionNotify();
    }
}
