<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_User class
 */
class AdminUserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Admin_User::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_User');
        $this->assertSame('user', $oView->render());
    }
}
