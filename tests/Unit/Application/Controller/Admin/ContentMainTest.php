<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Content;
use \Exception;
use \oxTestModules;

/**
 * Tests for Content_Main class
 */
class ContentMainTest extends \OxidTestCase
{

    /**
     * Content_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Content_Main');
        $this->assertEquals('content_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Content);
    }

    /**
     * Content_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Content_Main');
        $this->assertEquals('content_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Content_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcontent', 'save', '{ throw new Exception( "save" );}');

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentMain::class, array("_checkIdent"));
            $oView->expects($this->once())->method('_checkIdent')->will($this->returnValue(false));
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Content_Main::Save()");

            return;
        }
        $this->fail("Error in Content_Main::Save()");
    }

    /**
     * Content_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxcontent', 'save', '{ throw new Exception( "save" );}');

        // testing..
        try {
            $oView = oxNew('Content_Main');
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Content_Main::Save()");

            return;
        }
        $this->fail("Error in Content_Main::Save()");
    }

    /**
     * Content_Main::PrepareIdent() test case
     *
     * @return null
     */
    public function testPrepareIdentEmptyIdent()
    {
        // defining parameters
        $oView = oxNew('Content_Main');
        $this->assertNull($oView->UNITprepareIdent(false));
    }

    /**
     * Content_Main::PrepareIdent() test case
     *
     * @return null
     */
    public function testPrepareIdent()
    {
        // defining parameters
        $oView = oxNew('Content_Main');
        $this->assertEquals("aaabbb", $oView->UNITprepareIdent("~!@#$%^&^%*%(&^)aaabbb"));
    }

    /**
     * Content_Main::CheckIdent() test case
     *
     * @return null
     */
    public function testCheckIdentEmptyIdent()
    {
        // testing..
        $oView = oxNew('Content_Main');
        $this->assertTrue($oView->UNITcheckIdent("", ""));
    }

    /**
     * Content_Main::CheckIdent() test case
     *
     * @return null
     */
    public function testCheckIdent()
    {
        // testing..
        $oView = oxNew('Content_Main');
        $this->assertTrue($oView->UNITcheckIdent("oxstartmetadescription", ""));
    }
}
