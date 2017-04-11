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
 * Tests for Country_List class
 */
class Unit_Admin_CountryListTest extends OxidTestCase
{

    /**
     * Country_List::DeleteEntry() test case
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction('oxcountry', 'delete', '{ throw new Exception("delete");}');
        oxTestModules::addFunction('oxcountry', 'isDerived', '{ return false;}');
        modConfig::getInstance()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = new Country_List();
            $oView->deleteEntry();
        } catch (Exception $oExcp) {
            $this->assertEquals("delete", $oExcp->getMessage(), "Error in Country_List::DeleteEntry()");

            return;
        }
        $this->fail("Error in Country_List::DeleteEntry()");
    }

    /**
     * Country_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $view = oxNew('Country_List');

        $this->assertEquals(array('oxcountry' => array('oxactive' => 'asc', 'oxtitle' => 'asc')), $view->getListSorting());
        $this->assertEquals('country_list.tpl', $view->render());
    }

    /**
     * Test, that the country list adds the sorting by oxtitle, if there is only the oxactive sorting given.
     * We do this, cause mysql sees the order of the remainder (after sorting by active) as undefined.
     */
    public function testAddingSortingByTitleWhenOnlyActiveIsGiven()
    {
        $view = oxNew('Country_List');

        $countryList = $view->getItemList();
        $firstActiveTitle = null;
        $firstInactiveTitle = null;

        foreach ($countryList as $country) {
            $isCountryActive= $this->isCountryActive($country);

            $firstActiveTitle = $this->getTitleIfUnset($firstActiveTitle, $country, $isCountryActive);
            $firstInactiveTitle = $this->getTitleIfUnset($firstInactiveTitle, $country, !$isCountryActive);
        }

        $this->assertEquals('Deutschland', $firstActiveTitle);
        $this->assertEquals('Afghanistan', $firstInactiveTitle);
    }

    /**
     * Determine, if the given country is active.
     *
     * @param oxCountry $country The country we want to know, if it is activated.
     *
     * @return bool Is the given country active?
     */
    private function isCountryActive($country)
    {
        return '1' === $country->oxcountry__oxactive->value;
    }

    /**
     * If the item title is not set yet, take it from the country object.
     *
     * @param null|string $countryTitle The title of the first country we process.
     * @param oxCountry   $country      The first country we process.
     * @param bool        $process      Should we take this country or not, cause it was not of the correct activation status.
     *
     * @return string The title of the first country we process.
     */
    private function getTitleIfUnset($countryTitle, $country, $process)
    {
        if (is_null($countryTitle) && $process) {
            $countryTitle = $country->oxcountry__oxtitle->value;
        }

        return $countryTitle;
    }

}