<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Discount_Users class
 */
class DiscountUsersTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Discount_Users::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdiscount", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Discount_Users');
        $this->assertSame('discount_users', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('allgroups2', $aViewData);
        $this->assertArrayHasKey('readonly', $aViewData);
    }
}
