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
 * @copyright (C) OXID eSales AG 2003-2015
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
        $oView = new Country_List();

        $this->assertEquals(array('oxcountry' => array('oxactive' => 'asc', 'oxtitle' => 'asc')), $oView->getListSorting());
        $this->assertEquals('country_list.tpl', $oView->render());
    }

    /**
     * Test, that the country list adds the sorting by oxtitle, if there is only the oxactive sorting given.
     * We do this, cause mysql sees the order of the remainder (after sorting by active) as undefined.
     */
    public function testAddingSortingByTitleWhenOnlyActiveIsGiven()
    {
        $oView = oxNew('Country_List');

        $oList = $oView->getItemList();
        $sFirstItem = null;
        $sFirstInactiveItem = null;

        foreach ($oList as $oCountry) {
            if ('1' === $oCountry->oxcountry__oxactive->value) {
                if (is_null($sFirstItem)) {
                    $sFirstItem = $oCountry->oxcountry__oxtitle->value;
                }
            } else {
                if (is_null($sFirstInactiveItem)) {
                    $sFirstInactiveItem = $oCountry->oxcountry__oxtitle->value;
                }
            }
        }

        $this->assertEquals('Deutschland', $sFirstItem);
        $this->assertEquals('Afghanistan', $sFirstInactiveItem);
    }

}