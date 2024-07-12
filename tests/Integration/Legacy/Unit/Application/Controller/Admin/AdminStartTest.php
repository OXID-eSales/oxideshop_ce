<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Start class
 */
class AdminStartTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Admin_Start::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Start');
        $this->assertSame('start', $oView->render());
    }
}
