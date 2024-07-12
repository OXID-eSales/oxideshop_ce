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
class CategoryListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Category_List::Init() test case
     */
    public function testInit()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Category_List'));
        $oView->init();
        $this->assertSame(["oxcategories" => ["oxrootid" => "desc", "oxleft" => "asc"]], $oView->getListSorting());
    }

    /**
     * Category_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_List');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\CategoryList::class, $aViewData["cattree"]);

        $this->assertSame('category_list', $sTplName);
    }
}
