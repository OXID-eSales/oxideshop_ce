<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
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
     */
    public function testLoadFinishedByCount()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');

        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            sprintf('select * from %s where oxtype=2 and oxactive=1 and oxshopid=\'', $sView) . $sShopId . "' and oxactiveto>0 and oxactiveto < '{$sNow}'
               (user group filter)
               order by oxactiveto desc, oxactivefrom desc limit 5"
        )->will($this->evalFunction('{$invocation->getObject()->assign(array("asd", "dsa", "aaa"));}'));
        $oL->loadFinishedByCount(5);
        $this->assertEquals(["asd", "dsa", "aaa"], $oL->getArray());
        $this->assertEquals([2, 1, 0], array_keys($oL->getArray()));
    }

    /**
     * oxActionList::loadFinishedByTimespan() test case
     * test the load of last finished promotions after given timespan
     */
    public function testLoadFinishedByTimespan()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $sDateFrom = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 50);
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');

        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            sprintf('select * from %s where oxtype=2 and oxactive=1 and oxshopid=\'', $sView) . $sShopId . "' and oxactiveto < '{$sNow}' and oxactiveto > '{$sDateFrom}'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadFinishedByTimespan(50);
    }

    /**
     * oxActionList::loadCurrent() test case
     * test the load of current promotions
     */
    public function testLoadCurrent()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');

        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            sprintf('select * from %s where oxtype=2 and oxactive=1 and oxshopid=\'', $sView) . $sShopId . "' and (oxactiveto > '{$sNow}' or oxactiveto=0) and oxactivefrom != 0 and oxactivefrom < '{$sNow}'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadCurrent(50);
    }

    /**
     * oxActionList::loadFutureByCount() test case
     * test the loads of next not yet started promotions by count
     */
    public function testLoadFutureByCount()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');

        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            sprintf('select * from %s where oxtype=2 and oxactive=1 and oxshopid=\'', $sView) . $sShopId . "' and (oxactiveto > '{$sNow}' or oxactiveto=0) and oxactivefrom > '{$sNow}'
               (user group filter)
               order by oxactiveto, oxactivefrom limit 50"
        );
        $oL->loadFutureByCount(50);
    }

    /**
     * oxActionList::loadFutureByTimespan() test case
     * test the loads of next not yet started promotions before the given timespan
     */
    public function testLoadFutureByTimespan()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $sFut = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 50));
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');

        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')->with(
            sprintf('select * from %s where oxtype=2 and oxactive=1 and oxshopid=\'', $sView) . $sShopId . "' and (oxactiveto > '{$sNow}' or oxactiveto=0) and oxactivefrom > '{$sNow}' and oxactivefrom < '{$sFut}'
               (user group filter)
               order by oxactiveto, oxactivefrom"
        );
        $oL->loadFutureByTimespan(50);
    }

    /**
     * oxActionList::getUserGroupFilter() test case
     * test if part of user group filter query returns correctly.
     */
    public function testGetUserGroupFilter()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxactions');
        $sGroupTable = $tableViewNameGenerator->getViewName('oxgroups');

        $sGroupSql = sprintf('EXISTS(select oxobject2action.oxid from oxobject2action where oxobject2action.oxactionid=%s.OXID and oxobject2action.oxclass=\'oxgroups\' and oxobject2action.OXOBJECTID in (', $sTable) . implode(', ', ["'id1'", "'id2'", "'id3'"]) . ") )";
        $sQ .= " and (
                if(EXISTS(select 1 from oxobject2action, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2action.oxobjectid and oxobject2action.oxactionid={$sTable}.OXID and oxobject2action.oxclass='oxgroups' LIMIT 1),
                    {$sGroupSql},
                    1)
            ) ";

        $oGroup1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Groups::class, ["getId"]);
        $oGroup1->expects($this->once())->method('getId')->will($this->returnValue("id1"));

        $oGroup2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Groups::class, ["getId"]);
        $oGroup2->expects($this->once())->method('getId')->will($this->returnValue("id2"));

        $oGroup3 = $this->getMock(\OxidEsales\Eshop\Application\Model\Groups::class, ["getId"]);
        $oGroup3->expects($this->once())->method('getId')->will($this->returnValue("id3"));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getUserGroups"]);
        $oUser->expects($this->once())->method('getUserGroups')->will($this->returnValue([$oGroup1, $oGroup2, $oGroup3]));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ["getUser"]);
        $oList->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals($sQ, $oList->getUserGroupFilter());
    }

    /**
     * oxActionList::getUserGroupFilter() test case
     * test if part of user group filter query returns correctly without user object.
     */
    public function testGetUserGroupFilterNoUser()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxactions');
        $sGroupTable = $tableViewNameGenerator->getViewName('oxgroups');

        $sGroupSql = '0';
        $sQ .= " and (
                if(EXISTS(select 1 from oxobject2action, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2action.oxobjectid and oxobject2action.oxactionid={$sTable}.OXID and oxobject2action.oxclass='oxgroups' LIMIT 1),
                    {$sGroupSql},
                    1)
            ) ";

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ["getUser"]);
        $oList->expects($this->once())->method('getUser')->will($this->returnValue(null));

        $this->assertEquals($sQ, $oList->getUserGroupFilter());
    }

    /**
     * Data provider for testAreAnyActivePromotions.
     *
     * @return array
     */
    public function testAreAnyActivePromotionsDataProvider()
    {
        return [['1', true], ['', false]];
    }

    /**
     * oxActionList::areAnyActivePromotions() test case
     * test if return value is in the true case "true" and the other way around.
     *
     * @dataProvider testAreAnyActivePromotionsDataProvider
     */
    public function testAreAnyPromotionsActive($response, $expected)
    {
        $actionListMock = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['fetchExistsActivePromotion']);
        $actionListMock->expects($this->any())->method('fetchExistsActivePromotion')->willReturn($response);

        $this->assertEquals($expected, $actionListMock->areAnyActivePromotions());
    }

    /**
     * general test
     */
    public function testLoadBanners()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getRequestTime', '{return ' . (ceil(time() / 300) * 300) . ';}');
        $sNow = (date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getRequestTime()));
        $sShopId = $this->getConfig()->getShopId();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions');
        $oL = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['selectString', 'getUserGroupFilter']);
        $oL->expects($this->once())->method('getUserGroupFilter')->will($this->returnValue('(user group filter)'));
        $oL->expects($this->once())->method('selectString')
            ->with(sprintf('select * from %s where oxtype=3 and  (   %s.oxactive = 1  or  ( %s.oxactivefrom < \'%s\' and %s.oxactiveto > \'%s\' ) )  and oxshopid=\'%s\' (user group filter) order by oxsort', $sView, $sView, $sView, $sNow, $sView, $sNow, $sShopId));

        $oL->loadBanners();
    }
}
