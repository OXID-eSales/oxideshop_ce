<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\SelectList;
use \Exception;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for SelectList_Main class
 */
class SelectListMainTest extends \OxidTestCase
{

    /**
     * SelectList_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('SelectList_Main');
        $this->assertEquals('selectlist_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof selectlist);
    }

    /**
     * Statistic_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('SelectList_Main');
        $this->assertEquals('selectlist_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * SelectList_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxselectlist', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction('oxselectlist', 'isDerived', '{ return false; }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('SelectList_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in SelectList_Main::save()");

            return;
        }

        $this->fail("error in SelectList_Main::save()");
    }

    /**
     * SelectList_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxselectlist', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction('oxselectlist', 'isDerived', '{ return false; }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('SelectList_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in SelectList_Main::saveinnlang()");

            return;
        }

        $this->fail("error in SelectList_Main::saveinnlang()");
    }

    /**
     * SelectList_Main::DelFields() test case
     */
    public function testDelFields()
    {
        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxselectlist', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass(); $oField1->name = "testField1";$oField2 = new stdClass(); $oField2->name = "testField2"; return array( $oField1, $oField2 ); }');

        $this->setRequestParameter("aFields", ["testField1", "testField2"]);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMain::class, ["parseFieldName", "save"]);
        $oView
            ->method('parseFieldName')
            ->willReturnOnConsecutiveCalls(
                'testField2',
                'testField1'
            );
        $oView->expects($this->atLeastOnce())->method('save');
        $oView->delFields();
    }

    /**
     * SelectList_Main::AddField() test case
     */
    public function testAddFieldNothingToAdd()
    {
        $this->setRequestParameter("sAddField", null);
        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');

        // testing..
        $oView = oxNew('SelectList_Main');
        $this->assertNull($oView->addField());
        $this->assertEquals(-1, oxRegistry::getSession()->getVariable("iErrorCode"));
    }

    /**
     * SelectList_Main::AddField() test case
     */
    public function testAddFieldRearangeFieldReturnsTrue()
    {
        $this->setRequestParameter("sAddField", "testField");
        $this->setRequestParameter("aFields", ["testField"]);
        $this->setRequestParameter("sAddFieldPos", 1);

        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMain::class, ["rearrangeFields", "save"]);
        $oView->expects($this->once())->method('rearrangeFields')->will($this->returnValue(true));
        $oView->expects($this->never())->method('save');
        $this->assertNull($oView->addField());
    }

    /**
     * SelectList_Main::AddField() test case
     */
    public function testAddFieldRearangeField()
    {
        $this->setRequestParameter("sAddField", "testField");
        $this->setRequestParameter("aFields", ["testField"]);
        $this->setRequestParameter("sAddFieldPos", 1);

        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMain::class, ["rearrangeFields", "save"]);
        $oView->expects($this->once())->method('rearrangeFields')->will($this->returnValue(false));
        $oView->expects($this->once())->method('save');
        $this->assertNull($oView->addField());
    }

    /**
     * SelectList_Main::ChangeField() test case
     */
    public function testChangeFieldNothingToChange()
    {
        $this->setRequestParameter("sAddField", null);

        // testing..
        $oView = oxNew('SelectList_Main');
        $this->assertNull($oView->changeField());
        $this->assertEquals(-1, oxRegistry::getSession()->getVariable("iErrorCode"));
    }

    /**
     * SelectList_Main::ChangeField() test case
     */
    public function testChangeFieldRearagneFieldsReturnsTrue()
    {
        $this->setRequestParameter("sAddField", "testField");
        $this->setRequestParameter("aFields", ["testField"]);
        $this->setRequestParameter("sAddFieldPos", 1);

        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMain::class, ["parseFieldName", "rearrangeFields", "save"]);
        $oView->expects($this->once())->method('parseFieldName')->will($this->returnValue("testField1"));
        $oView->expects($this->once())->method('rearrangeFields')->will($this->returnValue(true));
        $oView->expects($this->never())->method('save');
        $oView->changeField();
    }

    /**
     * SelectList_Main::ChangeField() test case
     */
    public function testChangeField()
    {
        $this->setRequestParameter("sAddField", "testField");
        $this->setRequestParameter("aFields", ["testField"]);
        $this->setRequestParameter("sAddFieldPos", 1);

        oxTestModules::addFunction('oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMain::class, ["parseFieldName", "rearrangeFields", "save"]);
        $oView->expects($this->once())->method('parseFieldName')->will($this->returnValue("testField1"));
        $oView->expects($this->once())->method('rearrangeFields')->will($this->returnValue(false));
        $oView->expects($this->once())->method('save');
        $oView->changeField();
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsFieldArrayIsEmpty()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", null);
        $this->assertTrue($oView->rearrangeFields("test", 0));
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsPosIsBelowZero()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", [1]);
        $this->assertTrue($oView->rearrangeFields("test", -1));
        $this->assertEquals(-2, oxRegistry::getSession()->getVariable("iErrorCode"));
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsUnknownField()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", [1]);
        $this->assertTrue($oView->rearrangeFields("test", 1));
        $this->assertEquals(-2, oxRegistry::getSession()->getVariable("iErrorCode"));
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsNoChangesWereMade()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", [1, 2]);
        $this->assertFalse($oView->rearrangeFields(1, 1));
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsCurrentPosIsLowerThatPassed()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", [1, 2, 3]);
        $this->assertFalse($oView->rearrangeFields(1, 2));
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     */
    public function testRearrangeFieldsCurrentPosIsHigherThatPassed()
    {
        // defining parameters
        $oView = $this->getProxyClass("SelectList_Main");
        $oView->setNonPublicVar("aFieldArray", [1, 2, 3]);
        $this->assertFalse($oView->rearrangeFields(2, 0));
    }


    /**
     * SelectList_Main::ParseFieldName() test case
     */
    public function testParseFieldName()
    {
        $oView = oxNew('SelectList_Main');
        $this->assertEquals("bbb", $oView->parseFieldName("aaa__@@bbb"));
    }
}
