<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\CategoryList;
use \oxTestModules;

/**
 * Tests for Category_Update class
 */
class CategoryUpdateTest extends \OxidTestCase
{

    /**
     * Category_Update::GetCatListUpdateInfo() test case
     */
    public function testGetCatListUpdateInfo()
    {
        // testing..
        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ["getUpdateInfo"]);
        $oCategoryList->expects($this->once())->method('getUpdateInfo');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategoryUpdate::class, ["getCategoryList"]);
        $oView->expects($this->once())->method('getCategoryList')->will($this->returnValue($oCategoryList));
        $oView->getCatListUpdateInfo();
    }

    /**
     * Category_Update::getCategoryList() test case
     */
    public function testGetCategoryList()
    {
        oxTestModules::addFunction('oxCategoryList', 'updateCategoryTree', '{}');

        $oView = oxNew('Category_Update');
        $this->assertTrue($oView->getCategoryList() instanceof CategoryList);
    }

    /**
     * Category_Update::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_Update');
        $this->assertEquals('category_update', $oView->render());
    }
}
