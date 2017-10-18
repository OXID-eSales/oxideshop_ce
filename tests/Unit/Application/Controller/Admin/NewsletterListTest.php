<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for News_List class
 */
class NewsletterListTest extends \OxidTestCase
{

    /**
     * Testing render
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Newsletter_List');
        $this->assertEquals('newsletter_list.tpl', $oView->render());
    }
}
