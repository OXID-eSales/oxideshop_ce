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
class ArticleAttributeTest extends \OxidTestCase
{

    /**
     * Article_Attribute::Render() test case
     *
     * @return null
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
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertTrue($aViewData["readonly"]);

        $this->assertEquals('article_attribute.tpl', $sTplName);
    }
}
