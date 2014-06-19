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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(".") . '/unit/OxidTestCase.php';
require_once realpath(".") . '/unit/test_config.inc.php';

/**
 * Testing oxshoplist class
 */
class Unit_Core_oxshoplistTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $oDb = oxDb::getDb();

        for ($i = 2; $i < 5; $i++) {
                $oDb->execute("INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME) VALUES ($i, 1, 'Test Shop $i')");
        }
    }

    /**
     * Executed after test is down
     *
     */
    protected function tearDown()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("DELETE FROM `oxshops` WHERE `oxid` > 1");

        parent::tearDown();
    }

    /**
     * All shop list test
     */
    public function testGetAll()
    {
        $oShopList = new oxShopList();
        $oShopList->getAll();
        $this->assertEquals(4, $oShopList->count());
    }

    /**
     * Check that raw array retrieval has required fields
     */
    public function testGetRawList()
    {
        $oShopList = new oxShopList();

        $aList = $oShopList->getRawList('SELECT `oxshops`.* FROM `oxshops`');

        $this->assertEquals(4, count($aList));
        $this->assertEquals('1', $aList[0]->oxshops__oxid->value);
        $this->assertEquals('OXID eShop 5', $aList[0]->oxshops__oxname->value);
        $this->assertEquals(array('oxshops__oxid', 'oxshops__oxname'), array_keys(get_object_vars($aList[2])));
        $this->assertEquals('3', $aList[2]->oxshops__oxid->value);
        $this->assertEquals('Test Shop 3', $aList[2]->oxshops__oxname->value);
    }

    /**
     * Check that raw array retrieval has required fields
     */
    public function testDefaultGetRawList()
    {
        $oShopList = new oxShopList();

        $aList = $oShopList->getRawList();
        $this->assertEquals(4, count($aList));

        oxDb::getDb()->execute("update oxshops set oxactive = 0 where oxid in (2,4)");

        $aList = $oShopList->getRawList();
        $this->assertEquals(2, count($aList));
    }
}
