<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for GenImport_Main class
 */
class Unit_Admin_GenImportMainTest extends OxidTestCase
{
    /**
     * GenImport_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new GenImport_Main();
        $this->assertEquals( 'genimport_main.tpl', $oView->render() );
    }

    /**
     * GenImport_Main::DeleteCsvFile() test case
     *
     * @return null
     */
    public function testDeleteCsvFile()
    {
        // creating file for test
        $sFilePath = oxConfig::getInstance()->getConfigParam( "sCompileDir" ) . md5( time() );
        $rFile = fopen( $sFilePath, "w" );
        fclose( $rFile );

        $this->assertTrue( file_exists( $sFilePath ) );

        // testing..
        $oView = $this->getMock( "GenImport_Main", array( "_getUploadedCsvFilePath" ) );
        $oView->expects( $this->once() )->method( '_getUploadedCsvFilePath' )->will( $this->returnValue( $sFilePath ) );
        $oView->UNITdeleteCsvFile();

        $this->assertFalse( file_exists( $sFilePath ) );
    }

    /**
     * GenImport_Main::GetCsvFieldsNames() test case
     *
     * @return null
     */
    public function testGetCsvFieldsNamesContainsNoHeader()
    {
        modConfig::getInstance()->setParameter( 'blContainsHeader', false );

        $oView = $this->getMock( "GenImport_Main", array( "_getUploadedCsvFilePath", "_getCsvFirstRow" ) );
        $oView->expects( $this->once() )->method( '_getUploadedCsvFilePath' )->will( $this->returnValue( false ) );
        $oView->expects( $this->once() )->method( '_getCsvFirstRow' )->will( $this->returnValue( array( 1, 2, 3 ) ) );
        $this->assertEquals( array( 2 => 'Column 1', 3 => 'Column 2', 4 => 'Column 3' ), $oView->UNITgetCsvFieldsNames() );
    }

    /**
     * GenImport_Main::GetCsvFieldsNames() test case
     *
     * @return null
     */
    public function testGetCsvFieldsNamesContainsHeader()
    {
        modConfig::getInstance()->setParameter( 'blContainsHeader', true );

        $oView = $this->getMock( "GenImport_Main", array( "_getUploadedCsvFilePath", "_getCsvFirstRow" ) );
        $oView->expects( $this->once() )->method( '_getUploadedCsvFilePath' )->will( $this->returnValue( false ) );
        $oView->expects( $this->once() )->method( '_getCsvFirstRow' )->will( $this->returnValue( array( 1, 2, 3 ) ) );
        $this->assertEquals( array( 1, 2, 3 ), $oView->UNITgetCsvFieldsNames() );
    }

    /**
     * GenImport_Main::GetCsvFirstRow() test case
     *
     * @return null
     */
    public function testGetCsvFirstRow()
    {
        // creating file for test
        $sFilePath = oxConfig::getInstance()->getConfigParam( "sCompileDir" ) . md5( time() );
        $rFile = fopen( $sFilePath, "w" );
        fwrite( $rFile, "\"test1\";\"test2\";\"test3\"" );
        fclose( $rFile );

        // testing..
        $oView = $this->getMock( "GenImport_Main", array( "_getCsvFieldsTerminator", "_getCsvFieldsEncolser", "_getUploadedCsvFilePath" ));
        $oView->expects( $this->once() )->method( '_getCsvFieldsTerminator' )->will( $this->returnValue( ";" ) );
        $oView->expects( $this->once() )->method( '_getCsvFieldsEncolser' )->will( $this->returnValue( "\"" ) );
        $oView->expects( $this->once() )->method( '_getUploadedCsvFilePath' )->will( $this->returnValue( $sFilePath ) );
        $this->assertEquals( array( "test1", "test2", "test3" ), $oView->UNITgetCsvFirstRow() );
    }

    /**
     * GenImport_Main::ResetUploadedCsvData() test case
     *
     * @return null
     */
    public function testResetUploadedCsvData()
    {
        modSession::getInstance()->setVar( "sCsvFilePath", "sCsvFilePath" );
        modSession::getInstance()->setVar( "blCsvContainsHeader", "blCsvContainsHeader" );

        $oView = $this->getProxyClass( "GenImport_Main" );
        $oView->setNonPublicVar( "_sCsvFilePath", "testPath" );
        $oView->UNITresetUploadedCsvData();

        $this->assertNull( oxSession::getVar( "sCsvFilePath" ) );
        $this->assertNull( oxSession::getVar( "blCsvContainsHeader" ) );
        $this->assertNull( $oView->getNonPublicVar( "_sCsvFilePath" ) );
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep2()
    {
        oxTestModules::addFunction( 'oxUtilsView', 'addErrorToDisplay', '{}');

        // defining parameters
        $iNavStep = 2;

        $oView = $this->getMock( "GenImport_Main", array( "_getUploadedCsvFilePath" ) );
        $oView->expects( $this->once() )->method( '_getUploadedCsvFilePath' )->will( $this->returnValue( false ) );
        $this->assertEquals( 1, $oView->UNITcheckErrors( $iNavStep ) );
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3EmptyCsvFields()
    {
        modConfig::getInstance()->setParameter( 'aCsvFields', array() );

        // defining parameters
        $iNavStep = 3;

        $oView = new GenImport_Main();
        $this->assertEquals( 2, $oView->UNITcheckErrors( $iNavStep ) );
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3()
    {
        oxTestModules::addFunction( 'oxUtilsView', 'addErrorToDisplay', '{}');
        modConfig::getInstance()->setParameter( 'aCsvFields', array( "sTestField" ) );

        // defining parameters
        $iNavStep = 3;

        $oView = new GenImport_Main();
        $this->assertEquals( $iNavStep, $oView->UNITcheckErrors( $iNavStep ) );
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePathDefinedAsClassParam()
    {
        modSession::getInstance()->setVar( "sCsvFilePath", null );

        // testing..
        $oView = $this->getProxyClass( "GenImport_Main" );
        $oView->setNonPublicVar( "_sCsvFilePath", "_sCsvFilePath" );
        $this->assertEquals( "_sCsvFilePath", $oView->UNITgetUploadedCsvFilePath() );
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePathDefinedAsSessionParam()
    {
        modSession::getInstance()->setVar( "sCsvFilePath", "sCsvFilePath" );

        // testing..
        $oView = $this->getProxyClass( "GenImport_Main" );
        $this->assertEquals( "sCsvFilePath", $oView->UNITgetUploadedCsvFilePath() );
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePath()
    {
        modSession::getInstance()->setVar( "sCsvFilePath", null );
        $sFileName = md5( time() );

        // testing..
        $oConfig = $this->getMock( "oxStdClass", array( "getUploadedFile", "getConfigParam" ) );
        $oConfig->expects( $this->once() )->method( 'getUploadedFile' )->will( $this->returnValue( array( "name" => $sFileName, "tmp_name" => rtrim( sys_get_temp_dir(), '/' ) . '/' . $sFileName ) ) );
        $oConfig->expects( $this->once() )->method( 'getConfigParam' )->will( $this->returnValue( oxConfig::getInstance()->getConfigParam( "sCompileDir" ) ) );

        $oView = $this->getMock( "GenImport_Main", array( "getConfig" ), array(), '', false );
        $oView->expects( $this->once() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( oxConfig::getInstance()->getConfigParam( "sCompileDir" ).$sFileName, $oView->UNITgetUploadedCsvFilePath() );
        $this->assertEquals( oxConfig::getInstance()->getConfigParam( "sCompileDir" ).$sFileName, oxSession::getVar( 'sCsvFilePath' ) );
    }

    /**
     * GenImport_Main::CheckImportErrors() test case
     *
     * @return null
     */
    public function testCheckImportErrors()
    {
        oxTestModules::addFunction( 'oxUtilsView', 'addErrorToDisplay', '{ throw new Exception( "addErrorToDisplay" );}');

        // defining parameters
        $oErpImport = $this->getMock( "oxStdClass", array( "getStatistics" ) );
        $oErpImport->expects( $this->once() )->method( 'getStatistics' )->will( $this->returnValue( array( array( "r" => false, "m" => true )) ) );

        try {
            $oView = new GenImport_Main();
            $oView->UNITcheckImportErrors( $oErpImport );
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "addErrorToDisplay", $oExcp->getMessage(), "Error in GenImport_Main::_checkImportErrors()" );
            return;
        }
        $this->fail( "Error in GenImport_Main::_checkImportErrors()" );
    }

    /**
     * GenImport_Main::GetCsvFieldsTerminator() test case
     *
     * @return null
     */
    public function testGetCsvFieldsTerminator()
    {
        modConfig::getInstance()->setConfigParam( "sGiCsvFieldTerminator", ";" );

        // testing..
        $oView = new GenImport_Main();
        $this->assertEquals( oxConfig::getInstance()->getConfigParam( 'sGiCsvFieldTerminator' ), $oView->UNITgetCsvFieldsTerminator() );

    }

    /**
     * GenImport_Main::GetCsvFieldsEncolser() test case
     *
     * @return null
     */
    public function testGetCsvFieldsEncolser()
    {
        modConfig::getInstance()->setConfigParam( "sGiCsvFieldEncloser", "\"" );

        // testing..
        $oView = new GenImport_Main();
        $this->assertEquals( oxConfig::getInstance()->getConfigParam( 'sGiCsvFieldEncloser' ), $oView->UNITgetCsvFieldsEncolser() );
    }
}
