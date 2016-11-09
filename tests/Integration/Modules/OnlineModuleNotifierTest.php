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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Integration\Modules;

use oxOnlineModulesNotifierRequest;
use oxOnlineModuleVersionNotifier;
use oxOnlineModuleVersionNotifierCaller;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

require_once __DIR__ . '/BaseModuleTestCase.php';

class OnlineModuleNotifierTest extends BaseModuleTestCase
{
    /**
     * Tests if module was activated.
     */
    public function testVersionNotify()
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare(array('extending_1_class', 'extending_1_class_3_extensions', 'with_everything'));

        /** @var oxOnlineModuleVersionNotifierCaller|MockObject $oCaller */
        $oCaller = $this->getMock('oxOnlineModuleVersionNotifierCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->with($this->equalTo($this->getExpectedRequest()));

        $oModuleList = oxNew('oxModuleList');
        $sModuleDir = __DIR__ . '/TestData/modules';
        $oModuleList->getModulesFromDir($sModuleDir);

        $oNotifier = new oxOnlineModuleVersionNotifier($oCaller, $oModuleList);
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
        $aModulesInfo[] = array('id' => 'extending_3_blocks', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'extending_3_classes', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'extending_3_classes_with_1_extension', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'no_extending', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'virtualnamespace_module1', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'virtualnamespace_module2', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'virtualnamespace_module3', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_1_extension', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_files', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_settings', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_templates', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_events', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_everything', 'version' => '1.0', 'activeInShop' => array($sShopUrl));

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
}
