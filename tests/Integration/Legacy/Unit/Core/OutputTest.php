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

class OutputTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing output processor
     */
    public function testProcess()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertSame('someting', $oOutput->process('someting', 'something'));
    }

    /**
     * Testing output processor replaces euro sign in utf mode
     */
    public function testProcessWithEuroSign()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertSame('�someting', $oOutput->process('�someting', 'something'));
    }

    /**
     * Testing output processor replaces euro sign when replacing disabled
     */
    public function testProcessWithEuroSignWithDisabledReplace()
    {
        $oOutput = oxNew('oxOutput');

        $this->assertSame('�someting', $oOutput->process('�someting', 'something'));
    }

    public function testAddVersionTags()
    {
        $currentYear = date("Y");

        $output = oxNew('oxOutput');
        // should add tag only to first head item
        $test = "<head>foo</head>bar<head>test2</head>";
        $result = $output->addVersionTags($test);

        $editionName = $this->getEditionName();
        $this->assertNotSame($test, $result);
        $this->assertSame("<head>foo</head>\n  <!-- OXID eShop " . $editionName . sprintf(' Edition, Shopping Cart System (c) OXID eSales AG 2003 - %s - https://www.oxid-esales.com -->bar<head>test2</head>', $currentYear), $result);
    }

    /**
     * Bug #1800, fix test
     */
    public function testAddVersionTagsUpperCase()
    {
        $sCurYear = date("Y");

        $oOutput = oxNew('oxOutput');
        $sTest = "<head>foo</Head>bar";
        $sRes = $oOutput->addVersionTags($sTest);

        $editionName = $this->getEditionName();
        $this->assertNotSame($sTest, $sRes);
        $this->assertSame("<head>foo</head>\n  <!-- OXID eShop " . $editionName . sprintf(' Edition, Shopping Cart System (c) OXID eSales AG 2003 - %s - https://www.oxid-esales.com -->bar', $sCurYear), $sRes);
    }

    /**
     * Testing view processor
     */
    public function testProcessViewArray()
    {
        $oOutput = oxNew('oxOutput');
        $this->assertSame(['something'], $oOutput->processViewArray(['something'], 'something'));
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
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['setHeader']);
        $utils->expects($this->once())->method('setHeader')->with('Content-Type: text/html; charset=asd');
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $utils);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asd');
        $oOutput->sendHeaders();


        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['setHeader']);
        $utils->expects($this->once())->method('setHeader')->with('Content-Type: application/json; charset=asdd');
        oxTestModules::cleanUp();
        oxTestModules::addModuleObject('oxUtils', $utils);
        $oOutput = oxNew('oxOutput');
        $oOutput->setCharset('asdd');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
        $oOutput->sendHeaders();


        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['setHeader']);
        $utils->expects($this->once())->method('setHeader')->with('Content-Type: text/html; charset=asdd');
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
        $this->assertSame('asasd', ob_get_clean());
        ob_start();
        $oOutput->flushOutput();
        $this->assertSame('', ob_get_clean());

        $oOutput = oxNew('oxOutput');
        $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
        ob_start();
        $oOutput->output('asd', 'asasd');
        $this->assertSame('', ob_get_clean());
        ob_start();
        $oOutput->flushOutput();
        $this->assertSame('{"asd":"asasd"}', ob_get_clean());
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
