<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers oxServerProcessor
 * @covers oxApplicationServer
 * @covers oxServerChecker
 * @covers oxServerManager
 */
class Integration_OnlineInfo_OnlineModuleNotifierRequestFormationTest extends OxidTestCase
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

        $oCurl = $this->getMock('oxCurl', array('setParameters', 'execute'));
        $oCurl->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $sXml)));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = new oxOnlineServerEmailBuilder();
        $oOnlineModuleVersionNotifierCaller = new oxOnlineModuleVersionNotifierCaller($oCurl, $oEmailBuilder, new oxSimpleXml());

        $oModule1 = new oxModule();
        $oModule1->setModuleData(array(
            'id' => 'moduleId1',
            'version' => '1.0',
            'active' => true,
        ));

        $oModule2 = new oxModule();
        $oModule2->setModuleData(array(
            'id' => 'moduleId2',
            'version' => '2.0',
            'active' => true,
        ));

        $oModuleList = $this->getMock('oxModuleList', array('getList'));
        $oModuleList->expects($this->any())->method('getList')->will($this->returnValue(array($oModule1, $oModule2)));
        /** @var oxModule $oModuleList */

        $oOnlineModuleVersionNotifier = new oxOnlineModuleVersionNotifier($oOnlineModuleVersionNotifierCaller, $oModuleList);

        $oOnlineModuleVersionNotifier->versionNotify();
    }
}