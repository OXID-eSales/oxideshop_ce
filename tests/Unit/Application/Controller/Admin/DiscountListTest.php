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

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock($this->getProxyClassName('Discount_List'), array('getSession'));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));

        $oView->init();

        $this->assertEquals("oxdiscount", $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals("oxdiscountlist", $oView->getNonPublicVar("_sListType"));
        $this->assertEquals(array('oxdiscount' => array("oxsort" => "asc")), $oView->getListSorting());
        $this->assertEquals('discount_list.tpl', $oView->render());
    }
}
