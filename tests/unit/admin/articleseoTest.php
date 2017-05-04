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
 * Tests for Article_Seo class
 */
class Unit_Admin_ArticleSeoTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxvendor where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        $sQ = "delete from oxmanufacturers where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        $sQ = "delete from oxseo where oxobjectid='objectid'";
        oxDb::getDb()->execute($sQ);
        parent::tearDown();
    }

    /**
     * Article_Seo::getEntryUri() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $sO2CView = getViewName('oxobject2category');
        $sQ = "select oxarticles.oxid from oxarticles left join {$sO2CView} on
               oxarticles.oxid={$sO2CView}.oxobjectid where
               oxarticles.oxactive='1' and {$sO2CView}.oxobjectid is not null";

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sProdId = $oDb->getOne($sQ);

        // must be existing
        $this->assertTrue((bool) $sProdId);

        $oEncoder = $this->getMock("oxSeoEncoderCategory", array("getArticleVendorUri", "getArticleManufacturerUri", "getArticleTagUri", "getArticleUri", "getArticleMainUri"));
        $oEncoder->expects($this->at(0))->method('getArticleVendorUri')->will($this->returnValue("ArticleVendorUri"));
        $oEncoder->expects($this->at(1))->method('getArticleManufacturerUri')->will($this->returnValue("ArticleManufacturerUri"));
        $oEncoder->expects($this->at(2))->method('getArticleTagUri')->will($this->returnValue("ArticleTagUri"));
        $oEncoder->expects($this->at(3))->method('getArticleUri')->will($this->returnValue("ArticleUri"));
        $oEncoder->expects($this->at(4))->method('getArticleMainUri')->will($this->returnValue("ArticleMainUri"));

        $oView = $this->getMock("Article_Seo", array("getEditObjectId", "_getEncoder", "getActCatType", "getEditLang", "getActCatLang", "getActCatId"));
        $oView->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue($sProdId));
        $oView->expects($this->at(1))->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->at(2))->method('getActCatType')->will($this->returnValue("oxvendor"));
        $oView->expects($this->at(3))->method('getEditLang')->will($this->returnValue(0));

        $oView->expects($this->at(4))->method('getEditObjectId')->will($this->returnValue($sProdId));
        $oView->expects($this->at(5))->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->at(6))->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $oView->expects($this->at(7))->method('getEditLang')->will($this->returnValue(0));

        $oView->expects($this->at(8))->method('getEditObjectId')->will($this->returnValue($sProdId));
        $oView->expects($this->at(9))->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->at(10))->method('getActCatType')->will($this->returnValue("oxtag"));
        $oView->expects($this->at(11))->method('getActCatLang')->will($this->returnValue(0));

        $oView->expects($this->at(12))->method('getEditObjectId')->will($this->returnValue($sProdId));
        $oView->expects($this->at(13))->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->at(14))->method('getActCatType')->will($this->returnValue("oxsomething"));
        $oView->expects($this->at(15))->method('getActCatId')->will($this->returnValue(true));
        $oView->expects($this->at(16))->method('getEditLang')->will($this->returnValue(0));

        $oView->expects($this->at(17))->method('getEditObjectId')->will($this->returnValue($sProdId));
        $oView->expects($this->at(18))->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->at(19))->method('getActCatType')->will($this->returnValue("oxsomething"));
        $oView->expects($this->at(20))->method('getActCatId')->will($this->returnValue(false));
        $oView->expects($this->at(21))->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals("ArticleVendorUri", $oView->getEntryUri());
        $this->assertEquals("ArticleManufacturerUri", $oView->getEntryUri());
        $this->assertEquals("ArticleTagUri", $oView->getEntryUri());
        $this->assertEquals("ArticleUri", $oView->getEntryUri());
        $this->assertEquals("ArticleMainUri", $oView->getEntryUri());
    }

    /**
     * Testing Article_Seo::showCatSelect()
     *
     * @return null
     */
    public function showCatSelect()
    {
        $oView = new Article_Seo();
        $this->assertTrue($oView->showCatSelect());
    }

    /**
     * Article_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = new Article_Seo();
        $this->assertTrue($oView->UNITgetEncoder() instanceof oxSeoEncoderArticle);
    }


    /**
     * Article_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = new Article_Seo();
        $this->assertEquals("object_seo.tpl", $oView->render());
    }

    /**
     * Article_Seo::_getVendorList() test case (regular)
     *
     * @return null
     */
    public function testGetVendorList()
    {
        $oVendor = new oxVendor();
        $oVendor->setId("_test1");
        $oVendor->save();

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField("_test1");

        $oView = new Article_Seo();
        $aList = $oView->UNITgetVendorList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtVendor = reset($aList);
        $this->assertTrue($oArtVendor instanceof oxVendor);
        $this->assertEquals($oVendor->getId(), $oArtVendor->getId());
    }

    /**
     * Article_Seo::_getManufacturerList() test case (regular)
     *
     * @return null
     */
    public function testGetManufacturerList()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId("_test1");
        $oManufacturer->save();

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField("_test1");

        $oView = new Article_Seo();
        $aList = $oView->UNITgetManufacturerList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtManufacturer = reset($aList);
        $this->assertTrue($oArtManufacturer instanceof oxManufacturer);
        $this->assertEquals($oManufacturer->getId(), $oArtManufacturer->getId());
    }


    /**
     * Article_Seo::getActCategory() test case (category)
     *
     * @return null
     */
    public function testGetActCategory()
    {
        oxTestModules::addFunction('oxcategory', 'load', '{ return true; }');

        $oView = new Article_Seo();
        $this->assertTrue($oView->getActCategory() instanceof oxcategory);
    }

    /**
     * Article_Seo::getTag() test case (manufacturer)
     *
     * @return null
     */
    public function testGetTag()
    {
        $oTag1 = $this->getMock("oxManufacturer", array("getId", "getTitle"));
        $oTag1->expects($this->once())->method('getId')->will($this->returnValue("testTagId2"));
        $oTag1->expects($this->never())->method('getTitle')->will($this->returnValue("testTagId"));

        $oTag2 = $this->getMock("oxManufacturer", array("getId", "getTitle"));
        $oTag2->expects($this->once())->method('getId')->will($this->returnValue("testTagId"));
        $oTag2->expects($this->once())->method('getTitle')->will($this->returnValue("testTagId"));

        $aTagList = array($oTag1, $oTag2);

        $oView = $this->getMock("Article_Seo", array("getActCatType", "getActCatId", "getActCatLang", "getEditObjectId", "_getTagList"));
        $oView->expects($this->once())->method('getActCatType')->will($this->returnValue("oxtag"));
        $oView->expects($this->once())->method('getActCatId')->will($this->returnValue("testTagId"));
        $oView->expects($this->once())->method('getActCatLang')->will($this->returnValue(0));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("ObjectId"));
        $oView->expects($this->once())->method('_getTagList')->will($this->returnValue($aTagList));
        $this->assertEquals("testTagId", $oView->getTag());
    }

    /**
     * Article_Seo::getActVendor() test case (manufacturer)
     *
     * @return null
     */
    public function testGetActVendor()
    {
        oxTestModules::addFunction('oxvendor', 'load', '{ return true; }');

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxvendor"));
        $this->assertTrue($oView->getActVendor() instanceof oxvendor);
    }

    /**
     * Article_Seo::getActManufacturer() test case (manufacturer)
     *
     * @return null
     */
    public function testGetActManufacturer()
    {
        oxTestModules::addFunction('oxmanufacturer', 'load', '{ return true; }');

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $this->assertTrue($oView->getActManufacturer() instanceof oxmanufacturer);
    }

    /**
     * Article_Seo::getListType() test case
     *
     * @return null
     */
    public function testGetListType()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxvendor"));
        $this->assertEquals("vendor", $oView->getListType());

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $this->assertEquals("manufacturer", $oView->getListType());

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxtag"));
        $this->assertEquals("tag", $oView->getListType());

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxany"));
        $this->assertNull($oView->getListType());
    }


    /**
     * Article_Seo::_getAltSeoEntryId() test case
     *
     * @return null
     */
    public function testGetAltSeoEntryId()
    {
        $oView = $this->getMock("Article_Seo", array("getEditObjectId"));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->UNITgetAltSeoEntryId());
    }

    /**
     * Article_Seo::getEditLang() test case
     *
     * @return null
     */
    public function testGetEditLang()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatLang"));
        $oView->expects($this->once())->method('getActCatLang')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->getEditLang());
    }

    /**
     * Article_Seo::_getSeoEntryType() test case (tag)
     *
     * @return null
     */
    public function testGetSeoEntryTypeTag()
    {
        $oView = $this->getMock("Article_Seo", array("getTag"));
        $oView->expects($this->once())->method('getTag')->will($this->returnValue(true));
        $this->assertEquals('dynamic', $oView->UNITgetSeoEntryType());
    }

    /**
     * Article_Seo::_getSeoEntryType() test case (default)
     *
     * @return null
     */
    public function testGetSeoEntryType()
    {
        $oView = $this->getMock("Article_Seo", array("getTag"));
        $oView->expects($this->once())->method('getTag')->will($this->returnValue(false));
        $this->assertEquals('oxarticle', $oView->UNITgetSeoEntryType());
    }

    /**
     * Article_Seo::getType() test case (manufacturer)
     *
     * @return null
     */
    public function testGetType()
    {
        $oView = new Article_Seo();
        $this->assertEquals('oxarticle', $oView->UNITgetType());
    }

    /**
     * Article_Seo::_getStdUrl() test case (vendor)
     *
     * @return null
     */
    public function testGetStdUrlVendor()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType", "getTag", "getActCatId"));
        $oView->expects($this->any())->method("getActCatType")->will($this->returnValue("oxvendor"));
        $oView->expects($this->any())->method("getActCatId")->will($this->returnValue("vendor"));
        $oView->expects($this->any())->method("getTag")->will($this->returnValue("testTag"));

        $this->assertEquals("index.php?cl=details&amp;anid=&amp;listtype=vendor&amp;cnid=v_vendor", $oView->UNITgetStdUrl("testOxId"));
    }

    /**
     * Article_Seo::_getStdUrl() test case (manufacturer)
     *
     * @return null
     */
    public function testGetStdUrlManufacturer()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType", "getTag", "getActCatId"));
        $oView->expects($this->any())->method("getActCatType")->will($this->returnValue("oxmanufacturer"));
        $oView->expects($this->any())->method("getActCatId")->will($this->returnValue("manufacturer"));
        $oView->expects($this->any())->method("getTag")->will($this->returnValue("testTag"));

        $this->assertEquals("index.php?cl=details&amp;anid=&amp;listtype=manufacturer&amp;mnid=manufacturer", $oView->UNITgetStdUrl("testOxId"));
    }

    /**
     * Article_Seo::_getStdUrl() test case (tag)
     *
     * @return null
     */
    public function testGetStdUrlTag()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType", "getTag", "getActCatId"));
        $oView->expects($this->any())->method("getActCatType")->will($this->returnValue("oxtag"));
        $oView->expects($this->any())->method("getActCatId")->will($this->returnValue("tag"));
        $oView->expects($this->any())->method("getTag")->will($this->returnValue("testTag"));

        $this->assertEquals("index.php?cl=details&amp;anid=&amp;listtype=tag&amp;searchtag=testTag", $oView->UNITgetStdUrl("testOxId"));
    }

    /**
     * Article_Seo::_getStdUrl() test case (default)
     *
     * @return null
     */
    public function testGetStdUrlDefault()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType", "getTag", "getActCatId"));
        $oView->expects($this->any())->method("getActCatType")->will($this->returnValue("oxanytype"));
        $oView->expects($this->any())->method("getActCatId")->will($this->returnValue("catid"));

        $this->assertEquals("index.php?cl=details&amp;anid=&amp;cnid=catid", $oView->UNITgetStdUrl("testOxId"));

    }

    /**
     * Article_Seo::getActCatType() test case
     *
     * @return null
     */
    public function testGetActCatType()
    {
        modConfig::setRequestParameter("aSeoData", null);

        $oView = $this->getMock("Article_Seo", array("getSelectionList"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => "value"))));
        $this->assertEquals("type", $oView->getActCatType());

        modConfig::setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals("type", $oView->getActCatType());
    }

    /**
     * Article_Seo::getActCatLang() test case
     *
     * @return null
     */
    public function testGetActCatLang()
    {
        modConfig::setRequestParameter("aSeoData", null);

        $oView = $this->getMock("Article_Seo", array("getSelectionList"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => "value"))));
        $this->assertEquals(999, $oView->getActCatLang());

        modConfig::setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals(999, $oView->getActCatLang());
    }

    /**
     * Article_Seo::getActCatId() test case
     *
     * @return null
     */
    public function testGetActCatId()
    {
        modConfig::setRequestParameter("aSeoData", null);

        $oItem = $this->getMock("oxManufacturer", array("getId"));
        $oItem->expects($this->once())->method("getId")->will($this->returnValue("value"));

        $oView = $this->getMock("Article_Seo", array("getSelectionList", "getActCatType", "getActCatLang"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => array($oItem)))));
        $oView->expects($this->once())->method("getActCatType")->will($this->returnValue("type"));
        $oView->expects($this->once())->method("getActCatLang")->will($this->returnValue(999));
        $this->assertEquals("value", $oView->getActCatId());

        modConfig::setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals("value", $oView->getActCatId());
    }

    /**
     * Article_Seo::_getCategoryList() test case
     *
     * @return null
     */
    public function testGetCategoryList()
    {
        $sO2CView = getViewName('oxobject2category');
        $sQ = "select oxarticles.oxid from oxarticles left join {$sO2CView} on
               oxarticles.oxid={$sO2CView}.oxobjectid where
               oxarticles.oxactive='1' and {$sO2CView}.oxobjectid is not null";

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sProdId = $oDb->getOne($sQ);

        // must be existing
        $this->assertTrue((bool) $sProdId);

        $oProduct = new oxArticle();
        $oProduct->load($sProdId);

        $sQ = "select oxobject2category.oxcatnid as oxid from {$sO2CView} as oxobject2category where oxobject2category.oxobjectid="
              . $oDb->quote($oProduct->getId()) . " union " . $oProduct->getSqlForPriceCategories('oxid');

        $sQ = "select count(*) from ( $sQ ) as _tmp";
        $iCount = $oDb->getOne($sQ);

        $oView = new Article_Seo();
        $aList = $oView->UNITgetCategoryList($oProduct);

        // must be have few assignments
        $this->assertTrue($iCount > 0);
        $this->assertEquals($iCount, count($aList));
    }

    /**
     * Article_Seo::_getTagList() test case
     *
     * @return null
     */
    public function testGetTagList()
    {
        $sQ = "select oxarticles.oxid from oxarticles left join oxartextends on
               oxarticles.oxid=oxartextends.oxid where
               oxarticles.oxactive='1' and oxartextends.oxid is not null and oxartextends.oxtags != ''";

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sProdId = $oDb->getOne($sQ);

        // must be existing
        $this->assertTrue((bool) $sProdId);

        $oProduct = new oxArticle();
        $oProduct->load($sProdId);

        $oArticleTagList = new oxArticleTagList();
        $oArticleTagList->load($sProdId);
        $oTagSet = $oArticleTagList->get();
        $aTags = $oTagSet->get();

        $oView = new Article_Seo();
        $aList = $oView->UNITgetTagList($oProduct, 0);

        // must be have few assignments
        $this->assertTrue(count($aTags) > 0);
        $this->assertEquals(count($aTags), count($aList));
    }

    /**
     * Article_Seo::getSelectionList() test case
     *
     * @return null
     */
    public function testGetSelectionList()
    {
        $iProdId = oxDb::getDb()->getOne("select oxid from oxarticles");
        $iEditLang = oxRegistry::getLang()->getEditLanguage();

        $oProduct = oxNew('oxarticle');
        $oProduct->load($iProdId);
        $aLangs = $oProduct->getAvailableInLangs();

        $oView = $this->getMock("Article_Seo", array("getEditObjectId", "_getCategoryList", "_getVendorList", "_getManufacturerList", "_getTagList"));
        $oView->expects($this->any())->method("getEditObjectId")->will($this->returnValue($iProdId));
        $oView->expects($this->any())->method("_getCategoryList")->will($this->returnValue("CategoryList"));
        $oView->expects($this->any())->method("_getVendorList")->will($this->returnValue("VendorList"));
        $oView->expects($this->any())->method("_getManufacturerList")->will($this->returnValue("ManufacturerList"));
        $oView->expects($this->any())->method("_getTagList")->will($this->returnValue("TagList"));

        $aList = array();
        $aList["oxcategory"][$iEditLang] = "CategoryList";
        $aList["oxvendor"][$iEditLang] = "VendorList";
        $aList["oxmanufacturer"][$iEditLang] = "ManufacturerList";
        foreach ($aLangs as $iLang => $sLangTitle) {
            $aList["oxtag"][$iLang] = "TagList";
        }

        $this->assertEquals($aList, $oView->getSelectionList());
    }

    /**
     * Article_Seo::processParam() test case (tag)
     *
     * @return null
     */
    public function testProcessParamTag()
    {
        $oView = $this->getMock("Article_Seo", array("getTag"));
        $oView->expects($this->once())->method("getTag")->will($this->returnValue(true));
        $this->assertEquals("", $oView->processParam("testParam"));
    }

    /**
     * Article_Seo::processParam() test case (any other than tag)
     *
     * @return null
     */
    public function testProcessParam()
    {
        $oView = $this->getMock("Article_Seo", array("getTag", "getActCatId"));
        $oView->expects($this->once())->method("getTag")->will($this->returnValue(false));
        $oView->expects($this->once())->method("getActCatId")->will($this->returnValue("testParam2"));
        $this->assertEquals("testParam2", $oView->processParam("testParam1#testParam2#0"));
    }

    /**
     * Vendor_Seo::isEntryFixed() test case
     *
     * @return null
     */
    public function testIsEntryFixed()
    {
        $ShopId = oxRegistry::getConfig()->getShopId();
        $iLang = 0;
        $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxparams ) values
                                 ( 'objectid', 'ident', '{$ShopId}', '{$iLang}', 'stdurl', 'seourl', 'type', 1, 'catid' )";
        oxDb::getDb()->execute($sQ);

        $oView = $this->getMock("Article_Seo", array("_getSaveObjectId", "getActCatId", "getEditLang", "processParam"));
        $oView->expects($this->at(0))->method('_getSaveObjectId')->will($this->returnValue("objectid"));
        $oView->expects($this->at(1))->method('getEditLang')->will($this->returnValue(0));
        $oView->expects($this->at(2))->method('getActCatId')->will($this->returnValue("catid"));
        $oView->expects($this->at(3))->method('processParam')->will($this->returnValue("catid"));

        $oView->expects($this->at(4))->method('_getSaveObjectId')->will($this->returnValue("nonexistingobjectid"));
        $oView->expects($this->at(5))->method('getEditLang')->will($this->returnValue(0));
        $oView->expects($this->at(6))->method('getActCatId')->will($this->returnValue("catid"));
        $oView->expects($this->at(7))->method('processParam')->will($this->returnValue("catid"));

        $this->assertTrue($oView->isEntryFixed());
        $this->assertFalse($oView->isEntryFixed());
    }
}