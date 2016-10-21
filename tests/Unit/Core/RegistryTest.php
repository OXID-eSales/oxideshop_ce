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
namespace Unit\Core;

use oxConfig;
use OxidEsales\EshopCommunity\Core\Registry;
use oxLang;
use oxRegistry;
use oxSession;
use oxStr;
use oxUtils;
use stdClass;

/**
 * Test case for OxReg
 */
class RegistryTest extends \OxidTestCase
{

    /**
     * test for OxReg::get()
     */
    public function testGet()
    {
        $oStr = Registry::get("oxstr");
        $this->assertTrue($oStr instanceof OxStr);
    }

    /**
     * Tests that Registry is functioning in non case sensitive way
     */
    public function testSetGetCaseInsensitive()
    {
        $oStr = Registry::get("oxSTR");
        $oStr->test = "testValue";
        //differen case
        $oStr2 = Registry::get("OxStr");
        $this->assertEquals("testValue", $oStr2->test);
    }

    /**
     * tests OxReg::get() if the same instance is given every time
     */
    public function testGetSameInstance()
    {
        $oStr = Registry::get("oxstr");
        $oStr->test = "testValue";
        $oStr = Registry::get("oxstr");
        $this->assertEquals("testValue", $oStr->test);
    }

    /**
     * Tests OxReg::get() and OxReg::set()
     */
    public function testSetGetInstance()
    {
        $oTest = new stdClass();
        $oTest->testPublic = "testPublicVal";

        Registry::set("testCase", $oTest);
        $oTest2 = Registry::get("testCase");

        $this->assertEquals("testPublicVal", $oTest2->testPublic);
    }

    /**
     * Test for OxReg::getConfig()
     */
    public function testGetConfig()
    {
        $oSubj = $this->getConfig();
        $this->assertTrue($oSubj instanceof oxConfig);
    }

    public function testGetSession()
    {
        $oSubj = Registry::getSession();
        $this->assertTrue($oSubj instanceof oxSession);
    }

    public function testGetLang()
    {
        $oSubj = Registry::getLang();
        $this->assertTrue($oSubj instanceof oxLang);
    }

    public function testGetLUtils()
    {
        $oSubj = Registry::getUtils();
        $this->assertTrue($oSubj instanceof oxUtils);
    }

    public function testGetKeys()
    {
        Registry::set("testKey", "testVal");
        $this->assertTrue(in_array(strtolower("testKey"), Registry::getKeys()));
    }

    public function testUnset()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue(in_array(strtolower("testKey"), Registry::getKeys()));
        oxRegistry::set("testKey", null);
        $this->assertFalse(in_array(strtolower("testKey"), Registry::getKeys()));
    }

    public function testInstanceExists()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue(Registry::instanceExists('testKey'));
        oxRegistry::set("testKey", null);
        $this->assertFalse(Registry::instanceExists('testKey'));
    }
}
