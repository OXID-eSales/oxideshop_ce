<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Delivery;
use \oxTestModules;

/**
 * Tests for Delivery_Articles class
 */
class DeliveryArticlesTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Delivery_Articles::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdelivery", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Delivery_Articles');
        $this->assertSame('delivery_articles', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Delivery::class, $aViewData['edit']);
        $this->assertArrayHasKey('readonly', $aViewData);
    }
}
