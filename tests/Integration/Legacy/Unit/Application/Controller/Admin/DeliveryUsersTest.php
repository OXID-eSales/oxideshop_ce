<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Delivery_Users class
 */
class DeliveryUsersTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Delivery_Users::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdelivery", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Delivery_Users');
        $this->assertEquals('delivery_users', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['readonly']));
    }
}
