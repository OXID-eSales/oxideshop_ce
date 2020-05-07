<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Newsletter class
 */
class NewsletterTest extends \OxidTestCase
{
    /**
     * Admin_News::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('Admin_Newsletter');
        $this->assertEquals('newsletter.tpl', $oView->render());
    }
}
