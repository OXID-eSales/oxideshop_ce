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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for SelectList_Main class
 */
class Unit_Admin_SelectListMainTest extends OxidTestCase
{
    /**
     * SelectList_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        $oView = new SelectList_Main();
        $this->assertEquals( 'selectlist_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['edit'] ) );
        $this->assertTrue( $aViewData['edit'] instanceof oxselectlist );
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setParameter( "oxid", "-1" );

        // testing..
        $oView = new SelectList_Main();
        $this->assertEquals( 'selectlist_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['oxid'] ) );
        $this->assertEquals( "-1", $aViewData['oxid'] );
    }

    /**
     * SelectList_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction( 'oxselectlist', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction( 'oxselectlist', 'isDerived', '{ return false; }');
        modConfig::getInstance()->setConfigParam( "blAllowSharedEdit", true );

        // testing..
        try {
            $oView = new SelectList_Main();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in SelectList_Main::save()" );
            return;
        }
        $this->fail( "error in SelectList_Main::save()" );
    }

    /**
     * SelectList_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction( 'oxselectlist', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction( 'oxselectlist', 'isDerived', '{ return false; }');
        modConfig::getInstance()->setConfigParam( "blAllowSharedEdit", true );

        // testing..
        try {
            $oView = new SelectList_Main();
            $oView->saveinnlang();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in SelectList_Main::saveinnlang()" );
            return;
        }
        $this->fail( "error in SelectList_Main::saveinnlang()" );
    }

    /**
     * SelectList_Main::DelFields() test case
     *
     * @return null
     */
    public function testDelFields()
    {
        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction( 'oxselectlist', 'isDerived', '{ return false; }');
        oxTestModules::addFunction( 'oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass(); $oField1->name = "testField1";$oField2 = new stdClass(); $oField2->name = "testField2"; return array( $oField1, $oField2 ); }');

        modConfig::setParameter( "aFields", array( "testField1", "testField2" ) );

        // testing..
        $oView = $this->getMock( "SelectList_Main", array( "parseFieldName", "save" ) );
        $oView->expects( $this->at( 0 ) )->method( 'parseFieldName' )->will( $this->returnValue( "testField2" ) );
        $oView->expects( $this->at( 1 ) )->method( 'parseFieldName' )->will( $this->returnValue( "testField1" ) );
        $oView->expects( $this->at( 2 ) )->method( 'save' );
        $oView->delFields();
    }

    /**
     * SelectList_Main::AddField() test case
     *
     * @return null
     */
    public function testAddFieldNothingToAdd()
    {
        modConfig::setParameter( "sAddField", null );
        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');

        // testing..
        $oView = new SelectList_Main();
        $this->assertNull( $oView->addField() );
        $this->assertEquals( -1, oxSession::getVar( "iErrorCode" ) );
    }

    /**
     * SelectList_Main::AddField() test case
     *
     * @return null
     */
    public function testAddFieldRearangeFieldReturnsTrue()
    {
        modConfig::setParameter( "sAddField", "testField" );
        modConfig::setParameter( "aFields", array( "testField" ) );
        modConfig::setParameter( "sAddFieldPos", 1 );

        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction( 'oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock( "SelectList_Main", array( "_rearrangeFields", "save" ) );
        $oView->expects( $this->once() )->method( '_rearrangeFields' )->will( $this->returnValue( true ) );
        $oView->expects( $this->never() )->method( 'save' );
        $this->assertNull( $oView->addField() );
    }

    /**
     * SelectList_Main::AddField() test case
     *
     * @return null
     */
    public function testAddFieldRearangeField()
    {
        modConfig::setParameter( "sAddField", "testField" );
        modConfig::setParameter( "aFields", array( "testField" ) );
        modConfig::setParameter( "sAddFieldPos", 1 );

        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction( 'oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock( "SelectList_Main", array( "_rearrangeFields", "save" ) );
        $oView->expects( $this->once() )->method( '_rearrangeFields' )->will( $this->returnValue( false ) );
        $oView->expects( $this->once() )->method( 'save' );
        $this->assertNull( $oView->addField() );
    }

    /**
     * SelectList_Main::ChangeField() test case
     *
     * @return null
     */
    public function testChangeFieldNothingToChange()
    {
        modConfig::setParameter( "sAddField", null );

        // testing..
        $oView = new SelectList_Main();
        $this->assertNull( $oView->changeField() );
        $this->assertEquals( -1, oxSession::getVar( "iErrorCode" ) );
    }

    /**
     * SelectList_Main::ChangeField() test case
     *
     * @return null
     */
    public function testChangeFieldRearagneFieldsReturnsTrue()
    {
        modConfig::setParameter( "sAddField", "testField" );
        modConfig::setParameter( "aFields", array( "testField" ) );
        modConfig::setParameter( "sAddFieldPos", 1 );

        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction( 'oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock( "SelectList_Main", array( "parseFieldName", "_rearrangeFields", "save" ) );
        $oView->expects( $this->once() )->method( 'parseFieldName' )->will( $this->returnValue( "testField1" ) );
        $oView->expects( $this->once() )->method( '_rearrangeFields' )->will( $this->returnValue( true ) );
        $oView->expects( $this->never() )->method( 'save' );
        $oView->changeField();
    }

    /**
     * SelectList_Main::ChangeField() test case
     *
     * @return null
     */
    public function testChangeField()
    {
        modConfig::setParameter( "sAddField", "testField" );
        modConfig::setParameter( "aFields", array( "testField" ) );
        modConfig::setParameter( "sAddFieldPos", 1 );

        oxTestModules::addFunction( 'oxselectlist', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction( 'oxUtils', 'assignValuesFromText', '{ $oField1 = new stdClass();$oField1->name = "testField1";$oField2 = new stdClass();$oField2->name = "testField2";return array( 1 => $oField1, 2 => $oField2 ); }');

        // testing..
        $oView = $this->getMock( "SelectList_Main", array( "parseFieldName", "_rearrangeFields", "save" ) );
        $oView->expects( $this->once() )->method( 'parseFieldName' )->will( $this->returnValue( "testField1" ) );
        $oView->expects( $this->once() )->method( '_rearrangeFields' )->will( $this->returnValue( false     ) );
        $oView->expects( $this->once() )->method( 'save' );
        $oView->changeField();
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsFieldArrayIsEmpty()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", null );
        $this->assertTrue( $oView->UNITrearrangeFields( "test", 0 ) );
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsPosIsBelowZero()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", array( 1 ) );
        $this->assertTrue( $oView->UNITrearrangeFields( "test", -1 ) );
        $this->assertEquals( -2, oxSession::getVar( "iErrorCode" ) );
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsUnknownField()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", array( 1 ) );
        $this->assertTrue( $oView->UNITrearrangeFields( "test", 1 ) );
        $this->assertEquals( -2, oxSession::getVar( "iErrorCode" ) );
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsNoChangesWereMade()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", array( 1, 2 ) );
        $this->assertFalse( $oView->UNITrearrangeFields( 1, 1 ) );
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsCurrentPosIsLowerThatPassed()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", array( 1, 2, 3 ) );
        $this->assertFalse( $oView->UNITrearrangeFields( 1, 2 ) );
    }

    /**
     * SelectList_Main::RearrangeFields() test case
     *
     * @return null
     */
    public function testRearrangeFieldsCurrentPosIsHigherThatPassed()
    {
        // defining parameters
        $oView = $this->getProxyClass( "SelectList_Main" );
        $oView->setNonPublicVar( "aFieldArray", array( 1, 2, 3 ) );
        $this->assertFalse( $oView->UNITrearrangeFields( 2, 0 ) );
    }


    /**
     * SelectList_Main::ParseFieldName() test case
     *
     * @return null
     */
    public function testParseFieldName()
    {
        $oView = new SelectList_Main();
        $this->assertEquals( "bbb", $oView->parseFieldName( "aaa__@@bbb") );
    }
}
