<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for VoucherSerie class
 */
class VoucherSerieTest extends \OxidTestCase
{

    /**
     * VoucherSerie::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('VoucherSerie');
        $this->assertEquals('voucherserie.tpl', $oView->render());
    }
}
