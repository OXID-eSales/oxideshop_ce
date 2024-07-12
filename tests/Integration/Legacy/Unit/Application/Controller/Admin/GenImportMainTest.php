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
        $this->assertEquals('genimport_main', $oView->render());
    }


    /**
     * Checks if values was converted to HTML entities.
     *
     * @return array
     */
    public function providerRenderIfConvertedViewData()
    {
        return [['sGiCsvFieldTerminator', "'<b>", "&#039;&lt;b&gt;"], ['sGiCsvFieldEncloser', "'<b>", "&#039;&lt;b&gt;"]];
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getUploadedCsvFilePath"]);
        $oView->expects($this->once())->method('getUploadedCsvFilePath')->will($this->returnValue($sFilePath));
        $oView->deleteCsvFile();

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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getUploadedCsvFilePath", "getCsvFirstRow"]);
        $oView->expects($this->once())->method('getUploadedCsvFilePath')->will($this->returnValue(false));
        $oView->expects($this->once())->method('getCsvFirstRow')->will($this->returnValue([1, 2, 3]));
        $this->assertEquals([2 => 'Column 1', 3 => 'Column 2', 4 => 'Column 3'], $oView->getCsvFieldsNames());
    }

    /**
     * GenImport_Main::GetCsvFieldsNames() test case
     *
     * @return null
     */
    public function testGetCsvFieldsNamesContainsHeader()
    {
        $this->setRequestParameter('blContainsHeader', true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getUploadedCsvFilePath", "getCsvFirstRow"]);
        $oView->expects($this->once())->method('getUploadedCsvFilePath')->will($this->returnValue(false));
        $oView->expects($this->once())->method('getCsvFirstRow')->will($this->returnValue([1, 2, 3]));
        $this->assertEquals([1, 2, 3], $oView->getCsvFieldsNames());
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getCsvFieldsTerminator", "getCsvFieldsEncolser", "getUploadedCsvFilePath"]);
        $oView->expects($this->once())->method('getCsvFieldsTerminator')->will($this->returnValue(";"));
        $oView->expects($this->once())->method('getCsvFieldsEncolser')->will($this->returnValue("\""));
        $oView->expects($this->once())->method('getUploadedCsvFilePath')->will($this->returnValue($sFilePath));
        $this->assertEquals(["test1", "test2", "test3"], $oView->getCsvFirstRow());
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
        $oView->resetUploadedCsvData();

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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getUploadedCsvFilePath"]);
        $oView->expects($this->once())->method('getUploadedCsvFilePath')->will($this->returnValue(false));
        $this->assertEquals(1, $oView->checkErrors($iNavStep));
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3EmptyCsvFields()
    {
        $this->setRequestParameter('aCsvFields', []);

        // defining parameters
        $iNavStep = 3;

        $oView = oxNew('GenImport_Main');
        $this->assertEquals(2, $oView->checkErrors($iNavStep));
    }

    /**
     * GenImport_Main::CheckErrors() test case
     *
     * @return null
     */
    public function testCheckErrorsStep3()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{}');
        $this->setRequestParameter('aCsvFields', ["sTestField"]);

        // defining parameters
        $iNavStep = 3;

        $oView = oxNew('GenImport_Main');
        $this->assertEquals($iNavStep, $oView->checkErrors($iNavStep));
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
        $this->assertEquals("_sCsvFilePath", $oView->getUploadedCsvFilePath());
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
        $this->assertEquals("sCsvFilePath", $oView->getUploadedCsvFilePath());
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
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "getConfigParam"]);
        $oConfig->expects($this->once())->method('getUploadedFile')->will($this->returnValue(["name" => $sFileName, "tmp_name" => rtrim(sys_get_temp_dir(), '/') . '/' . $sFileName]));
        // $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue($this->getConfig()->getConfigParam("sCompileDir")));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals($this->getConfig()->getConfigParam("sCompileDir") . $sFileName, $oView->getUploadedCsvFilePath());
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
        $oErpImport = $this->getMock("oxErpGenImport", ["getStatistics"]);
        $oErpImport->expects($this->once())->method('getStatistics')->will($this->returnValue([["r" => false, "m" => true]]));

        try {
            $oView = oxNew('GenImport_Main');
            $oView->checkImportErrors($oErpImport);
        } catch (Exception $oExcp) {
            $this->assertEquals("addErrorToDisplay", $oExcp->getMessage(), "Error in GenImport_Main::checkImportErrors()");

            return;
        }
        $this->fail("Error in GenImport_Main::checkImportErrors()");
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
        $this->assertEquals($this->getConfig()->getConfigParam('sGiCsvFieldTerminator'), $oView->getCsvFieldsTerminator());
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
        $this->assertEquals($this->getConfig()->getConfigParam('sGiCsvFieldEncloser'), $oView->getCsvFieldsEncolser());
    }
}
