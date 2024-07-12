<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Tests for oxHeader class
 */
class HeaderTest extends \PHPUnit\Framework\TestCase
{

    /**
     * oxHeader::getHeader() test case.
     * test if default value is empty
     */
    public function testGetHeader_default()
    {
        $oHeader = oxNew('oxHeader');
        $this->assertSame([], $oHeader->getHeader(), 'Default header should be empty as nothing is set.');
    }

    /**
     * oxHeader::setHeader() oxHeader::getHeader() test case.
     * test if returns set value.
     */
    public function testSetGetHeader()
    {
        $oHeader = oxNew('oxHeader');
        $oHeader->setHeader("Some header");
        $this->assertSame(['Some header
'], $oHeader->getHeader(), 'Set header check.');
    }

    /**
     * oxReverseProxyHeader::setNonCacheable() test case.
     * test if no cache header formated correctly.
     */
    public function testSetNonCacheable()
    {
        $oHeader = oxNew('oxHeader');
        $oHeader->setNonCacheable();
        $this->assertSame(['Cache-Control: no-cache;
'], $oHeader->getHeader(), 'Cache header was NOT formated correctly.');
    }

    /**
     * @return array
     */
    public function providerSetGetHeader_withNewLine_newLineRemoved(): \Iterator
    {
        yield ["\r"];
        yield ["\n"];
        yield ["\r\n"];
        yield ["\n\r"];
    }

    /**
     * oxHeader::setHeader() oxHeader::getHeader() test case.
     * test if strips new lines.
     *
     * @dataProvider providerSetGetHeader_withNewLine_newLineRemoved
     *
     * @param $sNewLine
     */
    public function testSetGetHeader_withNewLine_newLineRemoved($sNewLine)
    {
        $oHeader = oxNew('oxHeader');
        $oHeader->setHeader("Some header" . $sNewLine . "2");
        $this->assertSame(['Some header2
'], $oHeader->getHeader(), 'Set header check.');
    }
}
