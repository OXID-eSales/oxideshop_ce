<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for DeliverySet class
 */
class DeliverySetTest extends \OxidTestCase
{

    /**
     * DeliverySet::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('DeliverySet');
        $this->assertEquals('deliveryset.tpl', $oView->render());
    }
}
