<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxRegistry;

class UtilsstringTest extends \PHPUnit\Framework\TestCase
{
    public function testPrepareCSVField()
    {
        $this->assertSame('"blafoo;wurst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField("blafoo;wurst;suppe"));
        $this->assertSame('"bl""afoo;wurst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField('bl"afoo;wurst;suppe'));
        $this->assertSame('"blafoo;wu"";rst;suppe"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField('blafoo;wu";rst;suppe'));
        $this->assertSame('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField(""));
        $this->assertSame('""""', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField('"'));
        $this->assertSame('";"', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField(";"));
    }

    public function testMinimizeTruncateString()
    {
        $sTest = "myfooblatest";
        $this->assertSame("myf", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 3));
        $this->assertSame("", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 0));
        $this->assertSame("myfooblatest", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 99));

        $sTest = "        my,f,,     o  o bl at  ,,  ,,est,  ";
        $this->assertSame("my", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 3));
        $this->assertSame("my,f", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 4));
        $this->assertSame("", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 0));
        $this->assertSame("my,f,, o o bl at ,, ,,est", \OxidEsales\Eshop\Core\Registry::getUtilsString()->minimizeTruncateString($sTest, 99));
    }

    public function testPrepareStrForSearch()
    {
        $this->assertSame(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch(' ä ö ü Ä Ö Ü ß &amp;'));
        $this->assertSame(' h&auml;user', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch(' häuser'));
        $this->assertSame('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrForSearch('qwertz'));
        $this->assertSame('', \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareStrforSearch(''));
    }
}
