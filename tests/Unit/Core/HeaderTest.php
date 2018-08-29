<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Tests for oxHeader class
 */
class HeaderTest extends \OxidTestCase
{

    /**
     * oxHeader::getHeader() test case.
     * test if default value is empty
     *
     * @return null
     */
    public function testGetHeader_default()
    {
        $oHeader = oxNew('oxHeader');
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
        $oHeader = oxNew('oxHeader');
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
        $oHeader = oxNew('oxHeader');
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
        $oHeader = oxNew('oxHeader');
        $oHeader->setHeader("Some header" . $sNewLine . "2");
        $this->assertEquals(array("Some header2" . "\r\n"), $oHeader->getHeader(), 'Set header check.');
    }
}
