<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Links class
 */
class AdminLinksTest extends \OxidTestCase
{

    /**
     * Admin_Links::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Links');
        $this->assertEquals('admin_links.tpl', $oView->render());
    }
}
