<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_User class
 */
class AdminUserTest extends \OxidTestCase
{

    /**
     * Admin_User::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_User');
        $this->assertEquals('user.tpl', $oView->render());
    }
}
