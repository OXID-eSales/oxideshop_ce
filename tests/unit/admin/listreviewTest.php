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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for List_Review class
 */
class Unit_Admin_ListReviewTest extends OxidTestCase
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

        modSession::getInstance()->cleanup();

        //
        oxRegistry::getConfig()->setGlobalParameter('ListCoreTable', null);

        parent::tearDown();
    }

    /**
     * List_Review::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument));

        $oView = $this->getMock("List_Review", array("getNavigation"));
        $oView->expects($this->at($iCnt++))->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_review.tpl", $oView->render());
    }

    /**
     * Testing if methods removes parent id checking from sql
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        $oArtList = new Article_List();
        $sSql = $oArtList->UNITbuildSelectString(new oxArticle());
        $sSql = $oArtList->UNITprepareWhereQuery(array(), $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = new List_Review();
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
        $oArtList = new Article_List();
        $sSql = $oArtList->UNITbuildSelectString(new oxArticle());
        $sSql = $oArtList->UNITprepareWhereQuery(array(), $sSql);

        // checking if exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertTrue((bool) $blCheckForParent);

        $oList = new List_Review();
        $sSql = $oList->UNITbuildSelectString("");
        $sSql = $oList->UNITprepareWhereQuery(array(), $sSql);

        // checking if not exists string oxarticle.oxparentid = ''
        $blCheckForParent = preg_match("/\s+and\s+" . getViewName('oxarticles') . ".oxparentid\s+=\s+''/", $sSql);
        $this->assertFalse((bool) $blCheckForParent);
    }
}
