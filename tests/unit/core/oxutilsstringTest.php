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
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxutilsstringTest extends OxidTestCase
{

    public function testPrepareCSVField()
    {
          $this->assertEquals('"blafoo;wurst;suppe"', oxUtilsString::getInstance()->prepareCSVField("blafoo;wurst;suppe"));
          $this->assertEquals('"bl""afoo;wurst;suppe"', oxUtilsString::getInstance()->prepareCSVField("bl\"afoo;wurst;suppe"));
          $this->assertEquals('"blafoo;wu"";rst;suppe"', oxUtilsString::getInstance()->prepareCSVField("blafoo;wu\";rst;suppe"));
          $this->assertEquals('', oxUtilsString::getInstance()->prepareCSVField(""));
          $this->assertEquals('""""', oxUtilsString::getInstance()->prepareCSVField("\""));
          $this->assertEquals('";"', oxUtilsString::getInstance()->prepareCSVField(";"));
    }

    public function testMinimizeTruncateString()
    {
        $sTest = "myfooblatest";
        $this->assertEquals("myf", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 3));
        $this->assertEquals("", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 0));
        $this->assertEquals("myfooblatest", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 99));

        $sTest = "        my,f,,     o  o bl at  ,,  ,,est,  ";
        $this->assertEquals("my", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 3));
        $this->assertEquals("my,f", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 4));
        $this->assertEquals("", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 0));
        $this->assertEquals("my,f,, o o bl at ,, ,,est", oxUtilsString::getInstance()->minimizeTruncateString($sTest, 99));
    }

    public function testPrepareStrForSearch()
    {
           $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', oxUtilsString::getInstance()->prepareStrForSearch(' ä ö ü Ä Ö Ü ß &amp;'));
           $this->assertEquals(' h&auml;user', oxUtilsString::getInstance()->prepareStrForSearch(' häuser'));
           $this->assertEquals('', oxUtilsString::getInstance()->prepareStrForSearch('qwertz'));
           $this->assertEquals('', oxUtilsString::getInstance()->prepareStrforSearch(''));
    }
}
