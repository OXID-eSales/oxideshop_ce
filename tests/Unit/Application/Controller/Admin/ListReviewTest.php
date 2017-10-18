<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DOMDocument;

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
    protected function tearDown()
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
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListReview::class, array("getNavigation"));
        $oView->expects($this->at(0))->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_review.tpl", $oView->render());
    }

    /**
     * Testing if methods removes parent id checking from sql
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        $oArtList = oxNew('Article_List');
        $sSql = $oArtList->UNITbuildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->UNITprepareWhereQuery(array(), $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->UNITbuildSelectString("");
        $sSql = $oList->UNITprepareWhereQuery(array(), $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
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
        $sSql = $oArtList->UNITbuildSelectString(oxNew('oxArticle'));
        $sSql = $oArtList->UNITprepareWhereQuery(array(), $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = oxNew('List_Review');
        $sSql = $oList->UNITbuildSelectString("");
        $sSql = $oList->UNITprepareWhereQuery(array(), $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }
}
