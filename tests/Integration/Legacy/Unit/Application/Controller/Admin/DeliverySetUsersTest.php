<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for DeliverySet_Users class
 */
class DeliverySetUsersTest extends \PHPUnit\Framework\TestCase
{

    /**
     * DeliverySet_Users::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdeliveryset", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('DeliverySet_Users');
        $this->assertSame('deliveryset_users', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('readonly', $aViewData);
    }
}
