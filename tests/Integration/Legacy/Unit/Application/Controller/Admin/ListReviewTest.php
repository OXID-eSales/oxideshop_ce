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
     *
     * @return null
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
     *
     * @return null
     */
    public function testRender()
    {
        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument()));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListReview::class, array("getNavigation"));
        $oView->expects($this->atLeastOnce())->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_review", $oView->render());
    }

    /**
     * Testing if methods removes parent id checking from sql
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        $oArtList = oxNew('Article_List');
        $sSql = $oArtList->buildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->prepareWhereQuery(array(), $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->buildSelectString("");
        $sSql = $oList->prepareWhereQuery(array(), $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }

    /**
     * Testing if methods removes parent id checking from sql
     *
     * @return null
     */
    public function testPrepareWhereQueryCase2()
    {
        $oArtList = oxNew('Article_List');
        $sSql = $oArtList->buildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->prepareWhereQuery(array(), $sSql);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->buildSelectString("");
        $sSql = $oList->prepareWhereQuery(array(), $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . $tableViewNameGenerator->getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }
}
