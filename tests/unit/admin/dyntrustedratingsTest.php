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
 * Tests for dyn_trusted_ratings class
 */
class Unit_Admin_dyntrustedratingsTest extends OxidTestCase
{

    /**
     * dyn_trusted_ratings::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_trusted_ratings();
        $this->assertEquals('dyn_trusted_ratings.tpl', $oView->render());
    }

    /**
     * dyn_trusted_ratings::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        $sLangAbbr = oxRegistry::getLang()->getLanguageAbbr();

        $this->setRequestParam("confaarrs", array("aTsLangIds" => array($sLangAbbr => "testId")));
        $this->setRequestParam("confbools", array("blTsWidget" => "true"));
        $this->setRequestParam("oxid", "testShopId");
        $sPkg = "OXID_ESALES";

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock("oxConfig", array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')->with($this->equalTo("arr"), $this->equalTo("aTsActiveLangIds"), $this->equalTo(array($sLangAbbr => true)), $this->equalTo("testShopId"));
        $oConfig->setConfigParam("sTsUser", "testUser");
        $oConfig->setConfigParam("sTsPass", "testPass");

        $oView = $this->getMock("dyn_trusted_ratings", array("getConfig", "_validateId"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('_validateId')->with($this->equalTo("testId"), $this->equalTo(true), $this->equalTo("testUser"), $this->equalTo("testPass"), $this->equalTo($sPkg))->will($this->returnValue("OK"));

        $oView->save();
    }

    /**
     * dyn_trusted_ratings::_getServiceWsdl() test case
     *
     * @return null
     */
    public function testGetServiceWsdl()
    {
        modConfig::getInstance()->setConfigParam("aTsConfig", array("blTestMode" => false));
        modConfig::getInstance()->setConfigParam("sTsServiceWsdl", "testWsdlUrl");

        $oView = new dyn_trusted_ratings();
        $this->assertEquals("testWsdlUrl", $oView->UNITgetServiceWsdl());
    }

    /**
     * dyn_trusted_ratings::_multilineToArray() test case
     *
     * @return null
     */
    public function testMultilineToArray()
    {
        // defining parameters
        $sMultiline = "a\nb\n\nc";

        // testing..
        $oView = new dyn_trusted_ratings();
        $this->assertEquals(array(0 => "a", 1 => "b", 3 => "c"), $oView->UNITmultilineToArray($sMultiline));
        $this->assertEquals(array(0 => "a", 1 => "b", 3 => "c"), $oView->UNITmultilineToArray(array(0 => "a", 1 => "b", 3 => "c")));
    }

    /**
     * dyn_trusted_ratings::_multilineToAarray() test case
     *
     * @return null
     */
    public function testMultilineToAarray()
    {
        // defining parameters
        $sMultiline = "a => b\nc => d";

        // testing..
        $oView = new dyn_trusted_ratings();
        $this->assertEquals(array("a" => "b", "c" => "d"), $oView->UNITmultilineToAarray($sMultiline));
        $this->assertEquals(array("a" => "b", "c" => "d"), $oView->UNITmultilineToAarray(array("a" => "b", "c" => "d")));
    }

    /**
     * dyn_trusted_ratings::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = new dyn_trusted_ratings();
        $this->assertEquals('dyn_interface', $oView->getViewId());
    }
}
