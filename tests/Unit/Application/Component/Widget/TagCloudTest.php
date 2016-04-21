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
namespace Unit\Application\Component\Widget;

use oxTagCloud;

/**
 * Tests for oxwTagCloud class
 */
class TagCloudTest extends \OxidTestCase
{

    /**
     * Testing oxwTagCloud::getTagCloudManager()
     *
     * @return null
     */
    public function testGetTagCloudManager()
    {
        $oTagCloud = oxNew('oxwTagCloud');
        $this->assertTrue($oTagCloud->getTagCloudManager() instanceof oxTagCloud);
    }

    /**
     * Testing oxwTagCloud::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oTagCloud = oxNew('oxwTagCloud');
        $this->assertEquals('widget/sidebar/tags.tpl', $oTagCloud->render());
    }

    /**
     * Testing oxwTagCloud::displayInBox()
     *
     * @return null
     */
    public function testDisplayInBox()
    {
        $oTagCloud = oxNew('oxwTagCloud');
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
        $oTagCloud = oxNew('oxwTagCloud');
        $this->assertTrue($oTagCloud->isMoreTagsVisible());
    }

}