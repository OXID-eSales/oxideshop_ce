<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Category;
use \oxTestModules;

/**
 * Tests for Category_Order class
 */
class CategoryOrderTest extends \OxidTestCase
{

    /**
     * Category_Order::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxcategory", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Category_Order');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Category);

        $this->assertEquals('category_order.tpl', $sTplName);
    }
}
