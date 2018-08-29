<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Category class
 */
class CategoryTest extends \OxidTestCase
{

    /**
     * Category::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category');
        $this->assertEquals('category.tpl', $oView->render());
    }
}
