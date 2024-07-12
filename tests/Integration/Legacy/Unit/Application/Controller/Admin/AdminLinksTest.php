<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Links class
 */
class AdminLinksTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Admin_Links::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Links');
        $this->assertSame('admin_links', $oView->render());
    }
}
