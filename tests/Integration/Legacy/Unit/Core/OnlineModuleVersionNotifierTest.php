<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxOnlineModuleVersionNotifier;

class OnlineModuleVersionNotifierTest extends \PHPUnit\Framework\TestCase
{
    public function testVersionNotifyWithModulesInShop()
    {
        $oCaller = $this->getMock(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller::class, ['doRequest'], [], '', false);
        $oCaller->method('doRequest');

        $oModule = $this->getMock('oxModule');

        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, ['getList']);
        $oModuleList->method('getList')->willReturn([$oModule]);

        $oNotifier = new oxOnlineModuleVersionNotifier($oCaller, $oModuleList);
        $oNotifier->versionNotify();
    }
}
