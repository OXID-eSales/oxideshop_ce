<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Category;
use \Exception;
use \oxTestModules;

/**
 * Tests for Category_Text class
 */
class CategoryTextTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Category_Text::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxcategory", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Category_Text');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Category);
        $this->assertEquals('category_text', $sTplName);
    }

    /**
     * Category_Text::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Text');
        $this->assertEquals('category_text', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Category_Text::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Category_Text');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in Category_Text::Save()");

            return;
        }

        $this->fail("Error in Category_Text::Save()");
    }
}
