<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwServiceList class
 */
class ServiceListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwServiceList::render()
     */
    public function testRender()
    {
        $oServiceList = oxNew('oxwServiceList');
        $this->assertSame('widget/footer/services', $oServiceList->render());
    }
}
