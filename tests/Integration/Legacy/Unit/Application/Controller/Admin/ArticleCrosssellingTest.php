<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxTestModules;

/**
 * Tests for Article_Crossselling class
 */
class ArticleCrosssellingTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Article_Crossselling::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->setRequestParameter("oxid", "1126");

        // testing..
        $oView = oxNew('Article_Crossselling');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);
        $this->assertTrue($aViewData["readonly"]);

        $this->assertSame('article_crossselling', $sTplName);
    }
}
