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

/**
 * Tests for oxHeader class
 */
class Unit_Core_oxHeaderTest extends OxidTestCase
{

    /**
     * oxHeader::getHeader() test case.
     * test if default value is empty
     *
     * @return null
     */
    public function testGetHeader_default()
    {
        $oHeader = new oxHeader();
        $this->assertEquals(array(), $oHeader->getHeader(), 'Default header should be empty as nothing is set.');
    }

    /**
     * oxHeader::setHeader() oxHeader::getHeader() test case.
     * test if returns set value.
     *
     * @return null
     */
    public function testSetGetHeader()
    {
        $oHeader = new oxHeader();
        $oHeader->setHeader("Some header");
        $this->assertEquals(array("Some header" . "\r\n"), $oHeader->getHeader(), 'Set header check.');
    }

    /**
     * oxReverseProxyHeader::setNonCacheable() test case.
     * test if no cache header formated correctly.
     *
     * @return null
     */
    public function testSetNonCacheable()
    {
        $oHeader = new oxHeader();
        $oHeader->setNonCacheable();
        $this->assertEquals(array("Cache-Control: no-cache;" . "\r\n"), $oHeader->getHeader(), 'Cache header was NOT formated correctly.');
    }

    /**
     * @return array
     */
    public function providerSetGetHeader_withNewLine_newLineRemoved()
    {
        return array(
            array("\r"),
            array("\n"),
            array("\r\n"),
            array("\n\r"),
        );
    }

    /**
     * oxHeader::setHeader() oxHeader::getHeader() test case.
     * test if strips new lines.
     *
     * @dataProvider providerSetGetHeader_withNewLine_newLineRemoved
     *
     * @param $sNewLine
     *
     * @return null
     */
    public function testSetGetHeader_withNewLine_newLineRemoved($sNewLine)
    {
        $oHeader = new oxHeader();
        $oHeader->setHeader("Some header" . $sNewLine . "2");
        $this->assertEquals(array("Some header2" . "\r\n"), $oHeader->getHeader(), 'Set header check.');
    }
}