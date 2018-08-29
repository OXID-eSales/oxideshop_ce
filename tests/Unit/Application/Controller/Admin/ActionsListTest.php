<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Actions_List class
 */
class ActionsListTest extends \OxidTestCase
{

    /**
     * Actions_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();

        $this->assertEquals('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals(array('oxactions' => array('oxtitle' => 'asc')), $oView->getListSorting());
        $this->assertEquals('actions_list.tpl', $sTplName);
    }

    /**
     * Actions_List::Render() test case
     *
     * @return null
     */
    public function testPromotionsRender()
    {
        $this->setRequestParameter("displaytype", "testType");

        $oView = $this->getProxyClass("Actions_List");
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();

        $this->assertEquals('oxactions', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals(array('oxactions' => array('oxtitle' => 'asc')), $oView->getListSorting());
        $this->assertEquals('testType', $aViewData['displaytype']);
        $this->assertEquals('actions_list.tpl', $sTplName);
    }

    /**
     * Actions_List::_prepareWhereQuery() test case
     */
    public function testPrepareWhereQuery()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $iTime = time();
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{ return ' . $iTime . '; }');
        $sTable = getViewName("oxactions");
        $sNow = date('Y-m-d H:i:s', $iTime);

        $oView = oxNew('Actions_List');

        $sQ = " and $sTable.oxactivefrom < '$sNow' and $sTable.oxactiveto > '$sNow' ";
        $this->setRequestParameter('displaytype', 1);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));

        $sQ = " and $sTable.oxactivefrom > '$sNow' ";
        $this->setRequestParameter('displaytype', 2);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));

        $sQ = " and $sTable.oxactiveto < '$sNow' and $sTable.oxactiveto != '0000-00-00 00:00:00' ";
        $this->setRequestParameter('displaytype', 3);
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery(array(), ""));
    }
}
