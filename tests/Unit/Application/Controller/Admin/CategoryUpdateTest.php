<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Application\Model\CategoryList;

use \oxTestModules;

/**
 * Tests for Category_Update class
 */
class CategoryUpdateTest extends \OxidTestCase
{

    /**
     * Category_Update::GetCatListUpdateInfo() test case
     *
     * @return null
     */
    public function testGetCatListUpdateInfo()
    {
        // testing..
        $oCategoryList = $this->getMock("oxCategoryList", array("getUpdateInfo"));
        $oCategoryList->expects($this->once())->method('getUpdateInfo');

        $oView = $this->getMock("Category_Update", array("_getCategoryList"));
        $oView->expects($this->once())->method('_getCategoryList')->will($this->returnValue($oCategoryList));
        $oView->getCatListUpdateInfo();
    }

    /**
     * Category_Update::_getCategoryList() test case
     *
     * @return null
     */
    public function testGetCategoryList()
    {
        oxTestModules::addFunction('oxCategoryList', 'updateCategoryTree', '{}');

        $oView = oxNew('Category_Update');
        $this->assertTrue($oView->UNITgetCategoryList() instanceof CategoryList);
    }

    /**
     * Category_Update::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_Update');
        $this->assertEquals('category_update.tpl', $oView->render());
    }
}
