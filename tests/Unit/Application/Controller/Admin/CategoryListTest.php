<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\CategoryList;
use \oxTestModules;

/**
 * Tests for Category_List class
 */
class CategoryListTest extends \OxidTestCase
{

    /**
     * Category_List::Init() test case
     *
     * @return null
     */
    public function testInit()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock($this->getProxyClassName('Category_List'), array('getSession'));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));

        $oView->init();
        $this->assertEquals(array("oxcategories" => array("oxrootid" => "desc", "oxleft" => "asc")), $oView->getListSorting());
    }

    /**
     * Category_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_List');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["cattree"] instanceof CategoryList);

        $this->assertEquals('category_list.tpl', $sTplName);
    }
}
