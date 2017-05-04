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

class Unit_Core_oxutilsstringTest extends OxidTestCase
{

    public function testPrepareCSVField()
    {
        $this->assertEquals('"blafoo;wurst;suppe"', oxRegistry::get("oxUtilsString")->prepareCSVField("blafoo;wurst;suppe"));
        $this->assertEquals('"bl""afoo;wurst;suppe"', oxRegistry::get("oxUtilsString")->prepareCSVField("bl\"afoo;wurst;suppe"));
        $this->assertEquals('"blafoo;wu"";rst;suppe"', oxRegistry::get("oxUtilsString")->prepareCSVField("blafoo;wu\";rst;suppe"));
        $this->assertEquals('', oxRegistry::get("oxUtilsString")->prepareCSVField(""));
        $this->assertEquals('""""', oxRegistry::get("oxUtilsString")->prepareCSVField("\""));
        $this->assertEquals('";"', oxRegistry::get("oxUtilsString")->prepareCSVField(";"));
    }

    public function testMinimizeTruncateString()
    {
        $sTest = "myfooblatest";
        $this->assertEquals("myf", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 3));
        $this->assertEquals("", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 0));
        $this->assertEquals("myfooblatest", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 99));

        $sTest = "        my,f,,     o  o bl at  ,,  ,,est,  ";
        $this->assertEquals("my", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 3));
        $this->assertEquals("my,f", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 4));
        $this->assertEquals("", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 0));
        $this->assertEquals("my,f,, o o bl at ,, ,,est", oxRegistry::get("oxUtilsString")->minimizeTruncateString($sTest, 99));
    }

    public function testPrepareStrForSearch()
    {
        $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', oxRegistry::get("oxUtilsString")->prepareStrForSearch(' ä ö ü Ä Ö Ü ß &amp;'));
        $this->assertEquals(' h&auml;user', oxRegistry::get("oxUtilsString")->prepareStrForSearch(' häuser'));
        $this->assertEquals('', oxRegistry::get("oxUtilsString")->prepareStrForSearch('qwertz'));
        $this->assertEquals('', oxRegistry::get("oxUtilsString")->prepareStrforSearch(''));
    }
}
