<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\ShopVersion;
use \oxUtils;
use \oxOutput;
use \oxconfig;
use \oxField;
use \oxTestModules;

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

class OutputTest extends \OxidTestCase
{
    /**
     * Testing output processor
     */
    public function testProcess()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertEquals('someting', $oOutput->process('someting', 'something'));
    }

    /**
     * Testing output processor replaces euro sign in utf mode
     */
    public function testProcessWithEuroSign()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertEquals('�someting', $oOutput->process('�someting', 'something'));
    }

    /**
     * Testing output processor replaces euro sign when replacing disabled
     */
    public function testProcessWithEuroSignWithDisabledReplace()
    {
        $oOutput = oxNew('oxOutput');

        $this->assertEquals('�someting', $oOutput->process('�someting', 'something'));
    }

    public function testAddVersionTags()
    {
        $version = oxNew(ShopVersion::class)->getVersion();
        $currentYear = date("Y");

        $majorVersion = explode('.', $version)[0];

        $output = oxNew('oxOutput');
        // should add tag only to first head item
        $test = "<head>foo</head>bar<head>test2</head>";
        $result = $output->addVersionTags($test);

        $editionName = $this->getEditionName();
        $this->assertNotEquals($test, $result);
        $this->assertEquals("<head>foo</head>\n  <!-- OXID eShop ". $editionName ." Edition, Version $majorVersion, Shopping Cart System (c) OXID eSales AG 2003 - $currentYear - https://www.oxid-esales.com -->bar<head>test2</head>", $result);
    }

    /**
     * Bug #1800, fix test
     */
    public function testAddVersionTagsUpperCase()
    {
        $version = oxNew(ShopVersion::class)->getVersion();
        $sCurYear = date("Y");

        $sMajorVersion = explode('.', $version)[0];

        $oOutput = oxNew('oxOutput');
        $sTest = "<head>foo</Head>bar";
        $sRes = $oOutput->addVersionTags($sTest);

        $editionName = $this->getEditionName();
        $this->assertNotEquals($sTest, $sRes);
        $this->assertEquals("<head>foo</head>\n  <!-- OXID eShop ". $editionName ." Edition, Version $sMajorVersion, Shopping Cart System (c) OXID eSales AG 2003 - $sCurYear - https://www.oxid-esales.com -->bar", $sRes);
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
        $oEmail = oxNew('oxEmail');
        $oEmail->email = 1;
        $oEmail2 = clone $oEmail;
        $oOutput->processEmail($oEmail);
        $this->assertEquals($oEmail2, $oEmail);
    }

    public function testSetCharsetSetOutputFormatSendHeaders()
    {
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('setHeader'));
        $utils->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: text/html; charset=asd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $utils);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asd');
        $oOutput->sendHeaders();


        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('setHeader'));
        $utils->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: application/json; charset=asdd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $utils);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asdd');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
        $oOutput->sendHeaders();


        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('setHeader'));
        $utils->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: text/html; charset=asdd'));
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $utils);
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

    private function getEditionName()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $editionName = 'Enterprise';
        } elseif ($this->getTestConfig()->getShopEdition() == 'PE') {
            $editionName = 'Professional';
        } else {
            $editionName = 'Community';
        }

        return $editionName;
    }
}
