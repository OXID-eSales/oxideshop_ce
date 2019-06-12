<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for GenImport_Main class
 */
class GenImportMainTest extends \OxidTestCase
{

    /**
     * GenImport_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenImport_Main');
        $this->assertEquals('genimport_main.tpl', $oView->render());
    }


    /**
     * Checks if values was converted to HTML entities.
     *
     * @return array
     */
    public function providerRenderIfConvertedViewData()
    {
        return array(
            array('sGiCsvFieldTerminator', "'<b>", "&#039;&lt;b&gt;"),
            array('sGiCsvFieldEncloser', "'<b>", "&#039;&lt;b&gt;")
        );
    }

    /**
     * @param $sParameter
     * @param $sValue
     * @param $sResult
     *
     * @dataProvider providerRenderIfConvertedViewData
     */
    public function testRenderIfConvertedViewData($sParameter, $sValue, $sResult)
    {
        $oView = oxNew('GenImport_Main');
        $this->getConfig()->setConfigParam($sParameter, $sValue);
        $oView->render();
        $aData = $oView->getViewData();

        $this->assertSame($sResult, $aData[$sParameter]);
    }

    /**
     * GenImport_Main::DeleteCsvFile() test case
     *
     * @return null
     */
    public function testDeleteCsvFile()
    {
        // creating file for test
        $sFilePath = $this->getConfig()->getConfigParam("sCompileDir") . md5(time());
        $rFile = fopen($sFilePath, "w");
        fclose($rFile);

        $this->assertTrue(file_exists($sFilePath));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("_getUploadedCsvFilePath"));
        $oView->expects($this->once())->method('_getUploadedCsvFilePath')->will($this->returnValue($sFilePath));
        $oView->UNITdeleteCsvFile();

        $this->assertFalse(file_exists($sFilePath));
    }

    /**
     * GenImport_Main::GetCsvFieldsNames() test case
     *
     * @return null
     */
    public function testGetCsvFieldsNamesContainsNoHeader()
    {
        $this->setRequestParameter('blContainsHeader', false);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("_getUploadedCsvFilePath", "_getCsvFirstRow"));
        $oView->expects($this->once())->method('_getUploadedCsvFilePath')->will($this->returnValue(false));
        $oView->expects($this->once())->method('_getCsvFirstRow')->will($this->returnValue(array(1, 2, 3)));
        $this->assertEquals(array(2 => 'Column 1', 3 => 'Column 2', 4 => 'Column 3'), $oView->UNITgetCsvFieldsNames());
    }

    /**
     * GenImport_Main::GetCsvFieldsNames() test case
     *
     * @return null
     */
    public function testGetCsvFieldsNamesContainsHeader()
    {
        $this->setRequestParameter('blContainsHeader', true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("_getUploadedCsvFilePath", "_getCsvFirstRow"));
        $oView->expects($this->once())->method('_getUploadedCsvFilePath')->will($this->returnValue(false));
        $oView->expects($this->once())->method('_getCsvFirstRow')->will($this->returnValue(array(1, 2, 3)));
        $this->assertEquals(array(1, 2, 3), $oView->UNITgetCsvFieldsNames());
    }

    /**
     * GenImport_Main::GetCsvFirstRow() test case
     *
     * @return null
     */
    public function testGetCsvFirstRow()
    {
        // creating file for test
        $sFilePath = $this->getConfig()->getConfigParam("sCompileDir") . md5(time());
        $rFile = fopen($sFilePath, "w");
        fwrite($rFile, "\"test1\";\"test2\";\"test3\"");
        fclose($rFile);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("_getCsvFieldsTerminator", "_getCsvFieldsEncolser", "_getUploadedCsvFilePath"));
        $oView->expects($this->once())->method('_getCsvFieldsTerminator')->will($this->returnValue(";"));
        $oView->expects($this->once())->method('_getCsvFieldsEncolser')->will($this->returnValue("\""));
        $oView->expects($this->once())->method('_getUploadedCsvFilePath')->will($this->returnValue($sFilePath));
        $this->assertEquals(array("test1", "test2", "test3"), $oView->UNITgetCsvFirstRow());
    }

    /**
     * GenImport_Main::ResetUploadedCsvData() test case
     *
     * @return null
     */
    public function testResetUploadedCsvData()
    {
        $this->getSession()->setVariable("sCsvFilePath", "sCsvFilePath");
        $this->getSession()->setVariable("blCsvContainsHeader", "blCsvContainsHeader");

        $oView = $this->getProxyClass("GenImport_Main");
        $oView->setNonPublicVar("_sCsvFilePath", "testPath");
        $oView->UNITresetUploadedCsvData();

        $this->assertNull(oxRegistry::getSession()->getVariable("sCsvFilePath"));
        $this->assertNull(oxRegistry::getSession()->getVariable("blCsvContainsHeader"));
        $this->assertNull($oView->getNonPublicVar("_sCsvFilePath"));
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep2()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{}');

        // defining parameters
        $iNavStep = 2;

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("_getUploadedCsvFilePath"));
        $oView->expects($this->once())->method('_getUploadedCsvFilePath')->will($this->returnValue(false));
        $this->assertEquals(1, $oView->UNITcheckErrors($iNavStep));
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3EmptyCsvFields()
    {
        $this->setRequestParameter('aCsvFields', array());

        // defining parameters
        $iNavStep = 3;

        $oView = oxNew('GenImport_Main');
        $this->assertEquals(2, $oView->UNITcheckErrors($iNavStep));
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{}');
        $this->setRequestParameter('aCsvFields', array("sTestField"));

        // defining parameters
        $iNavStep = 3;

        $oView = oxNew('GenImport_Main');
        $this->assertEquals($iNavStep, $oView->UNITcheckErrors($iNavStep));
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePathDefinedAsClassParam()
    {
        $this->getSession()->setVariable("sCsvFilePath", null);

        // testing..
        $oView = $this->getProxyClass("GenImport_Main");
        $oView->setNonPublicVar("_sCsvFilePath", "_sCsvFilePath");
        $this->assertEquals("_sCsvFilePath", $oView->UNITgetUploadedCsvFilePath());
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePathDefinedAsSessionParam()
    {
        $this->getSession()->setVariable("sCsvFilePath", "sCsvFilePath");

        // testing..
        $oView = $this->getProxyClass("GenImport_Main");
        $this->assertEquals("sCsvFilePath", $oView->UNITgetUploadedCsvFilePath());
    }

    /**
     * GenImport_Main::GetUploadedCsvFilePath() test case
     *
     * @return null
     */
    public function testGetUploadedCsvFilePath()
    {
        $this->getSession()->setVariable("sCsvFilePath", null);
        $sFileName = md5(time());

        // testing..
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getUploadedFile", "getConfigParam"));
        $oConfig->expects($this->once())->method('getUploadedFile')->will($this->returnValue(array("name" => $sFileName, "tmp_name" => rtrim(sys_get_temp_dir(), '/') . '/' . $sFileName)));
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue($this->getConfig()->getConfigParam("sCompileDir")));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, array("getConfig"), array(), '', false);
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals($this->getConfig()->getConfigParam("sCompileDir") . $sFileName, $oView->UNITgetUploadedCsvFilePath());
        $this->assertEquals($this->getConfig()->getConfigParam("sCompileDir") . $sFileName, oxRegistry::getSession()->getVariable('sCsvFilePath'));
    }

    /**
     * GenImport_Main::CheckImportErrors() test case
     *
     * @return null
     */
    public function testCheckImportErrors()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ throw new Exception( "addErrorToDisplay" );}');

        // defining parameters
        $oErpImport = $this->getMock("oxErpGenImport", array("getStatistics"));
        $oErpImport->expects($this->once())->method('getStatistics')->will($this->returnValue(array(array("r" => false, "m" => true))));

        try {
            $oView = oxNew('GenImport_Main');
            $oView->UNITcheckImportErrors($oErpImport);
        } catch (Exception $oExcp) {
            $this->assertEquals("addErrorToDisplay", $oExcp->getMessage(), "Error in GenImport_Main::_checkImportErrors()");

            return;
        }
        $this->fail("Error in GenImport_Main::_checkImportErrors()");
    }

    /**
     * GenImport_Main::GetCsvFieldsTerminator() test case
     *
     * @return null
     */
    public function testGetCsvFieldsTerminator()
    {
        $this->getConfig()->setConfigParam("sGiCsvFieldTerminator", ";");

        // testing..
        $oView = oxNew('GenImport_Main');
        $this->assertEquals($this->getConfig()->getConfigParam('sGiCsvFieldTerminator'), $oView->UNITgetCsvFieldsTerminator());
    }

    /**
     * GenImport_Main::GetCsvFieldsEncolser() test case
     *
     * @return null
     */
    public function testGetCsvFieldsEncolser()
    {
        $this->getConfig()->setConfigParam("sGiCsvFieldEncloser", "\"");

        // testing..
        $oView = oxNew('GenImport_Main');
        $this->assertEquals($this->getConfig()->getConfigParam('sGiCsvFieldEncloser'), $oView->UNITgetCsvFieldsEncolser());
    }
}
