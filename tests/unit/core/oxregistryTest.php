<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxreg.php 25467 2010-02-01 14:14:26Z tomas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';

/**
 * Test case for OxReg
 */
class unit_core_OxRegistryTest extends OxidTestCase
{

    /**
     * `__callStatic` should be defined and return null by default.
     */
    public function testCallStatic_methodExistAndReturnNullByDefault()
    {
        $this->assertNull( oxRegistry::DummyCall() );
    }

    /**
     * `__callStatic` should return null if calling getter for non-existing class instance.
     */
    public function testCallStatic_gettingNonExistingInstance_returnNull()
    {
        $this->assertNull( oxRegistry::getSomeVeryUnlikelyClassName() );
    }

    /**
     * `__callStatic` should return exactly the same instance as default oxRegistry get method
     *  if calling it as a getter.
     */
    public function testCallStatic_gettingExistingInstance_returnTheInstance()
    {
        $this->assertSame( oxRegistry::get( 'oxCaptcha' ), oxRegistry::getOxCaptcha() );
    }

    /**
     * test for OxReg::get()
     */
    public function testGet()
    {
        $oStr = OxRegistry::get("oxstr");
        $this->assertTrue($oStr instanceof OxStr);
    }

    /**
     * Tests that Registry is functioning in non case sensitive way
     */
    public function testSetGetCaseInsensitive()
    {
        $oStr = OxRegistry::get("oxSTR");
        $oStr->test = "testValue";
        //differen case
        $oStr2 = OxRegistry::get("OxStr");
        $this->assertEquals("testValue", $oStr2->test);
    }

    /**
     * tests OxReg::get() if the same instance is given every time
     */
    public function testGetSameInstance()
    {
        $oStr = OxRegistry::get("oxstr");
        $oStr->test = "testValue";
        $oStr = OxRegistry::get("oxstr");
        $this->assertEquals("testValue", $oStr->test);
    }

    /**
     * Tests OxReg::get() and OxReg::set()
     */
    public function testSetGetInstance()
    {
        $oTest = new stdClass();
        $oTest->testPublic = "testPublicVal";

        OxRegistry::set("testCase", $oTest);
        $oTest2 = OxRegistry::get("testCase");

        $this->assertEquals("testPublicVal", $oTest2->testPublic);
    }

    /**
     * Test for OxReg::getConfig()
     */
    public function testGetConfig()
    {
        $oSubj = OxRegistry::getConfig();
        $this->assertTrue($oSubj instanceof oxConfig);
    }

    public function testGetSession()
    {
        $oSubj = OxRegistry::getSession();
        $this->assertTrue($oSubj instanceof oxSession);
    }

    public function testGetLang()
    {
        $oSubj = OxRegistry::getLang();
        $this->assertTrue($oSubj instanceof oxLang);
    }

    public function testGetLUtils()
    {
        $oSubj = OxRegistry::getUtils();
        $this->assertTrue($oSubj instanceof oxUtils);
    }

    public function testGetKeys()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue( in_array( strtolower("testKey"), oxRegistry::getKeys()) );
    }


    public function testUnset()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue( in_array( strtolower("testKey"), oxRegistry::getKeys()) );
        oxRegistry::set("testKey", null);
        $this->assertFalse( in_array( strtolower("testKey"), oxRegistry::getKeys()) );
    }
}
