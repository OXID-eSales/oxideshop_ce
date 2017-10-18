<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Shop_List class
 */
class ShopListTest extends \OxidTestCase
{
    /**
     * Shop_List::Init() test case
     */
    public function testInit()
    {
        // testing..
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock($this->getProxyClassName('Shop_List'), array('getSession'));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));
        $oView->init();

        $this->assertEquals(array("oxshops" => array("oxname" => "asc")), $oView->getListSorting());
    }

    /**
     * Shop_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_List');
        $this->assertEquals('shop_list.tpl', $oView->render());
    }

    /**
     * Shop_List::BuildWhere() test case
     */
    public function testBuildWhere()
    {
        // Force to set shop ID, as this method rewrite session value. So next line would be lost otherwise.
        $this->getConfig()->getShopId();
        $this->getSession()->setVariable("malladmin", null);
        $this->getSession()->setVariable("actshop", "testShopId");

        $sViewName = getViewName("oxshops");

        // testing..
        $oView = oxNew('Shop_List');
        $aWhere = $oView->buildWhere();
        $this->assertTrue(isset($aWhere[$sViewName . '.oxid']));
        $this->assertEquals("testShopId", $aWhere[$sViewName . '.oxid']);
    }
}
