<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Discount_List class
 */
class DiscountListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Discount_List::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Discount_List'));
        $oView->init();

        $this->assertSame("oxdiscount", $oView->getNonPublicVar("_sListClass"));
        $this->assertSame("oxdiscountlist", $oView->getNonPublicVar("_sListType"));
        $this->assertSame(['oxdiscount' => ["oxsort" => "asc"]], $oView->getListSorting());
        $this->assertSame('discount_list', $oView->render());
    }
}
