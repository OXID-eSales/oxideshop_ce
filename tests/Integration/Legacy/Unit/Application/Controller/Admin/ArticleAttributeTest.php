<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Article_Attribute class
 */
class ArticleAttributeTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Article_Attribute::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxattribute"));

        // testing..
        $oView = oxNew('Article_Attribute');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);
        $this->assertTrue($aViewData["readonly"]);

        $this->assertSame('article_attribute', $sTplName);
    }
}
