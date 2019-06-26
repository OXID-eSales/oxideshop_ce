<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxRegistry;

class UtilsstringTest extends \OxidTestCase
{
    public function testPrepareCSVField()
    {
        $this->assertEquals('"blafoo;wurst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField("blafoo;wurst;suppe"));
        $this->assertEquals('"bl""afoo;wurst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField("bl\"afoo;wurst;suppe"));
        $this->assertEquals('"blafoo;wu"";rst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField("blafoo;wu\";rst;suppe"));
        $this->assertEquals('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField(""));
        $this->assertEquals('""""', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField("\""));
        $this->assertEquals('";"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField(";"));
    }

    public function testMinimizeTruncateString()
    {
        $sTest = "myfooblatest";
        $this->assertEquals("myf", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 3));
        $this->assertEquals("", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 0));
        $this->assertEquals("myfooblatest", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 99));

        $sTest = "        my,f,,     o  o bl at  ,,  ,,est,  ";
        $this->assertEquals("my", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 3));
        $this->assertEquals("my,f", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 4));
        $this->assertEquals("", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 0));
        $this->assertEquals("my,f,, o o bl at ,, ,,est", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 99));
    }

    public function testPrepareStrForSearch()
    {
        $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch(' ä ö ü Ä Ö Ü ß &amp;'));
        $this->assertEquals(' h&auml;user', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch(' häuser'));
        $this->assertEquals('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch('qwertz'));
        $this->assertEquals('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrforSearch(''));
    }
}
