<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for PriceAlarm_Mail class
 */
class PriceAlarmMailTest extends \PHPUnit\Framework\TestCase
{

    /**
     * PriceAlarm_Mail::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('PriceAlarm_Mail');
        $this->assertSame('pricealarm_mail', $oView->render());
    }
}
