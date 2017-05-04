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

class Unit_Views_tagsTest extends OxidTestCase
{

    /**
     * Testing Tags::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oView = new Tags();
        $this->assertEquals('page/tags/tags.tpl', $oView->render());
    }

    /**
     * Testing Tags::getTagCloudManager()
     *
     * @return null
     */
    public function testGetTagCloudManager()
    {
        $oView = new Tags();
        $this->assertTrue($oView->getTagCloudManager() instanceof oxTagCloud);
    }

    /**
     * Testing Tags::getTitleSuffix()
     *
     * @return null
     */
    public function testGetTitleSuffix()
    {
        $oView = new Tags();
        $this->assertNull($oView->getTitleSuffix());
    }

    /**
     * Testing Tags::getTitlePageSuffix()
     *
     * @return null
     */
    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock("Tags", array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $this->assertEquals(oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getTitlePageSuffix());
    }

    /**
     * Testing tags::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oTags = new Tags();

        $this->assertEquals(1, count($oTags->getBreadCrumb()));
    }
}