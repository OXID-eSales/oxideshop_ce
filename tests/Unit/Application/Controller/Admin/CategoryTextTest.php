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
class CategoryTextTest extends \OxidTestCase
{

    /**
     * Category_Text::Render() test case
     *
     * @return null
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
        $this->assertEquals('category_text.tpl', $sTplName);
    }

    /**
     * Category_Text::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Text');
        $this->assertEquals('category_text.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Category_Text::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Category_Text');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Category_Text::Save()");

            return;
        }
        $this->fail("Error in Category_Text::Save()");
    }
}
