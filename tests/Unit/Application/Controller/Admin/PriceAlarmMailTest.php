<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for PriceAlarm_Mail class
 */
class PriceAlarmMailTest extends \OxidTestCase
{

    /**
     * PriceAlarm_Mail::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('PriceAlarm_Mail');
        $this->assertEquals('pricealarm_mail.tpl', $oView->render());
    }
}
