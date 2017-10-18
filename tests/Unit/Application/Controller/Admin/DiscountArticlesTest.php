<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Discount;

use \oxTestModules;

/**
 * Tests for Discount_Articles class
 */
class DiscountArticlesTest extends \OxidTestCase
{

    /**
     * Discount_Articles::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdiscount", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Discount_Articles');
        $this->assertEquals('discount_articles.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue(isset($aViewData['readonly']));
        $this->assertTrue($aViewData['edit'] instanceof Discount);
    }
}
