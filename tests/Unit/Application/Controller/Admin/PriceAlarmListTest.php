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
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \stdClass;

/**
 * Tests for PriceAlarm_List class
 */
class PriceAlarmListTest extends \OxidTestCase
{

    /**
     * PriceAlarm_List::BuildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectString()
    {
        $sViewName = getViewName("oxpricealarm");
        $sArtViewName = getViewName("oxarticles");

        $sSql = "select {$sViewName}.*, {$sArtViewName}.oxtitle AS articletitle, ";
        $sSql .= "oxuser.oxlname as userlname, oxuser.oxfname as userfname ";
        $sSql .= "from {$sViewName} ";
        $sSql .= "left join {$sArtViewName} on {$sArtViewName}.oxid = {$sViewName}.oxartid ";
        $sSql .= "left join oxuser on oxuser.oxid = {$sViewName}.oxuserid WHERE 1 ";

        // testing..
        $oView = oxNew('PriceAlarm_List');
        $this->assertEquals($sSql, $oView->UNITbuildSelectString(new stdClass()));
    }

    /**
     * PriceAlarm_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('PriceAlarm_List');
        $this->assertEquals('pricealarm_list.tpl', $oView->render());
    }

    /**
     * PriceAlarm_List::BuildWhere() test case
     *
     * @return null
     */
    public function testBuildWhere()
    {
        $this->setRequestParameter('where', array("oxpricealarm" => array("oxprice" => 15), "oxarticles" => array("oxprice" => 15)));

        $sViewName = getViewName("oxpricealarm");
        $sArtViewName = getViewName("oxarticles");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PriceAlarmList::class, array("_authorize"));
        $oView->expects($this->any())->method('_authorize')->will($this->returnValue(true));
        $oView->init();

        $queryWhereParts = $oView->buildWhere();
        $this->assertEquals('%15%', $queryWhereParts[$sViewName . '.oxprice']);
        $this->assertEquals('%15%', $queryWhereParts[$sArtViewName . '.oxprice']);
    }
}
