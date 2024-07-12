<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxDb;
use oxField;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Unit\FieldTestingTrait;
use stdclass;

class SelectlistTest extends \PHPUnit\Framework\TestCase
{
    use FieldTestingTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $myDB = oxDb::getDB();
        $myConfig = $this->getConfig();

        $sShopId = $myConfig->getBaseShopId();
        $sVal = '&amp;&test1, 10!P!10__@@test2, 10!P!10__@@test3, 10!P!10__@@';

        $sQ = 'insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ("oxsellisttest", "' . $sShopId . '", "oxsellisttest", "oxsellisttest", "' . $sVal . '")';

        $this->addToDatabase($sQ, 'oxselectlist');

        $sQ = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("oxsellisttest", "oxsellisttest", "oxsellisttest", 1) ';
        $myDB->Execute($sQ);
    }

    protected function tearDown(): void
    {
        $myDB = oxDb::getDB();

        $sQ = 'delete from oxselectlist where oxid = "oxsellisttest" ';
        $myDB->Execute($sQ);

        $sQ = 'delete from oxobject2selectlist where oxselnid = "oxsellisttest" ';
        $myDB->Execute($sQ);

        parent::tearDown();
    }

    /**
     * Checking deletion and assign
     */
    public function testDelete()
    {
        $myDB = oxDb::getDB();

        $oSelList = oxNew('oxselectlist');
        $oSelList->load('oxsellisttest');
        $oSelList->delete();

        $sQ = 'select count(*) from oxselectlist where oxid = "oxsellisttest" ';
        if ($myDB->getOne($sQ)) {
            $this->fail('records from oxselectlist are not deleted');
        }

        $sQ = 'select count(*) from oxobject2selectlist where oxselnid = "oxsellisttest" ';
        if ($myDB->getOne($sQ)) {
            $this->fail('records from oxobject2selectlist are not deleted');
        }
    }

    public function testGetFieldList()
    {
        $aSelList[0] = new stdclass();
        $aSelList[0]->name = $this->encode('&amp;&test1, 10');
        $aSelList[0]->value = null;

        $aSelList[1] = new stdclass();
        $aSelList[1]->name = 'test2, 10';
        $aSelList[1]->value = null;

        $aSelList[2] = new stdclass();
        $aSelList[2]->name = 'test3, 10';
        $aSelList[2]->value = null;

        $oSelList = oxNew('oxselectlist');
        $oSelList->Load('oxsellisttest');

        // checking loaded data
        $this->assertEquals($aSelList, $oSelList->getFieldList());
    }

    public function testAssignWithOtherLang()
    {
        $oSelectList = oxNew('oxselectlist');
        $oSelectList->setLanguage(1);
        $oSelectList->load('oxsellisttest');

        $aParams['oxtitle'] = 'Test_selectlist';
        $aParams['oxvaldesc'] = 'Test_1';

        $oSelectList->assign($aParams);
        $oSelectList->save();

        $this->assertEquals($oSelectList->oxselectlist__oxvaldesc->value, 'Test_1');
        $this->assertEquals($oSelectList->oxselectlist__oxtitle->value, 'Test_selectlist');
    }

    public function testDeleteNotExistingSelect()
    {
        $oSelectList = oxNew('oxselectlist');
        $this->assertFalse($oSelectList->delete("111111"));
    }

    /*
     * Check if getFieldList() stips tags from currency name
     */
    public function testGetFieldListStripsTagsFromCurrency()
    {
        $this->setRequestParameter('cur', 2);
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', 1);
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', 1);

        $oSelList = oxNew('oxselectlist');
        $oSelList->load('oxsellisttest');

        $aSelList = $oSelList->getFieldList();

        // checking loaded data
        $this->assertSame($this->encode('&amp;&test1, 10') . ' +14,33 CHF', $aSelList[0]->name);
        $this->assertSame("test2, 10 +14,33 CHF", $aSelList[1]->name);
        $this->assertSame("test3, 10 +14,33 CHF", $aSelList[2]->name);
    }

    /**
     * oxSelectList::setActiveSelectionByIndex() test case
     */
    public function testSetActiveSelectionByIndex()
    {
        $oSel0 = $this->getMock(\OxidEsales\Eshop\Application\Model\Selection::class, ["setActiveState"], [], '', false);
        $oSel0->expects($this->once())->method('setActiveState')->with(false);

        $oSel1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Selection::class, ["setActiveState"], [], '', false);
        $oSel1->expects($this->once())->method('setActiveState')->with(false);

        $oSel2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Selection::class, ["setActiveState"], [], '', false);
        $oSel2->expects($this->once())->method('setActiveState')->with(true);

        $aSelections = [$oSel0, $oSel1, $oSel2];

        $oSelectList = $this->getMock(\OxidEsales\Eshop\Application\Model\SelectList::class, ["getSelections"]);
        $oSelectList->expects($this->once())->method('getSelections')->willReturn($aSelections);
        $oSelectList->setActiveSelectionByIndex(2);

        $this->assertEquals($oSel2, $oSelectList->getActiveSelection());
    }

    /**
     * oxSelectList::getActiveSelection() test case
     */
    public function testGetActiveSelection()
    {
        $aSelections = ["oxSel0", "oxSel1", "oxSel2"];

        $oSelectList = $this->getMock(\OxidEsales\Eshop\Application\Model\SelectList::class, ["getSelections"]);
        $oSelectList->expects($this->once())->method('getSelections')->willReturn($aSelections);
        $this->assertSame("oxSel0", $oSelectList->getActiveSelection());
    }

    /**
     * oxSelectList::getSelections() test case
     */
    public function testGetSelections()
    {
        // valdesc is not set
        $oSelectList = oxNew('oxselectlist');
        $this->assertNull($oSelectList->getSelections());

        $this->setRequestParameter('cur', 2);
        $aSelections = [oxNew('oxSelection', "test1, 10", 0, false, true), oxNew('oxSelection', "test2, 10", 1, false, false), oxNew('oxSelection', "test3', 10", 2, false, false)];

        // valdesc is set
        $oSelectList = oxNew('oxselectlist');
        $oSelectList->oxselectlist__oxvaldesc = new oxField("test1, 10!P!10__@@test2, 10!P!10__@@test3', 10!P!10__@@");

        $this->assertEquals($aSelections, $oSelectList->getSelections());
    }

    /**
     * oxSelectList::getLabel() test case
     */
    public function testGetLabel()
    {
        $oSelectList = oxNew('oxselectlist');
        $oSelectList->oxselectlist__oxtitle = new oxField("test");
        $this->assertSame("test", $oSelectList->getLabel());
    }

    /**
     * oxSelectList::setVat() & oxSelectList::getVat() test case
     */
    public function testSetVatAndGetVat()
    {
        // no VAT set
        $oSelectList = oxNew('oxselectlist');
        $this->assertNull($oSelectList->getVat());

        // setting and checking VAT
        $oSelectList->setVat(123);
        $this->assertSame(123, $oSelectList->getVat());
    }
}
