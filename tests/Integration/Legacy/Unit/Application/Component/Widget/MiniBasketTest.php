<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwMiniBasket class
 */
class MiniBasketTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing oxwMiniBasket::render()
     */
    public function testRender()
    {
        $oWMiniBasket = oxNew('oxwMiniBasket');
        $this->assertEquals('widget/header/minibasket', $oWMiniBasket->render());
    }
}
