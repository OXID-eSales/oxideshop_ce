<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_News class
 */
class AdminNewsTest extends \OxidTestCase
{

    /**
     * Admin_News::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('Admin_News');
        $this->assertEquals('admin_news.tpl', $oView->render());
    }
}
