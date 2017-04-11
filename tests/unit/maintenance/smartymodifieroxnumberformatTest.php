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

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'core/smarty/plugins/modifier.oxnumberformat.php';

class Unit_Maintenance_smartyModifierOxNumberFormatTest extends OxidTestCase
{

    /**
     * Provides number format, number and expected value
     */
    public function Provider()
    {
        return array(
            array("EUR@ 1.00@ ,@ .@ EUR@ 2", 25000, '25.000,00'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 2", 25000.1584, '25.000,16'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 3", 25000.1584, '25.000,158'),
            array("EUR@ 1.00@ ,@ .@ EUR@ 0", 25000000.5584, '25.000.001'),
            array("EUR@ 1.00@ .@ ,@ EUR@ 2", 25000000.5584, '25,000,000.56'),
        );
    }

    /**
     * Tests how oxnumberformat modifier works
     *
     * @dataProvider Provider
     */
    public function testNumberFormatDefaultFormat($sFormat, $mValue, $sExpected)
    {
        $this->assertEquals($sExpected, smarty_modifier_oxnumberformat($sFormat, $mValue));
    }

}
