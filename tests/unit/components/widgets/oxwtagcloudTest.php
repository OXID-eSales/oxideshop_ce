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
 * Tests for oxwTagCloud class
 */
class Unit_Components_Widgets_oxwTagCloudTest extends OxidTestCase
{

    /**
     * Testing oxwTagCloud::getTagCloudManager()
     *
     * @return null
     */
    public function testGetTagCloudManager()
    {
        $oTagCloud = new oxwTagCloud();
        $this->assertTrue($oTagCloud->getTagCloudManager() instanceof oxTagCloud);
    }

    /**
     * Testing oxwTagCloud::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oTagCloud = new oxwTagCloud();
        $this->assertEquals('widget/sidebar/tags.tpl', $oTagCloud->render());
    }

    /**
     * Testing oxwTagCloud::displayInBox()
     *
     * @return null
     */
    public function testDisplayInBox()
    {
        $oTagCloud = new oxwTagCloud();
        $oTagCloud->setViewParameters(array("blShowBox" => 1));
        $this->assertTrue($oTagCloud->displayInBox());
    }

    /**
     * Testing oxwTagCloud::isMoreTagsVisible()
     *
     * @return null
     */
    public function testIsMoreTagsVisible()
    {
        $oTagCloud = new oxwTagCloud();
        $this->assertTrue($oTagCloud->isMoreTagsVisible());
    }

}