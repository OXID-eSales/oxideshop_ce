<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Content class
 */
class AdminContentTest extends \OxidTestCase
{

    /**
     * Admin_Content::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Content');
        $this->assertEquals('content.tpl', $oView->render());
    }
}
