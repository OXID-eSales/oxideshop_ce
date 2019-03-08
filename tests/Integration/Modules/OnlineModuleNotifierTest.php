<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;
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
    /**
     * Tests if module was activated.
     */
    public function testVersionNotify()
    {
        $this->installAndActivateModule('extending_1_class');
        $this->installAndActivateModule('extending_1_class_3_extensions');
        $this->installAndActivateModule('with_everything');

        /** @var oxOnlineModuleVersionNotifierCaller|MockObject $oCaller */
        $oCaller = $this->getMock(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller::class, array('doRequest'), array(), '', false);
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
        $aModulesInfo[] = array('id' => null, 'version' => null, 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'EshopTestModuleOne', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'extending_1_class', 'version' => '1.0', 'activeInShop' => array($sShopUrl));
        $aModulesInfo[] = array('id' => 'extending_1_class_3_extensions', 'version' => '1.0', 'activeInShop' => array($sShopUrl));
        $aModulesInfo[] = array('id' => 'extending_3_blocks', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'extending_3_classes', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'extending_3_classes_with_1_extension', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'metadata_controllers_feature', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'no_extending', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'translation_Application', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'unifiednamespace_module1', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'unifiednamespace_module2', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'unifiednamespace_module3', 'version' => '1.0.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_1_extension', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_files', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_settings', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_2_templates', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_events', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_everything', 'version' => '1.0', 'activeInShop' => array($sShopUrl));
        $aModulesInfo[] = array('id' => 'with_metadata_v2', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_metadata_v21', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'with_more_metadata_v2', 'version' => '1.0', 'activeInShop' => array());
        $aModulesInfo[] = array('id' => 'without_own_module_namespace', 'version' => '1.0.0', 'activeInShop' => array());

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
