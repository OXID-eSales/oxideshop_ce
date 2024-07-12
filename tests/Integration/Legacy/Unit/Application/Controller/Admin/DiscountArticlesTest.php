<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Discount;
use \oxTestModules;

/**
 * Tests for Discount_Articles class
 */
class DiscountArticlesTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Discount_Articles::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdiscount", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Discount_Articles');
        $this->assertSame('discount_articles', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertArrayHasKey('readonly', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Discount::class, $aViewData['edit']);
    }
}
