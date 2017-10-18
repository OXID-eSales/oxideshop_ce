<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for dyn_interface class
 */
class DyninterfaceTest extends \OxidTestCase
{

    /**
     * dyn_interface::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = oxNew('dyn_interface');
        $this->assertEquals('dyn_interface', $oView->getViewId());
    }
}
