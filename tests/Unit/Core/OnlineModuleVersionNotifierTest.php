<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxOnlineModuleVersionNotifier;

class OnlineModuleVersionNotifierTest extends \OxidTestCase
{
    public function testVersionNotifyWithModulesInShop()
    {
        $oCaller = $this->getMock(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller::class, array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest');

        $oModule = $this->getMock('oxModule');

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, array('getList'));
        $oModuleList->expects($this->any())->method('getList')->will($this->returnValue(array($oModule)));

        $oNotifier = new oxOnlineModuleVersionNotifier($oCaller, $oModuleList);
        $oNotifier->versionNotify();
    }
}
