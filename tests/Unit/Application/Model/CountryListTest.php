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
namespace Unit\Application\Model;

use oxField;
use OxidEsales\EshopEnterprise\Application\Model\Country;

class CountryListTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        /** Insert a total of 8 inactive countries with different titles and the same oxorder */
        $aCountryTitle = array(
        '_CountryListTestId_0' =>  "_CountryListTestTitle_0",
        '_CountryListTestId_1' =>  "_CountryListTestTitle_1",
        '_CountryListTestId_2' =>  "_CountryListTestTitle_ä",
        '_CountryListTestId_3' =>  "_CountryListTestTitle_á",
        '_CountryListTestId_4' =>  "_CountryListTestTitle_à",
        '_CountryListTestId_5' =>  "_CountryListTestTitle_a",
        '_CountryListTestId_6' =>  "_CountryListTestTitle_b",
        '_CountryListTestId_7' =>  "_CountryListTestTitle_c"
        );

        foreach ($aCountryTitle as $oxid => $title) {
            /** @var Country $country */
            $country = oxNew('oxCountry');
            $country->setId($oxid);
            $country->oxcountry__oxactive = new oxField(0, oxField::T_RAW);
            $country->oxcountry__oxorder = new oxField(0, oxField::T_RAW);
            $country->oxcountry__oxtitle = new oxField($title, oxField::T_RAW);
            
            $country->save();
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxcountry');

        parent::tearDown();
    }

    /**
     * Tests selectString and _localCompare
     */
    public function testSelectStringRetrievesProperNumberOfRecords()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $viewName = $countryList->getBaseObject()->getViewName();
        $query = "SELECT oxid FROM $viewName WHERE oxid LIKE '\_CountryListTestId\_%'";
        $countryList->selectString($query);

        $this->assertEquals(8, $countryList->count(), 'A total of 8 records is retrieved');
    }

    public function testSelectStringOrderByOxOrder()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $query = "SELECT oxid FROM oxcountry WHERE oxid LIKE '\_CountryListTestId\_%' ORDER BY oxorder, oxtitle";
        $countryList ->selectString($query);

        $expectedArrayKeys = array(
            '_CountryListTestId_0',
            '_CountryListTestId_1',
            '_CountryListTestId_2',
            '_CountryListTestId_3',
            '_CountryListTestId_4',
            '_CountryListTestId_5',
            '_CountryListTestId_6',
            '_CountryListTestId_7',
            );
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertEquals($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxorder');
    }

    public function testSelectStringChangeOrderRetrievesResultInProperOrder()
    {

        /** Put the first row to the end of the results by giving it an oxsort of 999 */
        /** @var \oxCountry $country */
        $country = oxNew('oxCountry');
        $country->load('_CountryListTestId_0');
        $country->oxcountry__oxorder = new oxField('999', oxField::T_RAW);
        $country->save();

        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $query = "SELECT oxid FROM oxcountry WHERE oxid LIKE '\_CountryListTestId\_%' ORDER BY oxorder, oxtitle";
        $countryList->selectString($query);

        $expectedArrayKeys = array(
            '_CountryListTestId_1',
            '_CountryListTestId_2',
            '_CountryListTestId_3',
            '_CountryListTestId_4',
            '_CountryListTestId_5',
            '_CountryListTestId_6',
            '_CountryListTestId_7',
            '_CountryListTestId_0');
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertEquals($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxorder after the field oxd order is changed');
    }

    public function testSelectStringOrderByOxTitle()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $query = "SELECT oxid FROM oxcountry WHERE oxid LIKE '\_CountryListTestId\_%' ORDER BY oxorder, oxtitle";
        $countryList->selectString($query);

        $expectedArrayKeys = array(
            '_CountryListTestId_0',
            '_CountryListTestId_1',
            '_CountryListTestId_2',
            '_CountryListTestId_3',
            '_CountryListTestId_4',
            '_CountryListTestId_5',
            '_CountryListTestId_6',
            '_CountryListTestId_7',
        );
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertEquals($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxtitle');
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountries()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $countryList->loadActiveCountries();

        $this->assertEquals(5, $countryList->count());
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountriesInEN()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $countryList->loadActiveCountries(1);
        $this->assertEquals('Germany', $countryList['a7c40f631fc920687.20179984']->oxcountry__oxtitle->value);
    }
}
