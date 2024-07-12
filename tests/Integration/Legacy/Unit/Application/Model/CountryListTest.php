<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxField;
use OxidEsales\EshopEnterprise\Application\Model\Country;

class CountryListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        /** Insert a total of 8 inactive countries with different titles and the same oxorder */
        $aCountryTitle = ['_CountryListTestId_0' =>  "_CountryListTestTitle_0", '_CountryListTestId_1' =>  "_CountryListTestTitle_1", '_CountryListTestId_2' =>  "_CountryListTestTitle_ä", '_CountryListTestId_3' =>  "_CountryListTestTitle_á", '_CountryListTestId_4' =>  "_CountryListTestTitle_à", '_CountryListTestId_5' =>  "_CountryListTestTitle_a", '_CountryListTestId_6' =>  "_CountryListTestTitle_b", '_CountryListTestId_7' =>  "_CountryListTestTitle_c"];

        foreach ($aCountryTitle as $oxid => $title) {
            /** @var \OxidEsales\Eshop\Application\Controller\Admin\CountryController $country */
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
     */
    protected function tearDown(): void
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
        $query = sprintf('SELECT oxid FROM %s WHERE oxid LIKE \'\_CountryListTestId\_%%\'', $viewName);
        $countryList->selectString($query);

        $this->assertSame(8, $countryList->count(), 'A total of 8 records is retrieved');
    }

    public function testSelectStringOrderByOxOrder()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $query = "SELECT oxid FROM oxcountry WHERE oxid LIKE '\_CountryListTestId\_%' ORDER BY oxorder, oxtitle";
        $countryList ->selectString($query);

        $expectedArrayKeys = ['_CountryListTestId_0', '_CountryListTestId_1', '_CountryListTestId_2', '_CountryListTestId_3', '_CountryListTestId_4', '_CountryListTestId_5', '_CountryListTestId_6', '_CountryListTestId_7'];
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertSame($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxorder');
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

        $expectedArrayKeys = ['_CountryListTestId_1', '_CountryListTestId_2', '_CountryListTestId_3', '_CountryListTestId_4', '_CountryListTestId_5', '_CountryListTestId_6', '_CountryListTestId_7', '_CountryListTestId_0'];
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertSame($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxorder after the field oxd order is changed');
    }

    public function testSelectStringOrderByOxTitle()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $query = "SELECT oxid FROM oxcountry WHERE oxid LIKE '\_CountryListTestId\_%' ORDER BY oxorder, oxtitle";
        $countryList->selectString($query);

        $expectedArrayKeys = ['_CountryListTestId_0', '_CountryListTestId_1', '_CountryListTestId_2', '_CountryListTestId_3', '_CountryListTestId_4', '_CountryListTestId_5', '_CountryListTestId_6', '_CountryListTestId_7'];
        $actualArrayKeys = $countryList->arrayKeys();

        $this->assertSame($expectedArrayKeys, $actualArrayKeys, 'The countries are properly sorted by the field oxtitle');
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountries()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $countryList->loadActiveCountries();

        $this->assertSame(5, $countryList->count());
    }

    /**
     * Tests loadActiveCountries
     */
    public function testLoadActiveCountriesInEN()
    {
        /** @var \oxCountryList $countryList */
        $countryList = oxNew('oxCountryList');
        $countryList->loadActiveCountries(1);
        $this->assertSame('Germany', $countryList['a7c40f631fc920687.20179984']->oxcountry__oxtitle->value);
    }
}
