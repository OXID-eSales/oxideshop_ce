<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwMiniBasket class
 */
class MiniBasketTest extends \OxidTestCase
{
    /**
     * Testing oxwMiniBasket::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oWMiniBasket = oxNew('oxwMiniBasket');
        $this->assertEquals('widget/header/minibasket.tpl', $oWMiniBasket->render());
    }
}
