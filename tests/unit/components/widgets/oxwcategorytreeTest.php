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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for oxwCategoryTree class
 */
class Unit_Components_Widgets_oxwCategoryTreeTest extends OxidTestCase
{

    /**
     * Testing oxwCategoryTree::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oCategoryTree = new oxwCategoryTree();
        $this->assertEquals('widget/sidebar/categorytree.tpl', $oCategoryTree->render());
    }

    /**
     * Testing oxwCategoryTree::render()
     *
     * @return null
     */
    public function testRenderDifferentTemplate()
    {
        $oCategoryTree = new oxwCategoryTree();
        $oCategoryTree->setViewParameters(array("sWidgetType" => "header"));
        $this->assertEquals('widget/header/categorylist.tpl', $oCategoryTree->render());
    }

    /**
     * Testing oxwCategoryTree::getDeepLevel()
     *
     * @return null
     */
    public function testGetDeepLevel()
    {
        $oCategoryTree = new oxwCategoryTree();
        $oCategoryTree->setViewParameters(array("deepLevel" => 2));
        $this->assertEquals(2, $oCategoryTree->getDeepLevel());
    }
}