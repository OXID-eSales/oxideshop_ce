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
class ArticleCrosssellingTest extends \OxidTestCase
{

    /**
     * Article_Crossselling::Render() test case
     *
     * @return null
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
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertTrue($aViewData["readonly"]);

        $this->assertEquals('article_crossselling.tpl', $sTplName);
    }
}
