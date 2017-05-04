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

class oxUtils_Extended extends oxUtils
{

    public function checkForSearchEngines($blIsSEOverride = -1)
    {
        return true;
    }
}

class oxOutput_Extended extends oxOutput
{

    public function _SIDCallBack($aMatches)
    {
        return parent::_SIDCallBack($aMatches);
    }
}

class oxConfigForUnit_oxoutputTest extends oxconfig
{

    public function getShopURL($iLang = null, $blAdmin = null)
    {
        return 'www.test.com';
    }
}

class Unit_Core_oxoutputTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxAddClassModule('oxUtils_Extended', 'oxUtils');
        oxAddClassModule('oxOutput_Extended', 'oxOutput');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown()
    {
        oxRemClassModule('oxUtils_Extended');
        oxRemClassModule('oxOutput_Extended');

        parent::tearDown();
    }

    /**
     * Testing output processor
     */
    public function testProcess()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertEquals('someting', $oOutput->process('someting', 'something'));
    }

    /**
     * Testing output processor replaces euro sign in non utf mode
     */
    public function testProcessWithEuroSign()
    {
        $oOutput = oxNew('oxOutput');
        oxRegistry::getConfig()->setConfigParam('blSkipEuroReplace', false);
        modConfig::getInstance()->setConfigParam('iUtfMode', 0);
        $this->assertEquals('&euro;someting', $oOutput->process('山ometing', 'something'));
    }

    /**
     * Testing output processor replaces euro sign in utf mode
     */
    public function testProcessWithEuroSignInUtfMode()
    {
        $oOutput = oxNew('oxOutput');
        oxRegistry::getConfig()->setConfigParam('blSkipEuroReplace', false);
        modConfig::getInstance()->setConfigParam('iUtfMode', 1);
        $this->assertEquals('山ometing', $oOutput->process('山ometing', 'something'));
    }

    /**
     * Testing output processor replaces euro sign when replacing disabled
     */
    public function testProcessWithEuroSignWithDisabledReplace()
    {
        $oOutput = oxNew('oxOutput');
        oxRegistry::getConfig()->setConfigParam('blSkipEuroReplace', true);
        oxRegistry::getConfig()->setConfigParam('iUtfMode', 0);

        $this->assertEquals('山ometing', $oOutput->process('山ometing', 'something'));
    }

    public function testAddVersionTags()
    {
        $myConfig = oxRegistry::getConfig();
        $sVersion = $myConfig->getActiveShop()->oxshops__oxversion->value;
        $sVersion = $myConfig->getActiveShop()->oxshops__oxversion = new oxField("9.9", oxField::T_RAW);
        $sCurYear = date("Y");

        $sMajorVersion = '9';

        $oOutput = new oxOutput();
        // should add tag only to first head item
        $sTest = "<head>foo</head>bar<head>test2</head>";
        $sRes = $oOutput->addVersionTags($sTest);
        //reset value
        $myConfig->getActiveShop()->oxshops__oxversion = new oxField($sVersion, oxField::T_RAW);

        $this->assertNotEquals($sTest, $sRes);
        $this->assertEquals("<head>foo</head>\n  <!-- OXID eShop Community Edition, Version $sMajorVersion, Shopping Cart System (c) OXID eSales AG 2003 - $sCurYear - http://www.oxid-esales.com -->bar<head>test2</head>", $sRes);


    }

    /**
     * Bug #1800, fix test
     *
     */
    public function testAddVersionTagsUpperCase()
    {
        $myConfig = oxRegistry::getConfig();
        $sVersion = $myConfig->getActiveShop()->oxshops__oxversion->value;
        $sVersion = $myConfig->getActiveShop()->oxshops__oxversion = new oxField("9.9", oxField::T_RAW);
        $sCurYear = date("Y");

        $sMajorVersion = '9';

        $oOutput = new oxOutput();
        $sTest = "<head>foo</Head>bar";
        $sRes = $oOutput->addVersionTags($sTest);
        //reset value
        $myConfig->getActiveShop()->oxshops__oxversion = new oxField($sVersion, oxField::T_RAW);

        $this->assertNotEquals($sTest, $sRes);
        $this->assertEquals("<head>foo</head>\n  <!-- OXID eShop Community Edition, Version $sMajorVersion, Shopping Cart System (c) OXID eSales AG 2003 - $sCurYear - http://www.oxid-esales.com -->bar", $sRes);



    }

    /**
     * Testing view processor
     */
    public function testProcessViewArray()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertEquals(array('something'), $oOutput->processViewArray(array('something'), 'something'));
    }

    /**
     * Testing email processor
     */
    public function testProcessEmail()
    {
        $oOutput = oxNew('oxOutput');
        $oEmail = new oxEmail();
        $oEmail->email = 1;
        $oEmail2 = clone $oEmail;
        $oOutput->processEmail($oEmail);
        $this->assertEquals($oEmail2, $oEmail);
    }

    public function testSetCharsetSetOutputFormatSendHeaders()
    {
        $oU = $this->getMock('oxUtils', array('setHeader'));
        $oU->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: text/html; charset=asd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $oU);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asd');
        $oOutput->sendHeaders();


        $oU = $this->getMock('oxUtils', array('setHeader'));
        $oU->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: application/json; charset=asdd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $oU);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asdd');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
        $oOutput->sendHeaders();


        $oU = $this->getMock('oxUtils', array('setHeader'));
        $oU->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: text/html; charset=asdd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $oU);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asdd');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_HTML);
        $oOutput->sendHeaders();
    }

    public function testOutputFlushOutput()
    {
        $oOutput = oxNew('oxOutput');
        ob_start();
        $oOutput->output('asd', 'asasd');
        $this->assertEquals('asasd', ob_get_clean());
        ob_start();
        $oOutput->flushOutput();
        $this->assertEquals('', ob_get_clean());

        $oOutput = oxNew('oxOutput');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
        ob_start();
        $oOutput->output('asd', 'asasd');
        $this->assertEquals('', ob_get_clean());
        ob_start();
        $oOutput->flushOutput();
        $this->assertEquals('{"asd":"asasd"}', ob_get_clean());
    }
}
