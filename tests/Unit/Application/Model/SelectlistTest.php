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

use \stdclass;
use \oxSelection;
use \oxField;
use \oxDb;

/**
 * Testing oxselectlist class
 */
class SelectlistTest extends \OxidTestCase
{
    /**
     * Initialize the fixture add some users.
     *
     * @return null
     */
    protected function setUp()
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

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
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
        $aSelList[0]->name = '&amp;amp;&amp;test1, 10';
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
        $this->assertEquals("&amp;amp;&amp;test1, 10 +14,33 CHF", $aSelList[0]->name);
        $this->assertEquals("test2, 10 +14,33 CHF", $aSelList[1]->name);
        $this->assertEquals("test3, 10 +14,33 CHF", $aSelList[2]->name);
    }

    /**
     * oxSelectList::setActiveSelectionByIndex() test case
     *
     * @return null
     */
    public function testSetActiveSelectionByIndex()
    {
        $oSel0 = $this->getMock("oxSelection", array("setActiveState"), array(), '', false);
        $oSel0->expects($this->once())->method('setActiveState')->with($this->equalTo(false));

        $oSel1 = $this->getMock("oxSelection", array("setActiveState"), array(), '', false);
        $oSel1->expects($this->once())->method('setActiveState')->with($this->equalTo(false));

        $oSel2 = $this->getMock("oxSelection", array("setActiveState"), array(), '', false);
        $oSel2->expects($this->once())->method('setActiveState')->with($this->equalTo(true));

        $aSelections = array($oSel0, $oSel1, $oSel2);

        $oSelectList = $this->getMock("oxselectlist", array("getSelections"));
        $oSelectList->expects($this->once())->method('getSelections')->will($this->returnValue($aSelections));
        $oSelectList->setActiveSelectionByIndex(2);

        $this->assertEquals($oSel2, $oSelectList->getActiveSelection());
    }

    /**
     * oxSelectList::getActiveSelection() test case
     *
     * @return null
     */
    public function testGetActiveSelection()
    {
        $aSelections = array("oxSel0", "oxSel1", "oxSel2");

        $oSelectList = $this->getMock("oxselectlist", array("getSelections"));
        $oSelectList->expects($this->once())->method('getSelections')->will($this->returnValue($aSelections));
        $this->assertEquals("oxSel0", $oSelectList->getActiveSelection());
    }

    /**
     * oxSelectList::getSelections() test case
     *
     * @return null
     */
    public function testGetSelections()
    {
        // valdesc is not set
        $oSelectList = oxNew('oxselectlist');
        $this->assertNull($oSelectList->getSelections());

        $this->setRequestParameter('cur', 2);
        $aSelections = array(oxNew('oxSelection', "test1, 10", 0, false, true),
                             oxNew('oxSelection', "test2, 10", 1, false, false),
                             oxNew('oxSelection', "test3', 10", 2, false, false),
        );

        // valdesc is set
        $oSelectList = oxNew('oxselectlist');
        $oSelectList->oxselectlist__oxvaldesc = new oxField('test1, 10!P!10__@@test2, 10!P!10__@@test3\', 10!P!10__@@');

        $this->assertEquals($aSelections, $oSelectList->getSelections());
    }

    /**
     * oxSelectList::getLabel() test case
     *
     * @return null
     */
    public function testGetLabel()
    {
        $oSelectList = oxNew('oxselectlist');
        $oSelectList->oxselectlist__oxtitle = new oxField("test");
        $this->assertEquals("test", $oSelectList->getLabel());

    }

    /**
     * oxSelectList::setVat() & oxSelectList::getVat() test case
     *
     * @return null
     */
    public function testSetVatAndGetVat()
    {
        // no VAT set
        $oSelectList = oxNew('oxselectlist');
        $this->assertNull($oSelectList->getVat());

        // setting and checking VAT
        $oSelectList->setVat(123);
        $this->assertEquals(123, $oSelectList->getVat());
    }
}
