<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxField;
use \oxTestModules;

/**
 * Tests for Article_Overview class
 */
class ArticleOverviewTest extends \OxidTestCase
{

    /**
     * Tear down
     *
     * @return null
     */
    protected function tearDown()
    {
        //
        $this->cleanUpTable("oxorderarticles");

        parent::tearDown();
    }

    /**
     * Article_Overview::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->setRequestParameter("oxid", "1126");

        $oBase = oxNew('oxbase');
        $oBase->init("oxorderarticles");
        $oBase->setId("_testOrderArticleId");
        $oBase->oxorderarticles__oxorderid = new oxField("testOrderId");
        $oBase->oxorderarticles__oxamount = new oxField(1);
        $oBase->oxorderarticles__oxartid = new oxField("1126");
        $oBase->oxorderarticles__oxordershopid = new oxField($this->getConfig()->getShopId());
        $oBase->save();

        // testing..
        $oView = oxNew('Article_Overview');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertNull($aViewData["afolder"]);
        $this->assertNull($aViewData["aSubclass"]);

        $this->assertEquals('article_overview.tpl', $sTplName);
    }

    /**
     * Article_Overview::Render() test case
     *
     * @return null
     */
    public function testRenderPArentBuyable()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->setRequestParameter("oxid", "1126");
        $this->getConfig()->setConfigParam("blVariantParentBuyable", true);

        // testing..
        $oView = oxNew('Article_Overview');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertNull($aViewData["afolder"]);
        $this->assertNull($aViewData["aSubclass"]);

        $this->assertEquals('article_overview.tpl', $sTplName);
    }
}
