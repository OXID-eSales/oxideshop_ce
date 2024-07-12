<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class CounterTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute('delete from oxcounters');

        parent::tearDown();
    }

    /**
     * oxCounter:::getNext() test case
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
     */
    public function testUpdate()
    {
        $oCounter = oxNew('oxCounter');

        $this->assertSame(1, $oCounter->getNext("test4"));
        $oCounter->update("test3", 3);
        $this->assertSame(4, $oCounter->getNext("test3"));
        $oCounter->update("test3", 2);
        $this->assertSame(5, $oCounter->getNext("test3"));
        $this->assertSame(2, $oCounter->getNext("test4"));
    }
}
