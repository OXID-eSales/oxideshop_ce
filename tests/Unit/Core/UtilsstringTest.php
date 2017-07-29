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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
