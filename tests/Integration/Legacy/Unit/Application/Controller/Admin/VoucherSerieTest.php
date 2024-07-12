<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for VoucherSerie class
 */
class VoucherSerieTest extends \PHPUnit\Framework\TestCase
{

    /**
     * VoucherSerie::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('VoucherSerie');
        $this->assertSame('voucherserie', $oView->render());
    }
}
