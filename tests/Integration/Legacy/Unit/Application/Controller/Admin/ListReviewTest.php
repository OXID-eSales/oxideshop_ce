<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DOMDocument;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Tests for List_Review class
 */
class ListReviewTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxlinks');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxcontents');
        $this->cleanUpTable('oxobject2category');

        if (isset($_POST['oxid'])) {
            unset($_POST['oxid']);
        }

        $this->getConfig()->setGlobalParameter('ListCoreTable', null);

        parent::tearDown();
    }

    /**
     * List_Review::Render() test case
     */
    public function testRender()
    {
        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument()));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListReview::class, ["getNavigation"]);
        $oView->expects($this->atLeastOnce())->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_review", $oView->render());
    }

    /**
     * Testing if methods removes parent id checking from sql
     */
    public function testPrepareWhereQuery()
    {
        $oArtList = oxNew('Article_List');
        $sSql = $oArtList->buildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->prepareWhereQuery([], $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", (string) $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->buildSelectString("");
        $sSql = $oList->prepareWhereQuery([], $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", (string) $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }

    /**
     * Testing if methods removes parent id checking from sql
     */
    public function testPrepareWhereQueryCase2()
    {
        $oArtList = oxNew('Article_List');
        $sSql = $oArtList->buildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->prepareWhereQuery([], $sSql);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", (string) $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->buildSelectString("");
        $sSql = $oList->prepareWhereQuery([], $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", (string) $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }
}
