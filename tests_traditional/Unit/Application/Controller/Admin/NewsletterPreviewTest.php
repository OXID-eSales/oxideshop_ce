<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Newsletter;
use \oxTestModules;

/**
 * Tests for Newsletter_Preview class
 */
class NewsletterPreviewTest extends \OxidTestCase
{

    /**
     * Newsletter_Preview::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxnewsletter', 'prepare', '{}');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Newsletter_Preview');
        $this->assertEquals('newsletter_preview.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof newsletter);
    }
}
