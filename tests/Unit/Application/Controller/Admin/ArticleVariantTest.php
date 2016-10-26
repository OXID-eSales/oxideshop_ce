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

use \Exception;
use \OxidEsales\EshopCommunity\Application\Model\Article;
use \OxidEsales\EshopCommunity\Application\Model\ArticleList;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Article_Variant class
 */
class ArticleVariantTest extends \OxidTestCase
{
    /**
     * Article_Variant::render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxparentid from oxarticles where oxparentid != ''"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Variant');
        $this->assertEquals('article_variant.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertTrue($aViewData["mylist"] instanceof ArticleList);
    }

    /**
     * Article_Variant::render() test case
     *
     * @return null
     */
    public function testRenderVariant()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid != ''"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Variant');
        $this->assertEquals('article_variant.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertTrue($aViewData["parentarticle"] instanceof Article);
        $this->assertEquals(1, $aViewData["issubvariant"]);
        $this->assertEquals(1, $aViewData["readonly"]);
        $this->assertTrue($aViewData["mylist"] instanceof ArticleList);
    }

    /**
     * Article_Variant::savevariant() test case
     *
     * @return null
     */
    public function testSavevariant()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("voxid", "testid");
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->savevariant();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Variant::savevariant()");

            return;
        }
        $this->fail("error in Article_Variant::savevariant()");
    }

    /**
     * Article_Variant::savevariant() test case
     *
     * @return null
     */
    public function testSavevariantDefaultId()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("voxid", "-1");

        try {
            $oView = oxNew('Article_Variant');
            $oView->savevariant();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Variant::savevariant()");

            return;
        }
        $this->fail("error in Article_Variant::savevariant()");
    }

    /**
     * Article_Variant::savevariants() test case
     *
     * @return null
     */
    public function testSavevariants()
    {
        $this->setRequestParameter("editval", array("oxid1" => "param1", "oxid2" => "param2"));

        $aMethods[] = "savevariant";
        $aMethods[] = "resetContentCache";

        $oView = $this->getMock("Article_Variant", $aMethods);
        $oView->expects($this->at(0))->method('savevariant')->with($this->equalTo("oxid1"), $this->equalTo("param1"));
        $oView->expects($this->at(1))->method('savevariant')->with($this->equalTo("oxid2"), $this->equalTo("param2"));
        $oView->expects($this->at(2))->method('resetContentCache');

        $oView->savevariants();
    }

    /**
     * Article_Variant::deleteVariant() test case
     *
     * @return null
     */
    public function testDeleteVariant()
    {
        oxTestModules::addFunction('oxarticle', 'delete', '{ throw new Exception( "delete" ); }');
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->deleteVariant();
        } catch (Exception $oExcp) {
            $this->assertEquals("delete", $oExcp->getMessage(), "error in Article_Variant::deleteVariant()");

            return;
        }
        $this->fail("error in Article_Variant::deleteVariant()");
    }

    /**
     * Article_Variant::changename() test case
     *
     * @return null
     */
    public function testChangename()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->changename();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Variant::changename()");

            return;
        }
        $this->fail("error in Article_Variant::changename()");
    }

    /**
     * Article_Variant::addsel() test case
     *
     * @return null
     */
    public function testAddsel()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction('oxVariantHandler', 'genVariantFromSell', '{ throw new Exception( "genVariantFromSell" ); }');

        $this->setRequestParameter("allsel", "testsel");

        try {
            $oView = oxNew('Article_Variant');
            $oView->addsel();
        } catch (Exception $oExcp) {
            $this->assertEquals("genVariantFromSell", $oExcp->getMessage(), "error in Article_Variant::addsel()");

            return;
        }
        $this->fail("error in Article_Variant::addsel()");
    }
}
