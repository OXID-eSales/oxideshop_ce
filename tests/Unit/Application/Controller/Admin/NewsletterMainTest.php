<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Newsletter_Main class
 */
class NewsletterMainTest extends \OxidTestCase
{
    /**
     * Newsletter_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Newsletter_Main');
        $this->assertEquals('newsletter_main.tpl', $oView->render());
    }
}
