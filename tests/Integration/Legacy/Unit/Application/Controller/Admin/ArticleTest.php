<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Article class
 */
class ArticleTest extends \OxidTestCase
{

    /**
     * Article::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Article');
        $this->assertEquals('article', $oView->render());
    }
}
