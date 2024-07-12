<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Start class
 */
class AdminStartTest extends \OxidTestCase
{

    /**
     * Admin_Start::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Start');
        $this->assertEquals('start', $oView->render());
    }
}
