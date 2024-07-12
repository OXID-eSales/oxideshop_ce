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
class ContentMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Content_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Content_Main');
        $this->assertEquals('content_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Content);
    }

    /**
     * Content_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Content_Main');
        $this->assertEquals('content_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Content_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcontent', 'save', '{ throw new Exception( "save" );}');

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentMain::class, ["checkIdent"]);
            $oView->expects($this->once())->method('checkIdent')->will($this->returnValue(false));
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in Content_Main::Save()");

            return;
        }

        $this->fail("Error in Content_Main::Save()");
    }

    /**
     * Content_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxcontent', 'save', '{ throw new Exception( "save" );}');

        // testing..
        try {
            $oView = oxNew('Content_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in Content_Main::Save()");

            return;
        }

        $this->fail("Error in Content_Main::Save()");
    }

    /**
     * Content_Main::PrepareIdent() test case
     */
    public function testPrepareIdentEmptyIdent()
    {
        // defining parameters
        $oView = oxNew('Content_Main');
        $this->assertNull($oView->prepareIdent(false));
    }

    /**
     * Content_Main::PrepareIdent() test case
     */
    public function testPrepareIdent()
    {
        // defining parameters
        $oView = oxNew('Content_Main');
        $this->assertEquals("aaabbb", $oView->prepareIdent("~!@#$%^&^%*%(&^)aaabbb"));
    }

    /**
     * Content_Main::CheckIdent() test case
     */
    public function testCheckIdentEmptyIdent()
    {
        // testing..
        $oView = oxNew('Content_Main');
        $this->assertTrue($oView->checkIdent("", ""));
    }

    /**
     * Content_Main::CheckIdent() test case
     */
    public function testCheckIdent()
    {
        // testing..
        $oView = oxNew('Content_Main');
        $this->assertTrue($oView->checkIdent("oxstartmetadescription", ""));
    }
}
