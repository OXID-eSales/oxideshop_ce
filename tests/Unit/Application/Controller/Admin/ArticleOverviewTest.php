<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Application\Model\Article;
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
