<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Article_Variant class
 */
class ArticleVariantTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Article_Variant::render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxparentid from oxarticles where oxparentid != ''"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Variant');
        $this->assertSame('article_variant', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $aViewData["mylist"]);
    }

    /**
     * Article_Variant::render() test case
     */
    public function testRenderVariant()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid != ''"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Variant');
        $this->assertSame('article_variant', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["parentarticle"]);
        $this->assertSame(1, $aViewData["issubvariant"]);
        $this->assertSame(1, $aViewData["readonly"]);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $aViewData["mylist"]);
    }

    /**
     * Article_Variant::savevariant() test case
     */
    public function testSavevariant()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("voxid", "testid");
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->savevariant();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Article_Variant::savevariant()");

            return;
        }

        $this->fail("error in Article_Variant::savevariant()");
    }

    /**
     * Article_Variant::savevariant() test case
     */
    public function testSavevariantDefaultId()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("voxid", "-1");

        try {
            $oView = oxNew('Article_Variant');
            $oView->savevariant();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Article_Variant::savevariant()");

            return;
        }

        $this->fail("error in Article_Variant::savevariant()");
    }

    /**
     * Article_Variant::savevariants() test case
     */
    public function testSavevariants()
    {
        $this->setRequestParameter("editval", ["oxid1" => "param1", "oxid2" => "param2"]);

        $aMethods[] = "savevariant";
        $aMethods[] = "resetContentCache";

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleVariant::class, $aMethods);
        $oView
            ->method('savevariant')
            ->withConsecutive(['oxid1', 'param1'], ['oxid2', 'param2']);

        $oView->expects($this->atLeastOnce())->method('resetContentCache');

        $oView->savevariants();
    }

    /**
     * Article_Variant::deleteVariant() test case
     */
    public function testDeleteVariant()
    {
        oxTestModules::addFunction('oxarticle', 'delete', '{ throw new Exception( "delete" ); }');
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->deleteVariant();
        } catch (Exception $exception) {
            $this->assertSame("delete", $exception->getMessage(), "error in Article_Variant::deleteVariant()");

            return;
        }

        $this->fail("error in Article_Variant::deleteVariant()");
    }

    /**
     * Article_Variant::changename() test case
     */
    public function testChangename()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("oxid", "testid");

        try {
            $oView = oxNew('Article_Variant');
            $oView->changename();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Article_Variant::changename()");

            return;
        }

        $this->fail("error in Article_Variant::changename()");
    }

    /**
     * Article_Variant::addsel() test case
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
        } catch (Exception $exception) {
            $this->assertSame("genVariantFromSell", $exception->getMessage(), "error in Article_Variant::addsel()");

            return;
        }

        $this->fail("error in Article_Variant::addsel()");
    }
}
