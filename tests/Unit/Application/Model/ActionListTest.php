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

use \oxDb;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use \oxRegistry;
use \oxTestModules;
use \oxActionList;

/**
 * Tests for Actions_List class
 */
class ActionListTest extends \OxidTestCase
{

    /**
     * oxActionList::loadFinishedByCount() test case
     * test if the actions will load in right sequence.
     *
     * @return null
     */
    public function testLoadFinishedByCount()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');

        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            "select * from $sView where oxtype=2 and oxactive=1 and oxshopid='" . $sShopId . "' and oxactiveto>0 and oxactiveto < '$sNow'
               (user group filter)
               order by oxactiveto desc, oxactivefrom desc limit 5"
        )->will($this->evalFunction('{$invocation->object->assign(array("asd", "dsa", "aaa"));}'));
        $oL->loadFinishedByCount(5);
        $this->assertEquals(array("asd", "dsa", "aaa"), $oL->getArray());
        $this->assertEquals(array(2, 1, 0), array_keys($oL->getArray()));
    }

    /**
     * oxActionList::loadFinishedByTimespan() test case
     * test the load of last finished promotions after given timespan
     *
     * @return null
     */
    public function testLoadFinishedByTimespan()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $sDateFrom = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() - 50);
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');

        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            "select * from $sView where oxtype=2 and oxactive=1 and oxshopid='" . $sShopId . "' and oxactiveto < '$sNow' and oxactiveto > '$sDateFrom'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadFinishedByTimespan(50);
    }

    /**
     * oxActionList::loadCurrent() test case
     * test the load of current promotions
     *
     * @return null
     */
    public function testLoadCurrent()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');

        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            "select * from $sView where oxtype=2 and oxactive=1 and oxshopid='" . $sShopId . "' and (oxactiveto > '$sNow' or oxactiveto=0) and oxactivefrom != 0 and oxactivefrom < '$sNow'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadCurrent(50);
    }

    /**
     * oxActionList::loadFutureByCount() test case
     * test the loads of next not yet started promotions by count
     *
     * @return null
     */
    public function testLoadFutureByCount()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');

        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            "select * from $sView where oxtype=2 and oxactive=1 and oxshopid='" . $sShopId . "' and (oxactiveto > '$sNow' or oxactiveto=0) and oxactivefrom > '$sNow'
               (user group filter)
               order by oxactiveto, oxactivefrom limit 50"
        );
        $oL->loadFutureByCount(50);
    }

    /**
     * oxActionList::loadFutureByTimespan() test case
     * test the loads of next not yet started promotions before the given timespan
     *
     * @return null
     */
    public function testLoadFutureByTimespan()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sFut = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() + 50));
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');

        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            "select * from $sView where oxtype=2 and oxactive=1 and oxshopid='" . $sShopId . "' and (oxactiveto > '$sNow' or oxactiveto=0) and oxactivefrom > '$sNow' and oxactivefrom < '$sFut'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadFutureByTimespan(50);
    }

    /**
     * oxActionList::getUserGroupFilter() test case
     * test if part of user group filter query returns correctly.
     *
     * @return null
     */
    public function testGetUserGroupFilter()
    {
        $sTable = getViewName('oxactions');
        $sGroupTable = getViewName('oxgroups');

        $sGroupSql = "EXISTS(select oxobject2action.oxid from oxobject2action where oxobject2action.oxactionid=$sTable.OXID and oxobject2action.oxclass='oxgroups' and oxobject2action.OXOBJECTID in (" . implode(', ', array("'id1'", "'id2'", "'id3'")) . ") )";
        $sQ .= " and (
            select
                if(EXISTS(select 1 from oxobject2action, $sGroupTable where $sGroupTable.oxid=oxobject2action.oxobjectid and oxobject2action.oxactionid=$sTable.OXID and oxobject2action.oxclass='oxgroups' LIMIT 1),
                    $sGroupSql,
                    1)
            ) ";

        $oGroup1 = $this->getMock("oxGroups", array("getId"));
        $oGroup1->expects($this->once())->method('getId')->will($this->returnValue("id1"));

        $oGroup2 = $this->getMock("oxGroups", array("getId"));
        $oGroup2->expects($this->once())->method('getId')->will($this->returnValue("id2"));

        $oGroup3 = $this->getMock("oxGroups", array("getId"));
        $oGroup3->expects($this->once())->method('getId')->will($this->returnValue("id3"));

        $oUser = $this->getMock("oxUser", array("getUserGroups"));
        $oUser->expects($this->once())->method('getUserGroups')->will($this->returnValue(array($oGroup1, $oGroup2, $oGroup3)));

        $oList = $this->getMock("oxActionList", array("getUser"));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals($sQ, $oList->UNITgetUserGroupFilter());
    }

    /**
     * oxActionList::getUserGroupFilter() test case
     * test if part of user group filter query returns correctly without user object.
     *
     * @return null
     */
    public function testGetUserGroupFilterNoUser()
    {
        $sTable = getViewName('oxactions');
        $sGroupTable = getViewName('oxgroups');

        $sGroupSql = '0';
        $sQ .= " and (
            select
                if(EXISTS(select 1 from oxobject2action, $sGroupTable where $sGroupTable.oxid=oxobject2action.oxobjectid and oxobject2action.oxactionid=$sTable.OXID and oxobject2action.oxclass='oxgroups' LIMIT 1),
                    $sGroupSql,
                    1)
            ) ";

        $oList = $this->getMock("oxActionList", array("getUser"));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue(null));

        $this->assertEquals($sQ, $oList->UNITgetUserGroupFilter());
    }

    /**
     * Data provider for testAreAnyActivePromotions.
     *
     * @return array
     */
    public function testAreAnyActivePromotionsDataProvider()
    {
        return array(
            array('1', true),
            array('', false)
        );
    }

    /**
     * oxActionList::areAnyActivePromotions() test case
     * test if return value is in the true case "true" and the other way around.
     *
     * @dataProvider testAreAnyActivePromotionsDataProvider
     */
    public function testAreAnyPromotionsActive($response, $expected)
    {
        $actionListMock = $this->getMock('oxActionList', array('fetchExistsActivePromotion'));
        $actionListMock->expects($this->any())->method('fetchExistsActivePromotion')->willReturn($response);

        $this->assertEquals($expected, $actionListMock->areAnyActivePromotions());
    }

    /**
     * general test
     *
     * @return null
     */
    public function testLoadBanners()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getRequestTime', '{return ' . (ceil(time() / 300) * 300) . ';}');
        $sNow = (date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getRequestTime()));
        $sShopId = $this->getConfig()->getShopId();

        $sView = getViewName('oxactions');
        $oL = $this->getMock('oxActionList', array('selectString', '_getUserGroupFilter'));
        $oL->expects($this->once())->method('_getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')
            ->with("select * from $sView where oxtype=3 and  (   $sView.oxactive = 1  or  ( $sView.oxactivefrom < '$sNow' and $sView.oxactiveto > '$sNow' ) )  and oxshopid='$sShopId' (user group filter) order by oxsort");

        $oL->loadBanners();
    }

}
