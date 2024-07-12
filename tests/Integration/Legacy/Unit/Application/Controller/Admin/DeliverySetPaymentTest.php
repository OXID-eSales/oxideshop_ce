<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\DeliverySet;
use \oxTestModules;

/**
 * Tests for DeliverySet_Payment class
 */
class DeliverySetPaymentTest extends \PHPUnit\Framework\TestCase
{

    /**
     * DeliverySet_Payment::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdeliveryset", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('DeliverySet_Payment');
        $this->assertSame('deliveryset_payment', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertArrayHasKey('readonly', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliverySet::class, $aViewData['edit']);
    }

    /**
     * DeliverySet_Payment::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('DeliverySet_Payment');
        $this->assertSame('deliveryset_payment', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }
}
