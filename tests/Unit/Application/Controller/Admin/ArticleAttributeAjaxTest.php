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

use \oxDb;

/**
 * Tests for Actions_Order_Ajax class
 */
class ArticleAttributeAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute1', oxobjectid='_testObjectId', oxattrid='_testAttr1', oxvalue='_testValue1', oxpos=0");
        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute2', oxobjectid='_testObjectId', oxattrid='_testAttr2', oxvalue='_testValue2', oxpos=0");

        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute3', oxobjectid='_testObjectId2', oxattrid='_testAttribute', oxvalue='_testValue3', oxpos=0");
        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute4', oxobjectid='_testObjectId2', oxattrid='_testAttribute', oxvalue='_testValue4', oxpos=0");
        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute5', oxobjectid='_testObjectId2', oxattrid='_testAttribute', oxvalue='_testValue5', oxpos=0");

        $this->addToDatabase("insert into oxattribute set oxid='_testAttribute', oxshopid=1, oxtitle='_testAttributeTitle'", 'oxattribute');
        $this->addToDatabase("insert into oxarticles set oxid='_testAttributeArticle', oxshopid='1', oxtitle='_testAttributeArticle'", 'oxarticles');
        $this->addToDatabase("insert into oxattribute set oxid='_testAttributeSaveAttr', oxshopid=1, oxtitle='_testAttributeSaveAttrTitle'", 'oxattribute');

        oxDb::getDb()->execute("insert into oxobject2attribute set oxid='_testAttribute6', oxobjectid='_testAttributeArticle', oxattrid='_testAttributeSaveAttr', oxvalue='_testValue6', oxpos=0");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxattribute where oxid='_testAttribute'" );

        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testAttributeArticle'" );
        oxDb::getDb()->execute( "delete from oxattribute where oxid='_testAttributeSaveAttr'" );

        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute1'");
        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute2'");
        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute3'");
        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute4'");
        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute5'");

        oxDb::getDb()->execute("delete from oxobject2attribute where oxobjectid='_testObjectIdAdd1'");

        oxDb::getDb()->execute("delete from oxobject2attribute where oxid='_testAttribute6'");
        oxDb::getDb()->execute("delete from oxobject2attribute where oxvalue='_testAttrValue'");

        parent::tearDown();
    }

    public function getAttributeViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxattribute_1_de' : 'oxv_oxattribute_de';
    }

    public function getObject2AttributeViewTable()
    {
        return 'oxv_oxobject2attribute_de';
    }

    /**
     * ArticleAttributeAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_attribute_ajax');
        $this->assertEquals("from " . $this->getAttributeViewTable() . " where " . $this->getAttributeViewTable() . ".oxid not in ( select " . $this->getObject2AttributeViewTable() . ".oxattrid from " . $this->getObject2AttributeViewTable() . " left join " . $this->getAttributeViewTable() . " on " . $this->getAttributeViewTable() . ".oxid=" . $this->getObject2AttributeViewTable() . ".oxattrid  where " . $this->getObject2AttributeViewTable() . ".oxobjectid = '' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleAttributeAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testArticleAttributeOxid';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('article_attribute_ajax');
        $this->assertEquals("from " . $this->getObject2AttributeViewTable() . " left join " . $this->getAttributeViewTable() . " on " . $this->getAttributeViewTable() . ".oxid=" . $this->getObject2AttributeViewTable() . ".oxattrid  where " . $this->getObject2AttributeViewTable() . ".oxobjectid = '$sOxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleAttributeAjax::removeAttr() test case
     *
     * @return null
     */
    public function testRemoveAttr()
    {
        $oView = $this->getMock("article_attribute_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testAttribute1', '_testAttribute2')));

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='_testObjectId'"));
        $oView->removeAttr();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='_testObjectId'"));
    }

    /**
     * ArticleAttributeAjax::removeAttr() test case
     *
     * @return null
     */
    public function testRemoveAttrAll()
    {
        $sOxid = '_testObjectId2';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);
        $oView = oxNew('article_attribute_ajax');

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sOxid'"));
        $oView->removeAttr();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sOxid'"));
    }

    /**
     * ArticleAttributeAjax::addAttr() test case
     *
     * @return null
     */
    public function testAddAttr()
    {
        $oView = $this->getMock("article_attribute_ajax", array("_getActionIds"));
        $sSynchOxid = '_testObjectIdAdd1';
        $this->setRequestParameter("synchoxid", $sSynchOxid);

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testAttributeAdd1', '_testAttributeAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sSynchOxid'"));
        $oView->addAttr();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sSynchOxid'"));
    }

    /**
     * ArticleAttributeAjax::addAttr() test case
     *
     * @return null
     */
    public function testAddAttrAll()
    {
        $sSynchOxid = '_testObjectIdAdd1';
        $this->setRequestParameter("synchoxid", $sSynchOxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from " . $this->getAttributeViewTable() . " where " . $this->getAttributeViewTable() . ".oxid not in ( select " . $this->getObject2AttributeViewTable() . ".oxattrid from " . $this->getObject2AttributeViewTable() . " left join " . $this->getAttributeViewTable() . " on " . $this->getAttributeViewTable() . ".oxid=" . $this->getObject2AttributeViewTable() . ".oxattrid  where " . $this->getObject2AttributeViewTable() . ".oxobjectid = '$sSynchOxid' )");
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sSynchOxid'"));
        $oView = oxNew('article_attribute_ajax');
        $oView->addAttr();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='$sSynchOxid'"));
    }

    /**
     * ArticleAttributeAjax::saveAttributeValue() test case
     *
     * @return null
     */
    public function testSaveAttributeValue()
    {
        $sOxid = '_testAttributeArticle';
        $sAttrOxid = '_testAttributeSaveAttr';
        $sAttrValue = '_testAttrValue';

        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("attr_oxid", $sAttrOxid);
        $this->setRequestParameter("attr_value", $sAttrValue);

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxvalue='$sAttrValue'"));

        $oView = oxNew('article_attribute_ajax');
        $oView->saveAttributeValue();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxvalue='$sAttrValue'"));
    }

}