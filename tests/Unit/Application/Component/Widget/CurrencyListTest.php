<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCurrencyList class
 */
class CurrencyListTest extends \OxidTestCase
{

    /**
     * Testing oxwCurrencyList::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oCurrencyList = oxNew('oxwCurrencyList');
        $this->assertEquals('widget/header/currencies.tpl', $oCurrencyList->render());
    }
}
