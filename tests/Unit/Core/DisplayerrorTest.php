<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class DisplayerrorTest extends \OxidTestCase
{
    /** @var oxDisplayError */
    private $_oDisplayError;

    /**
     * Initialize default display error object.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oDisplayError = oxNew('oxDisplayError');
    }

    /**
     * Tests the set and getter for message
     */
    public function testGetOxMessage()
    {
        $this->_oDisplayError->setMessage("Test ");
        $this->assertEquals("Test ", $this->_oDisplayError->getOxMessage());
    }

    /**
     * Test if the error class is always null
     */
    public function testGetErrorClassType()
    {
        $this->assertNull($this->_oDisplayError->getErrorClassType());
    }

    /**
     *tests if the value is always empty
     */
    public function testGetValue()
    {
        $this->assertEquals($this->_oDisplayError->getValue("whatever"), "");
    }

    public function testFormatingMessage()
    {
        $this->_oDisplayError->setMessage("Test %s string with %d values");
        $this->_oDisplayError->setFormatParameters(array('formatting', 2));
        $this->assertEquals("Test formatting string with 2 values", $this->_oDisplayError->getOxMessage());
    }
}
