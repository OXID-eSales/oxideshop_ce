<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxTestModules;

/**
 * Tests for Shop_List class
 */
class ShopListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Shop_List::Init() test case
     */
    public function testInit()
    {
        // testing..
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Shop_List'));
        $oView->init();

        $this->assertSame(["oxshops" => ["oxname" => "asc"]], $oView->getListSorting());
    }

    /**
     * Shop_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_List');
        $this->assertSame('shop_list', $oView->render());
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

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName("oxshops");

        // testing..
        $oView = oxNew('Shop_List');
        $aWhere = $oView->buildWhere();
        $this->assertArrayHasKey($sViewName . '.oxid', $aWhere);
        $this->assertSame("testShopId", $aWhere[$sViewName . '.oxid']);
    }
}
