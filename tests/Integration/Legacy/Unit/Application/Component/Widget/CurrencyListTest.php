<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCurrencyList class
 */
class CurrencyListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwCurrencyList::render()
     */
    public function testRender()
    {
        $oCurrencyList = oxNew('oxwCurrencyList');
        $this->assertEquals('widget/header/currencies', $oCurrencyList->render());
    }
}
