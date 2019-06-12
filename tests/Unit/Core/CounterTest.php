<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxDb;

class CounterTest extends \OxidTestCase
{
    protected function tearDown()
    {
        oxDb::getDb("delete from oxcounters");

        return parent::tearDown();
    }

    /**
     * oxCounter:::getNext() test case
     *
     * @return null
     */
    public function testGetNext()
    {
        $oCounter = oxNew('oxCounter');

        $iNext1 = $oCounter->getNext("test1");
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));

        $iNext2 = $oCounter->getNext("test2");
        $this->assertNotEquals($iNext2, $iNext1);
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
    }

    /**
     * oxCounter:::update() test case
     *
     * @return null
     */
    public function testUpdate()
    {
        $oCounter = oxNew('oxCounter');

        $this->assertEquals(1, $oCounter->getNext("test4"));
        $oCounter->update("test3", 3);
        $this->assertEquals(4, $oCounter->getNext("test3"));
        $oCounter->update("test3", 2);
        $this->assertEquals(5, $oCounter->getNext("test3"));
        $this->assertEquals(2, $oCounter->getNext("test4"));
    }
}
