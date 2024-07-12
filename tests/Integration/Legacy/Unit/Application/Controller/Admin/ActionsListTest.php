<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxTestModules;

/**
 * Tests for Actions_List class
 */
class ActionsListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Actions_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $oView->getViewData();

        $this->assertSame('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertSame(['oxactions' => ['oxtitle' => 'asc']], $oView->getListSorting());
        $this->assertSame('actions_list', $sTplName);
    }

    /**
     * Actions_List::Render() test case
     */
    public function testPromotionsRender()
    {
        $this->setRequestParameter("displaytype", "testType");

        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();

        $this->assertSame('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertSame(['oxactions' => ['oxtitle' => 'asc']], $oView->getListSorting());
        $this->assertSame('testType', $aViewData['displaytype']);
        $this->assertSame('actions_list', $sTplName);
    }

    /**
     * Actions_List::prepareWhereQuery() test case
     */
    public function testPrepareWhereQuery()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $iTime = time();
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{ return ' . $iTime . '; }');
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName("oxactions");
        $sNow = date('Y-m-d H:i:s', $iTime);

        $oView = oxNew('Actions_List');

        $sQ = sprintf(" and %s.oxactivefrom < '%s' and %s.oxactiveto > '%s' ", $sTable, $sNow, $sTable, $sNow);
        $this->setRequestParameter('displaytype', 1);
        $this->assertSame($sQ, $oView->prepareWhereQuery([], ""));

        $sQ = sprintf(" and %s.oxactivefrom > '%s' ", $sTable, $sNow);
        $this->setRequestParameter('displaytype', 2);
        $this->assertSame($sQ, $oView->prepareWhereQuery([], ""));

        $sQ = sprintf(" and %s.oxactiveto < '%s' and %s.oxactiveto != '0000-00-00 00:00:00' ", $sTable, $sNow, $sTable);
        $this->setRequestParameter('displaytype', 3);
        $this->assertSame($sQ, $oView->prepareWhereQuery([], ""));
    }
}
