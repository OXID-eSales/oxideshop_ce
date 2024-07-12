<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Category;

/**
 * Tests for Category_Pictures class
 */
class CategoryPicturesTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Category_Pictures::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_Pictures');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Category::class, $aViewData["edit"]);

        $this->assertSame('category_pictures', $sTplName);
    }
}
