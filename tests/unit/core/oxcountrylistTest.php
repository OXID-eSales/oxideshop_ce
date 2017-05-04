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

class Unit_Core_oxCountryListTest extends OxidTestCase
{

    public $aList = array();

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $aCountryTitle = array("oxCountryListTest_ä",
                               "oxCountryListTest_1",
                               "oxCountryListTest_a",
                               "oxCountryListTest_ö",
                               "oxCountryListTest_b");

        $this->aList = array();
        foreach ($aCountryTitle as $iPos => $sTitle) {
            $this->aList[$iPos] = new oxcountry();
            $this->aList[$iPos]->setId('_testCountryId' . $iPos);
            $this->aList[$iPos]->oxcountry__oxorder = new oxField('123', oxField::T_RAW);
            $this->aList[$iPos]->oxcountry__oxtitle = new oxField($sTitle, oxField::T_RAW);
            $this->aList[$iPos]->Save();
        }

        // and one with diff order number
        $oCountry = new oxcountry();
        $oCountry->setId('_testCountryId5');
        $oCountry->oxcountry__oxorder = new oxField('0', oxField::T_RAW);
        $oCountry->oxcountry__oxtitle = new oxField("oxCountryListTest_ä", oxField::T_RAW);
        $oCountry->save();
        $this->aList[] = $oCountry;
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxcountry');

        /*
        foreach ( $this->aList as $oCountry )
            $oCountry->delete();
        */
        parent::tearDown();
    }

    /**
     * Tests selectString and _localCompare
     */
    public function testSelectString()
    {
        $oCountryList = new oxCountryList();
        $sVN = $oCountryList->getBaseObject()->getViewName();
        $sSelect = "SELECT oxid, oxtitle, oxisoalpha2 FROM $sVN WHERE oxtitle like 'oxCountryListTest%' ORDER BY oxorder, oxtitle";
        $oCountryList->selectString($sSelect);

        $aList = array('_testCountryId5', '_testCountryId1', '_testCountryId2', '_testCountryId0', '_testCountryId4', '_testCountryId3');

        $this->assertEquals(6, $oCountryList->count());
        $this->assertEquals($aList, $oCountryList->arrayKeys());
    }

    /**
     * Tests selectString and _localCompare
     */
    public function testSelectStringChangeOrder()
    {
        $oCountry = new oxcountry();
        $oCountry->load('_testCountryId4');
        $oCountry->oxcountry__oxorder = new oxField('999', oxField::T_RAW);
        $oCountry->save();
        $oCountryList = new oxCountryList();
        $sSelect = "SELECT oxid, oxtitle as oxtitle FROM oxcountry WHERE oxtitle like 'oxCountryListTest%' ORDER BY oxorder, oxtitle";
        $oCountryList->selectString($sSelect);

        $aList = array('_testCountryId5', '_testCountryId1', '_testCountryId2', '_testCountryId0', '_testCountryId3', '_testCountryId4');

        $this->assertEquals(6, $oCountryList->count());
        $this->assertEquals($aList, $oCountryList->arrayKeys());
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountries()
    {
        $oCountryList = new oxCountryList();
        $oCountryList->loadActiveCountries();

        $this->assertEquals(5, $oCountryList->count());
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountriesInEN()
    {
        $oCountryList = new oxCountryList();
        $oCountryList->loadActiveCountries(1);
        $this->assertEquals('Germany', $oCountryList['a7c40f631fc920687.20179984']->oxcountry__oxtitle->value);
    }
}
