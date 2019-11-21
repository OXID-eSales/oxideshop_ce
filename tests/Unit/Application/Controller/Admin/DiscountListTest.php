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
class DiscountListTest extends \OxidTestCase
{

    /**
     * Discount_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $session->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Discount_List'));
        $oView->init();

        $this->assertEquals("oxdiscount", $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals("oxdiscountlist", $oView->getNonPublicVar("_sListType"));
        $this->assertEquals(array('oxdiscount' => array("oxsort" => "asc")), $oView->getListSorting());
        $this->assertEquals('discount_list.tpl', $oView->render());
    }
}
