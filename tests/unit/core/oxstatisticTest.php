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

class Unit_Core_oxstatisticTest extends OxidTestCase
{

    /**
     * Testing if deletion removes all db records
     */
    public function testSetGetReports()
    {
        $oStatistic = oxNew("oxstatistic");
        $oStatistic->setReports("aaaa");
        $this->assertEquals("aaaa", $oStatistic->getReports());
    }

    /**
     * Testing oxvalue field setter
     */
    public function testSetFieldData()
    {
        $sValue = '"\"&\'';

        $oStatistic = oxNew("oxstatistic");
        $oStatistic->UNITsetFieldData('oxvalue', $sValue);
        $oStatistic->UNITsetFieldData('oxsomefield', $sValue);

        $this->assertEquals($sValue, $oStatistic->oxstatistics__oxvalue->value);
        $this->assertEquals(htmlentities($sValue, ENT_QUOTES), $oStatistic->oxstatistics__oxsomefield->value);
    }
}
