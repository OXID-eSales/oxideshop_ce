<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for VoucherSerie_List class
 */
class VoucherSerieListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * VoucherSerie_List::DeleteEntry() test case
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");
        oxTestModules::addFunction('oxvoucherserie', 'load', '{ return true; }');
        oxTestModules::addFunction('oxvoucherserie', 'deleteVoucherList', '{ return true; }');

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('VoucherSerie_List'));
        $oView->deleteEntry();
    }

    /**
     * VoucherSerie_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('VoucherSerie_List');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNull($aViewData["allowSharedEdit"]);
        $this->assertNull($aViewData["malladmin"]);
        $this->assertNull($aViewData["updatelist"]);
        $this->assertNull($aViewData["sort"]);

        $this->assertEquals('voucherserie_list', $sTplName);
    }
}
