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

class testElement extends oxI18n
{

    public function isInList()
    {
        return $this->_blIsInList;
    }
}

class Unit_Core_oxlistTest extends OxidTestCase
{

    private $_oList;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oList = oxNew("oxlist");
        $this->_oList->init("oxtest", "oxtest");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxactions');
        parent::tearDown();
    }

    public function testSplArrayAccess()
    {

        $oTest = new stdClass();
        $oTest->sTest = "HELLO";

        $this->_oList[1] = $oTest;

        $oRes = $this->_oList[1];

        $this->assertEquals("HELLO", $oRes->sTest);
    }

    public function testSplIterator()
    {
        $oTest = new stdClass();
        $oTest->sTest = "HELLO";
        $this->_oList[] = $oTest;

        $oTest = new stdClass();
        $oTest->sTest = " AGAIN";
        $this->_oList[] = $oTest;

        $sTest = '';
        foreach ($this->_oList as $key => $oObject) {
            $sTest .= $oObject->sTest;
        }

        $this->assertEquals("HELLO AGAIN", $sTest);
    }

    public function testAssign()
    {
        $oTest = oxNew('oxList');
        $oTest[] = "A";
        $oTest[] = "B";
        $oTest[] = "C";
        $oTest[] = "D";

        $aTest = array(1, 2, 3);

        $oTest->assign($aTest);

        $i = 0;
        foreach ($oTest as $key => $value) {
            ++$i;
            $this->assertEquals($value, $aTest[$key]);
        }
        $this->assertEquals($i, 3);

    }

    public function testAssignArray()
    {
        $aData = array();
        $aData[] = array('oxid' => 1, 'oxtitle' => 'name1');
        $aData[] = array('oxid' => 2, 'oxtitle' => 'name2');
        $aData[] = array('oxid' => 3, 'oxtitle' => 'name3');
        $aData[] = array('oxid' => 4, 'oxtitle' => 'name4');


        $oList = new oxList;
        $oList->init('oxCategory');
        $oList->assignArray($aData);

        $i = 0;
        foreach ($oList as $value) {
            $this->assertEquals($aData[$i]['oxid'], $value->oxcategories__oxid->value);
            $this->assertEquals($aData[$i]['oxtitle'], $value->oxcategories__oxtitle->value);
            ++$i;
        }

        $this->assertEquals($i, 4);
    }

    public function testSplCount()
    {
        $oTest = new stdClass();
        $oTest->sTest = "HELLO";
        $this->_oList[] = $oTest;

        $oTest = new stdClass();
        $oTest->sTest = " AGAIN";
        $this->_oList[] = $oTest;

        $oTest = new stdClass();
        $oTest->sTest = " !";
        $this->_oList[] = $oTest;

        $this->assertEquals(3, count($this->_oList));
        $this->assertEquals(3, count($this->_oList->aList));
    }

    public function testClear()
    {
        $oTest = new stdClass();
        $oTest->sTest = "HELLO";
        $this->_oList[] = $oTest;

        $oTest = new stdClass();
        $oTest->sTest = " AGAIN";
        $this->_oList[] = $oTest;

        $this->_oList->clear();

        $this->assertEquals(0, count($this->_oList));
    }

    public function testOxidAsIndex()
    {
        $oTest = new stdClass();
        $oTest->oxtest__oxid = new oxField("123", oxField::T_RAW);
        $oTest->oxtest__oxany = new oxField('test', oxField::T_RAW);
        $this->_oList[] = $oTest;
        $this->assertTrue(isset($this->_oList["123"]));
        $this->assertFalse(isset($this->_oList[0]));
        $this->assertEquals($this->_oList["123"]->oxtest__oxany->value, 'test');
    }

    public function testSelectString()
    {

        $oAction = oxNew("oxbase");
        $oAction->init('oxactions');
        $oAction->setId('_test1');
        $oAction->oxactions__oxtitle = new oxField('action1', oxField::T_RAW);
        $oAction->save();

        $oAction = oxNew("oxbase");
        $oAction->init('oxactions');
        $oAction->setId('_test2');
        $oAction->oxactions__oxtitle = new oxField('action2', oxField::T_RAW);
        $oAction->save();

        $oAction = oxNew("oxbase");
        $oAction->init('oxactions');
        $oAction->blIsClonedAndKeptProperty = true;

        $oList = $this->getMock('oxlist', array('getBaseObject'));
        $oList->expects($this->once())->method('getBaseObject')->will($this->returnValue($oAction));
        $oList->init('oxactions');

        $oList->selectString('select * from oxactions where oxid like "\_%"');

        $this->assertEquals('2', count($oList));
        $this->assertEquals('_test1', $oList['_test1']->getId());
        $this->assertEquals('action1', $oList['_test1']->oxactions__oxtitle->value);
        $this->assertTrue($oList['_test1']->blIsClonedAndKeptProperty);

        $this->assertEquals('_test2', $oList['_test2']->getId());
        $this->assertEquals('action2', $oList['_test2']->oxactions__oxtitle->value);
        $this->assertTrue($oList['_test2']->blIsClonedAndKeptProperty);
    }

    public function testSelectStringIfLimitIsSet()
    {
        $oAction = oxNew("oxbase");
        $oAction->init('oxactions');
        $oAction->setId('_test1');
        $oAction->oxactions__oxtitle = new oxField('action1', oxField::T_RAW);
        $oAction->save();

        $oAction = oxNew("oxbase");
        $oAction->init('oxactions');
        $oAction->setId('_test2');
        $oAction->oxactions__oxtitle = new oxField('action2', oxField::T_RAW);
        $oAction->save();

        $oList = new oxlist('oxactions');
        $oList->setSqlLimit(1, 1);
        $oList->selectString('select * from oxactions where oxid like "\_%"');

        $this->assertEquals('1', count($oList));
    }

    public function testSelectStringEmpty()
    {
        $oList = oxNew("oxlist");
        $oList->init("oxBase", "oxactions");
        $oList->selectString("select * from oxactions where oxid = 'non existant' ");
        $this->assertEquals(0, $oList->count());
    }

    public function testContainsFieldValue()
    {
        $oTest = new stdClass();
        $oTest->oxtest__oxid = new oxField("123", oxField::T_RAW);
        $oTest->oxtest__oxany = new oxField('test', oxField::T_RAW);
        $this->_oList[] = $oTest;
        $this->assertTrue($this->_oList->containsFieldValue('test', "oxany"));
        $this->assertFalse($this->_oList->containsFieldValue('test', "oxid"));
        $this->assertFalse($this->_oList->containsFieldValue('test', "none"));
    }

    public function testGetList()
    {
        $oList = oxNew("oxcountrylist");
        $oList->getList();
        $this->assertEquals(5, count($oList));
        $this->assertEquals("DE", $oList["a7c40f631fc920687.20179984"]->oxcountry__oxisoalpha2->value);
    }

    public function testGetListReturns()
    {
        $oList = oxNew("oxcountrylist");
        $oReturn = $oList->getList();
        $this->assertEquals($oReturn, $oList);
    }

    public function testGetArray()
    {
        $oList = new oxlist();
        $oList->offsetSet('xxx', 'yyy');

        $this->assertEquals(array('xxx' => 'yyy'), $oList->getArray());
    }

    public function testOffsetGet()
    {
        $oList = new oxlist();
        $oList->offsetSet('xxx', 'yyy');

        $this->assertEquals('yyy', $oList->offsetGet('xxx'));
        $this->assertFalse($oList->offsetGet('yyy'));
    }

    public function testOffsetUnset()
    {
        $oList = new oxlist();
        $oList->offsetSet('xxx', 'yyy');
        $this->assertEquals('yyy', $oList->offsetGet('xxx'));
        $oList->offsetUnset('xxx');
        $this->assertFalse($oList->offsetGet('xxx'));
    }

    public function testArrayKeys()
    {
        $aArray = array('a' => 'a1',
                        'b' => 'b1',
                        'c' => 'c1',
                        'd' => 'd1');

        $oList = new oxlist();
        $oList->assign($aArray);

        $this->assertEquals(array_keys($aArray), $oList->arrayKeys());
    }

    public function testReverse()
    {
        $aArray = array('a' => 'a1', 'b' => 'b1');

        $oList = new oxlist();
        $oList->assign($aArray);

        $this->assertEquals($aArray = array('b' => 'b1', 'a' => 'a1'), $oList->reverse());
    }

    public function testSetsInListAttritbue()
    {

        $sQ = "select * from oxarticles limit 0,5";
        $oSubj = new oxList();
        $oSubj->init("testElement", "oxarticles");
        $oSubj->selectString($sQ);

        $oElement = new testElement();
        $this->assertFalse($oElement->isInList());

        foreach ($oSubj as $oElement) {
            $this->assertTrue($oElement->isInList());
        }
    }

    public function testAssignElement()
    {
        $aDbFields = array("field1" => "val1");
        $oListObjectMock = $this->getMock('oxBase', array('assign'));
        $oListObjectMock->expects($this->once())->method('assign')->with($aDbFields);

        $oSubj = $this->getProxyClass("oxList");
        $oSubj->UNITassignElement($oListObjectMock, $aDbFields);
    }

    /**
     * Testing oxList::_getFieldLongName()
     *
     * @return null
     */
    public function testGetFieldLongName()
    {
        $sFieldName = "testFieldNames";
        $sCoreTable = "testCoreTable";

        $oSubj = $this->getProxyClass("oxList");

        $oSubj->setNonPublicVar("_sCoreTable", "");
        $this->assertEquals($sFieldName, $oSubj->UNITgetFieldLongName($sFieldName));

        $oSubj->setNonPublicVar("_sCoreTable", $sCoreTable);
        $this->assertEquals($sCoreTable . "__" . $sFieldName, $oSubj->UNITgetFieldLongName($sFieldName));
    }

    public function testUnsetForeach()
    {
        $oList = new oxlist();
        $oList->offsetSet('k1', 'cnt4');
        $oList->offsetSet('k2', 'cnt3');
        $oList->offsetSet('k3', 'cnt2');
        $oList->offsetSet('k4', 'cnt1');

        $iTotal = count($oList);
        $this->assertEquals(4, $iTotal);
        foreach ($oList as $sKey => $sVal) {
            $this->assertEquals($iTotal, count($oList));
            $this->assertEquals('cnt' . $iTotal, $sVal);

            $iTotal--;
            unset($oList[$sKey]);
        }
    }

    /**
     * Testing oxList::unset() before foreach loop
     *
     * @return null
     */
    public function testUnsetBeforeForeach()
    {
        $oList = new oxlist();
        $oList->offsetSet('k1', 'cnt4');
        $oList->offsetSet('k2', 'cnt3');
        $oList->offsetSet('k3', 'cnt2');
        $oList->offsetSet('k4', 'cnt1');

        $oList->offsetUnset('k1');
        $iTotal = $oList->count();
        $iCount = 0;

        foreach ($oList as $sVal) {
            $iCount++;
        }
        $this->assertEquals($iTotal, $iCount);
    }

    public function testUnsetWhile()
    {
        $oList = new oxlist();
        $oList->offsetSet('k1', 'cnt4');
        $oList->offsetSet('k2', 'cnt3');
        $oList->offsetSet('k3', 'cnt2');
        $oList->offsetSet('k4', 'cnt1');

        $iTotal = count($oList);
        $this->assertEquals(4, $iTotal);
        reset($oList);
        while (list($sKey, $sVal) = each($oList->aList)) {
            $this->assertEquals($iTotal, count($oList));
            $this->assertEquals('cnt' . $iTotal, $sVal);

            $iTotal--;
            unset($oList[$sKey]);
        }
    }

    /**
     * Testing oxList::unset() before while loop
     *
     * @return null
     */
    public function testUnsetBeforeWhile()
    {
        $oList = new oxlist();
        $oList->offsetSet('k1', 'cnt4');
        $oList->offsetSet('k2', 'cnt3');
        $oList->offsetSet('k3', 'cnt2');
        $oList->offsetSet('k4', 'cnt1');

        $oList->offsetUnset('k1');
        $iTotal = $oList->count();
        $iCount = 0;

        $oList->rewind();
        while ($oList->valid()) {
            $iCount++;
            $oList->next();
        }
        $this->assertEquals($iTotal, $iCount);
    }

    /**
     * Testing oxList::rewind()
     *
     * @return null
     */
    public function testRewind()
    {
        $aArray = array(1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4');

        $oList = new oxlist();
        $oList->assign($aArray);
        foreach ($oList as $key => $val) {
            if ($key == 3) {
                break;
            }
        }
        $this->assertEquals(3, $oList->current());
        $oList->rewind();
        $this->assertEquals(1, $oList->current());
    }

    /**
     * Checks single element addition to oxList.
     */
    public function testAdd()
    {
        $oList = new oxList();
        $oSample = new oxListObject('table');
        $oList->add($oSample);
        $this->assertEquals($oSample, $oList->current());
    }
}