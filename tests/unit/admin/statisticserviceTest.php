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
 * Tests for Statistic_Service class
 */
class Unit_Admin_StatisticServiceTest extends OxidTestCase
{

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getProxyClass("Statistic_Service");

        $this->assertEquals('statistic_service.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iLogCount']));
    }

    /**
     * Statistic_Service::cleanup() test case
     *
     * @return null
     */
    public function testCleanup()
    {
        // testing..
        $iTimeFrame = "62";
        modConfig::setRequestParameter("timeframe", $iTimeFrame);
        $dNow = time();
        $sInsertFrom = date("Y-m-d H:i:s", mktime(date("H", $dNow), date("i", $dNow), date("s", $dNow), date("m", $dNow), date("d", $dNow) - 186, date("Y", $dNow)));
        $sDeleteFrom = date("Y-m-d H:i:s", mktime(date("H", $dNow), date("i", $dNow), date("s", $dNow), date("m", $dNow), date("d", $dNow) - $iTimeFrame, date("Y", $dNow)));
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxlogs (oxtime) value (" . $oDb->quote($sInsertFrom) . ")");
        $iCnt = $oDb->getOne("select count(*) from oxlogs where oxtime < " . $oDb->quote($sDeleteFrom));

        $oView = new Statistic_Service();
        $oView->cleanup();

        $oDb = oxDb::getDb();
        $iCnt = $oDb->getOne("select count(*) from oxlogs where oxtime < " . $oDb->quote($sDeleteFrom));
        $this->assertEquals(0, $iCnt);
    }
}
