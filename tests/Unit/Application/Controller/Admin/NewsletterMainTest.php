<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Newsletter;

use \Exception;
use \oxTestModules;

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
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof newsletter);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Newsletter_Main');
        $sTpl = $oView->render();
        $this->assertEquals('newsletter_main.tpl', $sTpl);
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Newsletter_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxnewsletter', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Newsletter_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Newsletter_Main::save()");

            return;
        }
        $this->fail("error in Newsletter_Main::save()");
    }
}
