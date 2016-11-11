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
namespace Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use OxidEsales\EshopCommunity\Application\Model\PaymentList;
use OxidEsales\EshopCommunity\Application\Model\DeliverySetList;
use OxidEsales\EshopCommunity\Application\Model\DeliveryList;

use \oxField;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing details class
 */
class DetailsTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxrecommlists');
        $this->cleanUpTable('oxobject2list');
        $this->cleanUpTable('oxmediaurls');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxartextends');

        oxDb::getDB()->execute('delete from oxreviews where oxobjectid = "test"');
        oxDb::getDB()->execute('delete from oxratings');
        parent::tearDown();
    }

    /**
     * Test get canonical url with seo on.
     *
     * @return null
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $oProduct = $this->getMock("oxarticle", array("getBaseSeoLink", "getBaseStdLink"));
        $oProduct->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $oProduct->expects($this->never())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));

        $oDetailsView = $this->getMock("details", array("getProduct"));
        $oDetailsView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals("testSeoUrl", $oDetailsView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     *
     * @return null
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $oProduct = $this->getMock("oxarticle", array("getBaseSeoLink", "getBaseStdLink"));
        $oProduct->expects($this->never())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $oProduct->expects($this->once())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));

        $oDetailsView = $this->getMock("details", array("getProduct"));
        $oDetailsView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals("testStdUrl", $oDetailsView->getCanonicalUrl());
    }

    /**
     * Test draw parent url when active product is a variant and only one is buyable.
     *
     * @return null
     */
    public function testDrawParentUrlWhenActiveProductIsVariantAndOnlyOneIsBuyable()
    {
        $oParent = oxNew('oxArticle');
        $oParent->load("1126");
        $oParent->setId("_testParent");
        $oParent->save();

        $oVariant = oxNew('oxArticle');
        $oVariant->load("1126");
        $oVariant->setId("_testVariant");
        $oVariant->oxarticles__oxparentid = new oxField($oParent->getId());
        $oVariant->save();

        $oDetailsView = $this->getMock("details", array("getProduct"));
        $oDetailsView->expects($this->any())->method('getProduct')->will($this->returnValue($oVariant));
        $this->assertTrue($oDetailsView->drawParentUrl());
    }

    /**
     * Returns variants and expected results
     *
     * @return array
     */
    public function variantProvider()
    {
        return array(
            array(null, null, array()),
            array(array('abc'), null, array('varselid[0]' => 'abc')),
            array(null, array('abc'), array('sel[0]' => 'abc')),
            array(array('abc', 'cbe', 'ghf'), null, array('varselid[0]' => 'abc', 'varselid[1]' => 'cbe', 'varselid[2]' => 'ghf')),
            array(null, array('abc', 'cbe', 'ghf'), array('sel[0]' => 'abc', 'sel[1]' => 'cbe', 'sel[2]' => 'ghf')),
            array(array('abc', 'cbe', 'ghf'), array('efg', 'hjk', 'lmn'), array('varselid[0]' => 'abc', 'varselid[1]' => 'cbe', 'varselid[2]' => 'ghf', 'sel[0]' => 'efg', 'sel[1]' => 'hjk', 'sel[2]' => 'lmn')),
        );
    }

    /**
     * Test getNavigationParams when passing various variants.
     *
     * @dataProvider variantProvider
     */
    public function testGetNavigationParams($aVariants, $aSelectionVariants, $aExpected)
    {
        $this->setRequestParameter('varselid', $aVariants);
        $this->setRequestParameter('sel', $aSelectionVariants);

        $oDetails = oxNew('Details');
        $oDetails->setParent(oxNew('oxUBase'));

        $aExpected = array_merge($aExpected, $oDetails->getParent()->getNavigationParams());
        $this->assertEquals($aExpected, $oDetails->getNavigationParams());
    }

    /**
     * Test get additionall url parameters.
     *
     * @return null
     */
    public function testGetAddUrlParams()
    {
        $oDetailsView = $this->getMock("details", array("getListType", "getDynUrlParams"));
        $oDetailsView->expects($this->once())->method('getListType')->will($this->returnValue("somelisttype"));
        $oDetailsView->expects($this->never())->method('getDynUrlParams');
        $this->assertNull($oDetailsView->UNITgetAddUrlParams());

        $oDetailsView = $this->getMock("details", array("getListType", "getDynUrlParams"));
        $oDetailsView->expects($this->once())->method('getListType')->will($this->returnValue("search"));
        $oDetailsView->expects($this->once())->method('getDynUrlParams')->will($this->returnValue("searchparams"));
        $this->assertEquals("searchparams", $oDetailsView->UNITgetAddUrlParams());
    }

    /**
     * Test process product urls.
     *
     * @return null
     */
    public function testProcessProduct()
    {
        $oProduct = $this->getMock("oxarticle", array("setLinkType", "appendLink"));
        $oProduct->expects($this->once())->method('setLinkType')->with($this->equalTo("search"));
        $oProduct->expects($this->once())->method('appendLink')->with($this->equalTo("searchparams"));

        $oDetailsView = $this->getMock("details", array("getLinkType", "_getAddUrlParams"));
        $oDetailsView->expects($this->once())->method('getLinkType')->will($this->returnValue("search"));
        $oDetailsView->expects($this->once())->method('_getAddUrlParams')->will($this->returnValue("searchparams"));

        $oDetailsView->UNITprocessProduct($oProduct);

    }

    /**
     * Test get link type.
     *
     * @return null
     */
    public function testGetLinkType()
    {
        $this->setRequestParameter('listtype', 'vendor');
        $oDetailsView = $this->getMock("details", array('getActiveCategory'));
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertEquals(OXARTICLE_LINKTYPE_VENDOR, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', 'manufacturer');
        $oDetailsView = $this->getMock("details", array('getActiveCategory'));
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertEquals(OXARTICLE_LINKTYPE_MANUFACTURER, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock("details", array('getActiveCategory'));
        $oDetailsView->expects($this->once())->method('getActiveCategory')->will($this->returnValue(null));
        $this->assertEquals(OXARTICLE_LINKTYPE_CATEGORY, $oDetailsView->getLinkType());

        $oCategory = $this->getMock("oxcategory", array('isPriceCategory'));
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(true));

        $this->setRequestParameter('listtype', "recommlist");
        $oDetailsView = $this->getMock("details", array('getActiveCategory'));
        $oDetailsView->expects($this->never())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(OXARTICLE_LINKTYPE_RECOMM, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock("details", array('getActiveCategory'));
        $oDetailsView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(OXARTICLE_LINKTYPE_PRICECATEGORY, $oDetailsView->getLinkType());
    }

    /**
     * Test get parent product.
     *
     * @return null
     */
    public function testGetParentProduct()
    {
        $oProduct = $this->getMock("oxarticle", array("isBuyable"));
        $oProduct->expects($this->any())->method('isBuyable')->will($this->returnValue(true));

        $oDetailsView = $this->getMock("details", array("getProduct"));
        $oDetailsView->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));

        $oProduct = $oDetailsView->UNITgetParentProduct('1126');
        $this->assertTrue($oProduct instanceof article);
        $this->assertEquals('1126', $oProduct->getId());
    }

    /**
     * Test get parent of non existing product.
     *
     * @return null
     */
    public function testGetProductNotExistingProduct()
    {
        $this->setRequestParameter('anid', 'notexistingproductid');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( \$aA[0] ); }");

        try {
            $oDetailsView = oxNew('Details');
            $oDetailsView->getProduct();
        } catch (Exception $oExcp) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $oExcp->getMessage(), 'result does not match');

            return;
        }
        $this->fail('product should not be returned');
    }

    /**
     * Test case for #0002223: variant page works even if parent article is inactive
     *
     * @return null
     */
    public function testForBugEntry0002223()
    {
        $sQ = "select oxid from oxarticles where oxparentid!='' and oxactive = 1";
        $this->setRequestParameter('anid', oxDb::getDb()->getOne($sQ));
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( \$aA[0] ); }");

        $oParentProduct = $this->getMock("oxArticle", array("isVisible"));
        $oParentProduct->expects($this->once())->method('isVisible')->will($this->returnValue(false));

        try {
            $oDetailsView = $this->getMock("details", array("_getParentProduct"));
            $oDetailsView->expects($this->once())->method('_getParentProduct')->will($this->returnValue($oParentProduct));
            $oDetailsView->getProduct();
        } catch (Exception $oExcp) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $oExcp->getMessage(), 'result does not match');

            return;
        }
        $this->fail('product should not be returned');
    }

    /**
     * Test get invisible product.
     *
     * @return null
     */
    public function testGetProductInvisibleProduct()
    {
        $oProduct = $this->getMock('oxarticle', array('isVisible'));
        $oProduct->expects($this->once())->method('isVisible')->will($this->returnValue(false));

        $this->setRequestParameter('anid', 'notexistingproductid');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( \$aA[0] ); }");

        try {
            $oDetailsView = $this->getProxyClass('details');
            $oDetailsView->setNonPublicVar('_oProduct', $oProduct);
            $oDetailsView->getProduct();
        } catch (Exception $oExcp) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $oExcp->getMessage(), 'result does not match');

            return;
        }
        $this->fail('product should not be returned');
    }

    /**
     * Test noIndex property getter.
     *
     * @return null
     */
    public function testNoIndex()
    {
        $this->setRequestParameter('listtype', 'vendor');

        $oDetailsView = oxNew('Details');
        $this->assertEquals(2, $oDetailsView->noIndex());
    }

    /**
     * Test noIndex property getter.
     *
     * @return null
     */
    public function testNoIndex_unknowntype()
    {
        $this->setRequestParameter('listtype', 'unknown');

        $oView = oxNew('Details');
        $this->assertSame(0, $oView->noIndex());
    }

    /**
     * Test get product.
     *
     * @return null
     */
    public function testGetProduct()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $this->setRequestParameter('anid', '2000');
        $oDetails = $this->getProxyClass('details');
        $oDetails->init();
        $this->assertEquals('2000', $oDetails->getProduct()->getId());
    }

    /**
     * Test get product.
     *
     * @return null
     */
    public function testGetProductWithDirectVariant()
    {
        $oProduct = $this->getMock('oxarticle', array('load', 'getVariantSelections'));
        $oProduct->expects($this->once())->method('load')
            ->with($this->equalTo('anid__'))
            ->will($this->returnValue(1));
        $oProduct->expects($this->once())->method('getVariantSelections')
            ->with($this->equalTo('varselid__'))
            ->will($this->returnValue(array('oActiveVariant' => 'actvar', 'blPerfectFit' => true)));
        oxTestModules::addModuleObject('oxarticle', $oProduct);

        $this->setRequestParameter('anid', 'anid__');
        $this->setRequestParameter('varselid', 'varselid__');

        $oDetailsView = $this->getProxyClass('details');
        $oDetailsView->setNonPublicVar('_blIsInitialized', 1);
        $this->assertEquals('actvar', $oDetailsView->getProduct());
    }

    /**
     * Test get product.
     *
     * @return null
     */
    public function testGetProductWithIndirectVariant()
    {
        $oProduct = $this->getMock('oxarticle', array('load', 'getVariantSelections'));
        $oProduct->expects($this->once())->method('load')
            ->with($this->equalTo('anid__'))
            ->will($this->returnValue(1));
        $oProduct->expects($this->once())->method('getVariantSelections')
            ->with($this->equalTo('varselid__'))
            ->will($this->returnValue(array('oActiveVariant' => 'actvar', 'blPerfectFit' => false)));
        oxTestModules::addModuleObject('oxarticle', $oProduct);

        $this->setRequestParameter('anid', 'anid__');
        $this->setRequestParameter('varselid', 'varselid__');

        $oDetailsView = $this->getProxyClass('details');
        $oDetailsView->setNonPublicVar('_blIsInitialized', 1);
        $this->assertSame($oProduct, $oDetailsView->getProduct());
    }

    /**
     * Test draw parent url.
     *
     * @return null
     */
    public function testDrawParentUrl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxparentid = new oxField('parent');

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertTrue($oDetails->drawParentUrl());
    }

    /**
     * Test get picture gallery.
     *
     * @return null
     */
    public function testGetPictureGallery()
    {
        $sArtID = "096a1b0849d5ffa4dd48cd388902420b";

        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtID);
        $sActPic = $this->getConfig()->getPictureUrl(null) . "generated/product/1/380_340_75/" . basename($oArticle->oxarticles__oxpic1->value);

        $oDetails = $this->getMock('details', array("getPicturesProduct"));
        $oDetails->expects($this->once())->method('getPicturesProduct')->will($this->returnValue($oArticle));
        $aPicGallery = $oDetails->getPictureGallery();

        $this->assertEquals($sActPic, $aPicGallery['ActPic']);
    }

    /**
     * Test get active picture id.
     *
     * @return null
     */
    public function testGetActPictureId()
    {
        $aPicGallery = array('ActPicID' => 'aaa');
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertEquals('aaa', $oDetails->getActPictureId());
    }


    /**
     * Test get active picture.
     *
     * @return null
     */
    public function testGetActPicture()
    {
        $aPicGallery = array('ActPic' => 'aaa');
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertEquals('aaa', $oDetails->getActPicture());
    }

    /**
     * Test get pictures.
     *
     * @return null
     */
    public function testGetPictures()
    {
        $aPicGallery = array('Pics' => 'aaa');
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertEquals('aaa', $oDetails->getPictures());
    }

    /**
     * Test show zoom pictures.
     *
     * @return null
     */
    public function testShowZoomPics()
    {
        $aPicGallery = array('ZoomPic' => true);
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertTrue($oDetails->showZoomPics());
    }

    /**
     * Test get select lists.
     *
     * @return null
     */
    public function testGetSelectLists()
    {
        $this->setConfigParam('bl_perfLoadSelectLists', true);
        $oArticle = $this->getMock('oxarticle', array('getSelectLists'));
        $oArticle->expects($this->any())->method('getSelectLists')->will($this->returnValue("aaa"));

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals('aaa', $oDetails->getSelectLists());
    }

    /**
     * Test get reviews.
     *
     * @return null
     */
    public function testGetReviews()
    {
        $oArticle = $this->getMock('oxarticle', array('getReviews'));
        $oArticle->expects($this->any())->method('getReviews')->will($this->returnValue("aaa"));

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals('aaa', $oDetails->getReviews());
    }

    /**
     * Test get similar products.
     *
     * @return null
     */
    public function testGetSimilarProducts()
    {
        $oDetails = $this->getProxyClass('Details');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");
        $oDetails->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oDetails->getSimilarProducts();
        $this->assertTrue($oList instanceof articlelist);
        // Demo data is different in EE and CE
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 4 : 5;
        $this->assertEquals($expectedCount, count($oList));
    }

    /**
     * Test get crossselling.
     *
     * @return null
     */
    public function testGetCrossSelling()
    {
        $oDetails = $this->getProxyClass('details');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");
        $oDetails->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oDetails->getCrossSelling();
        $this->assertTrue($oList instanceof articlelist);

        // Demo data is different in EE and CE
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 3 : 2;
        $this->assertEquals($expectedCount, $oList->count());
    }

    /**
     * Test get ids for similar recomendation list.
     *
     * @return null
     */

    public function testGetSimilarRecommListIds()
    {
        $articleId = "articleId";
        $aArrayKeys = array($articleId);
        $oProduct = $this->getMock("oxarticle", array("getId"));
        $oProduct->expects($this->once())->method("getId")->will($this->returnValue($articleId));

        $oDetails = $this->getMock("details", array("getProduct"));
        $oDetails->expects($this->once())->method("getProduct")->will($this->returnValue($oProduct));
        $this->assertEquals($aArrayKeys, $oDetails->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getProduct()");
    }

    /**
     * Test get accessories.
     *
     * @return null
     */
    public function testGetAccessoires()
    {
        $oArticle = $this->getMock('oxarticle', array('getAccessoires'));
        $oArticle->expects($this->any())->method('getAccessoires')->will($this->returnValue("aaa"));

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals("aaa", $oDetails->getAccessoires());
    }

    /**
     * Test get also bought these products.
     *
     * @return null
     */
    public function testGetAlsoBoughtTheseProducts()
    {
        $oArticle = $this->getMock('oxarticle', array('getCustomerAlsoBoughtThisProducts'));
        $oArticle->expects($this->any())->method('getCustomerAlsoBoughtThisProducts')->will($this->returnValue("aaa"));

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals("aaa", $oDetails->getAlsoBoughtTheseProducts());
    }

    /**
     * Test is product added to price allarm.
     *
     * @return null
     */
    public function testIsPriceAlarm()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(1, oxField::T_RAW);

        $oView = $this->getMock('details', array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals(false, $oView->isPriceAlarm());
    }

    /**
     * Test is product added to price allarm - true test.
     *
     * @return null
     */
    public function testIsPriceAlarm_true()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(0, oxField::T_RAW);

        $oView = $this->getMock('details', array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oArticle));

        $this->assertEquals(true, $oView->isPriceAlarm());
    }

    /**
     * Test meta keywords generation.
     */
    public function testMetaKeywords()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $oProduct = oxNew("oxArticle");
        $oProduct->load("1849");
        $oProduct->oxarticles__oxsearchkeys->value = 'testValue1 testValue2   testValue3 <br> ';

        //building category tree for category "Bar-eqipment"
        $sCatId = '8a142c3e49b5a80c1.23676990';

        $oCategoryTree = oxNew('oxCategoryList');
        $oCategoryTree->buildTree($sCatId, false, false, false);

        $oDetails = $this->getMock('details', array('getProduct', 'getCategoryTree'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->expects($this->any())->method('getCategoryTree')->will($this->returnValue($oCategoryTree));

        $sKeywords = $oProduct->oxarticles__oxtitle->value;

        //adding breadcrumb
        $sKeywords .= ", Geschenke, Bar-Equipment";

        $oView = oxNew('oxUBase');
        $sTestKeywords = $oView->UNITprepareMetaKeyword($sKeywords, true) . ", testvalue1, testvalue2, testvalue3";

        $this->assertEquals($sTestKeywords, $oDetails->UNITprepareMetaKeyword(null));
    }

    /**
     * Test meta meta description generation
     *
     * @return null
     */
    public function testMetaDescriptionWithLongDesc()
    {
        $oProduct = oxNew("oxArticle");
        $oProduct->load("1849");

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));
        $sMeta = $oProduct->oxarticles__oxtitle->value . ' - ' . $oProduct->getLongDescription();

        $oView = oxNew('oxUBase');
        $this->assertEquals($oView->UNITprepareMetaDescription($sMeta, 200, false), $oDetails->UNITprepareMetaDescription(null));
    }

    /**
     * Test meta meta description generation when short desc is empty (should use long desc).
     *
     * @return null
     */
    public function testMetaDescriptionWithLongDescWithSmartyParsing()
    {
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', true);

        $oProduct = $this->getMock('oxarticle', array('getLongDesc', 'getLongDescription'));
        $oProduct->expects($this->once())->method('getLongDesc')->will($this->returnValue('parsed description'));
        $oProduct->expects($this->never())->method('getLongDescription')->will($this->returnValue('not parsed description'));
        $oProduct->oxarticles__oxshortdesc = new oxField('Short description', oxField::T_RAW);
        $oProduct->oxarticles__oxtitle = new oxField('Title', oxField::T_RAW);

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $sMeta = 'Title - parsed description';

        $oView = oxNew('oxUBase');
        $this->assertEquals($oView->UNITprepareMetaDescription($sMeta, 200, false), $oDetails->UNITprepareMetaDescription(null));
    }

    /**
     * Test search title setter/getter.
     *
     * @return null
     */
    public function testSetGetSearchTitle()
    {
        $oDetails = $this->getProxyClass('details');
        $oDetails->setSearchTitle("tetsTitle");

        $this->assertEquals("tetsTitle", $oDetails->getSearchTitle());
    }

    /**
     * Test category path setter/getter.
     *
     * @return null
     */
    public function testSetGetCatTreePath()
    {
        $oDetails = $this->getProxyClass('details');
        $oDetails->setCatTreePath("tetsPath");

        $this->assertEquals("tetsPath", $oDetails->getCatTreePath());
    }

    /**
     * Test article picture getter.
     *
     * @return null
     */
    public function testGetArtPic()
    {
        $aPicGallery = array('Pics' => array(1 => 'aaa'));
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertEquals('aaa', $oDetails->getArtPic(1));
    }

    /**
     * Test base view class title getter.
     *
     * @return null
     */
    public function testGetTitle()
    {
        $this->_oProduct->oxarticles__oxtitle->value . ($this->_oProduct->oxarticles__oxvarselect->value ? ' ' . $this->_oProduct->oxarticles__oxvarselect->value : '');

        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtitle = new oxField('product title');
        $oProduct->oxarticles__oxvarselect = new oxField('and varselect');

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals('product title and varselect', $oDetails->getTitle());
    }

    /**
     * Test base view class title getter - no product.
     *
     * @return null
     */
    public function testGetTitle_noproduct()
    {
        $oView = $this->getMock('Details', array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue(null));
        $this->assertNull($oView->getTitle());
    }

    /**
     * Test cannonical URL getter - no product.
     *
     * @return null
     */
    public function testGetCanonicalUrl_noproduct()
    {
        $oView = $this->getMock('Details', array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue(null));
        $this->assertNull($oView->getCanonicalUrl());
    }

    /**
     * Test review saving.
     *
     * @return null
     */
    public function testSaveReview()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', 'oxdefaultadmin');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxArticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->any())->method('addToRatingAverage');

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oDetails */
        $oDetails = $this->getMock('Details', array('getProduct', 'canAcceptFormData'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $oDetails->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving without user.
     *
     * @return null
     */
    public function testSaveReviewIfUserNotSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', null);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxArticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->any())->method('addToRatingAverage');

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oDetails */
        $oDetails = $this->getMock('Details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving without rating.
     *
     * @return null
     */
    public function testSaveReviewIfOnlyReviewIsSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', null);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->any())->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oDetails */
        $oDetails = $this->getMock('details', array('getProduct', 'getUser', 'canAcceptFormData'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oDetails->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $oDetails->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select 1 from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving with wrong rating.
     *
     * @return null
     */
    public function testSaveReviewIfWrongRating()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', 6);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->any())->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oDetails */
        $oDetails = $this->getMock('details', array('getProduct', 'getUser', 'canAcceptFormData'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oDetails->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $oDetails->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test only review rating saving.
     *
     * @return null
     */
    public function testSaveReviewIfOnlyRatingIsSet()
    {
        $this->setRequestParameter('rvw_txt', null);
        $this->setRequestParameter('artrating', 3);
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', 'oxdefaultadmin');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->any())->method('addToRatingAverage');

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oDetails */
        $oDetails = $this->getMock('details', array('getProduct', 'canAcceptFormData'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oDetails->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $oDetails->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testAddToRecommIfOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("details", array("getViewConfig", 'getArticleList'));
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getArticleList');

        $this->setRequestParameter('anid', 'asd');
        oxTestModules::addFunction('oxrecommlist', 'load', '{throw new Exception("should not come here");}');

        $this->assertSame(null, $oRecomm->addToRecomm());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testAddToRecommIfOn()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(true));

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxArticle', array('getId'));
        $oProduct->expects($this->once())->method('getId')->will($this->returnValue('test_artid'));

        $this->setRequestParameter('recomm', 'test_recomm');
        $this->setRequestParameter('recomm_txt', 'test_recommtext');

        /** @var oxRecommList|PHPUnit_Framework_MockObject_MockObject $oRecommList */
        $oRecommList = $this->getMock('oxRecommList', array('load', 'addArticle'));
        $oRecommList->expects($this->once())->method('load')->with($this->equalTo('test_recomm'));
        $oRecommList->expects($this->once())->method('addArticle')->with($this->equalTo('test_artid'), $this->equalTo('test_recommtext'));

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("details", array("getViewConfig", 'getProduct'));
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        oxTestModules::addModuleObject('oxrecommlist', $oRecommList);

        $this->assertSame(null, $oRecomm->addToRecomm());
    }

    /**
     * Testing Details::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {

        $details = oxNew('Details');

        $this->setRequestParameter('listtype', 'search');
        $this->assertTrue(count($details->getBreadCrumb()) >= 1);

        $details = oxNew('details');
        $this->setRequestParameter('listtype', 'recommlist');
        $this->assertTrue(count($details->getBreadCrumb()) >= 1);

        $details = oxNew('details');
        $this->setRequestParameter('listtype', 'vendor');
        $this->assertTrue(count($details->getBreadCrumb()) >= 1);

        $this->setRequestParameter('listtype', 'aaa');

        $oCat1 = $this->getMock('oxcategory', array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock('oxcategory', array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oView = $this->getMock("details", array("getCatTreePath"));
        $oView->expects($this->once())->method('getCatTreePath')->will($this->returnValue(array($oCat1, $oCat2)));

        $this->assertTrue(count($oView->getBreadCrumb()) >= 1);
    }

    /**
     * details::getVariantSelections() test case
     *
     * @return null
     */
    public function testGetVariantSelections()
    {
        $oProduct = $this->getMock("oxarticle", array("getVariantSelections"));
        $oProduct->expects($this->once())->method("getVariantSelections")->will($this->returnValue("varselections"));
        //$oProduct->expects( $this->never() )->method( "getId" );

        // no parent
        $oView = $this->getMock("details", array("getProduct", "_getParentProduct"));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));
        $oView->expects($this->once())->method('_getParentProduct')->will($this->returnValue(false));

        $this->assertEquals("varselections", $oView->getVariantSelections());

        $oProduct = $this->getMock("oxarticle", array("getVariantSelections"));
        $oProduct->expects($this->never())->method('getVariantSelections')->will($this->returnValue("varselections"));
        //$oProduct->expects( $this->once() )->method( 'getId');

        $oParent = $this->getMock("oxarticle", array("getVariantSelections"));
        $oParent->expects($this->once())->method('getVariantSelections')->will($this->returnValue("parentselections"));

        // has parent
        $oView = $this->getMock("details", array("getProduct", "_getParentProduct"));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));
        $oView->expects($this->once())->method('_getParentProduct')->will($this->returnValue($oParent));

        $this->assertEquals("parentselections", $oView->getVariantSelections());
    }

    /**
     * details::getPicturesProduct() test case
     *
     * @return null
     */
    public function testGetPicturesProductNoVariantInfo()
    {
        $oProduct = $this->getMock("stdclass", array("getId"));
        $oProduct->expects($this->never())->method('getId');

        // no picture product id
        $oView = $this->getMock("details", array("getProduct", 'getVariantSelections'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));
        $oView->expects($this->once())->method('getVariantSelections')->will($this->returnValue(false));
        $this->assertSame($oProduct, $oView->getPicturesProduct());
    }

    public function testGetPicturesProductWithNoPerfectFitVariant()
    {
        $oProduct = $this->getMock("stdclass", array("getId"));
        $oProduct->expects($this->never())->method('getId');

        $aInfo = array(
            'oActiveVariant' => $oProduct,
            'blPerfectFit'   => false
        );
        // no picture product id
        $oView = $this->getMock("details", array("getProduct", 'getVariantSelections'));
        $oView->expects($this->never())->method('getProduct');
        $oView->expects($this->once())->method('getVariantSelections')->will($this->returnValue($aInfo));
        $this->assertSame($oProduct, $oView->getPicturesProduct());
    }

    public function testGetPicturesProductWithPerfectFitVariant()
    {
        $oProduct = $this->getMock("stdclass", array("getId"));
        $oProduct->expects($this->never())->method('getId');

        $aInfo = array(
            'oActiveVariant' => $oProduct,
            'blPerfectFit'   => true
        );
        // no picture product id
        $oView = $this->getMock("details", array("getProduct", 'getVariantSelections'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue('prod'));
        $oView->expects($this->once())->method('getVariantSelections')->will($this->returnValue($aInfo));
        $this->assertEquals('prod', $oView->getPicturesProduct());
    }

    public function testGetSearchParamForHtml()
    {
        $oDetails = $this->getProxyClass('details');
        $this->setRequestParameter('searchparam', 'aaa');

        $this->assertEquals('aaa', $oDetails->getSearchParamForHtml());
    }

    public function testGetViewId_testcache()
    {
        $oView = $this->getProxyClass('Details');

        $oView->setNonPublicVar('_sViewId', '_testViewId');
        $this->assertSame('_testViewId', $oView->getViewId());
    }

    public function testGetViewId_testhash()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $oView = $this->getProxyClass('Details');

        $oBaseView = oxNew('oxUBase');
        $sBaseViewId = $oBaseView->getViewId();

        $this->setRequestParameter('anid', 'test_anid');
        $this->setRequestParameter('cnid', 'test_cnid');
        $this->setRequestParameter('listtype', 'search');
        $this->setRequestParameter('searchparam', 'test_sparam');
        $this->setRequestParameter('renderPartial', 'test_render');
        $this->setRequestParameter('varselid', 'test_varselid');
        $aFilters = array('test_cnid' => array(0 => 'test_filters'));
        $this->setSessionParam('session_attrfilter', $aFilters);

        $sExpected = $sBaseViewId . '|test_anid|';

        $sResp = $oView->getViewId();
        $this->assertSame($sExpected, $sResp);
        $this->assertSame($sExpected, $oView->getNonPublicVar('_sViewId'));
    }

    public function testIsReviewActive()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_perfLoadReviews'))->will($this->returnValue('test_isactive'));

        $oView = $this->getMock('Details', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertSame('test_isactive', $oView->isReviewActive());
    }

    public function testAddme_invalidEmail()
    {
        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $oEmail */
        $oEmail = $this->getMock('oxEmail', array('sendPricealarmNotification'));
        $oEmail->expects($this->never())->method('sendPricealarmNotification');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        /** @var oxPriceAlarm|PHPUnit_Framework_MockObject_MockObject $oPriceAlarm */
        $oPriceAlarm = $this->getMock('oxpricealarm', array('save'));
        $oPriceAlarm->expects($this->never())->method('save');
        oxTestModules::addModuleObject('oxpricealarm', $oPriceAlarm);

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getProxyClass('Details');

        $aParams = array();
        $aParams['email'] = 'test_email';

        $this->setRequestParameter('pa', $aParams);
        $oView->addme();
        $this->assertSame(0, $oView->getNonPublicVar('_iPriceAlarmStatus'));
    }

    public function testAddme_mailsent()
    {
        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $oEmail */
        $oEmail = $this->getMock('oxEmail', array('sendPricealarmNotification'));
        $oEmail->expects($this->once())->method('sendPricealarmNotification')->will($this->returnValue(123));
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        /** @var oxPriceAlarm|PHPUnit_Framework_MockObject_MockObject $oPriceAlarm */
        $oPriceAlarm = $this->getMock('oxPriceAlarm', array('save'));
        $oPriceAlarm->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxpricealarm', $oPriceAlarm);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxArticle', array('getId'));
        $oProduct->expects($this->once())->method('getId')->will($this->returnValue('test_artid'));

        /** @var Details|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock($this->getProxyClassName('Details'), array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $aParams = array();
        $aParams['email'] = 'test_email@eshop.com';
        $this->setRequestParameter('pa', $aParams);

        $oView->addme();
        $this->assertSame(123, $oView->getNonPublicVar('_iPriceAlarmStatus'));
    }

    public function testGetPriceAlarmStatus()
    {
        $oView = $this->getProxyClass('Details');
        $oView->setNonPublicVar('_iPriceAlarmStatus', 514);

        $this->assertSame(514, $oView->getPriceAlarmStatus());
    }

    public function testGetBidPrice()
    {
        $aParams = array();
        $aParams['price'] = '123.45';
        $this->setRequestParameter('pa', $aParams);

        $oView = $this->getProxyClass('Details');

        $this->assertSame('123,45', $oView->getBidPrice());
        $this->assertSame('123,45', $oView->getNonPublicVar('_sBidPrice'));
    }

    public function testRender_customArtTpl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template.tpl');

        $oView = $this->getMock($this->getProxyClassName('Details'), array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertSame('test_template.tpl', $oView->render());
        $this->assertSame('test_template.tpl', $oView->getNonPublicVar('_sThisTemplate'));
    }

    public function testRender_customParamTpl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template.tpl');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl.tpl');

        $oView = $this->getMock($this->getProxyClassName('Details'), array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $sExpected = 'custom/test_paramtpl.tpl';
        $this->assertSame($sExpected, $oView->render());
        $this->assertSame($sExpected, $oView->getNonPublicVar('_sThisTemplate'));
    }

    public function testRender_partial_productinfo()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template.tpl');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl.tpl');
        $this->setRequestParameter('renderPartial', 'productInfo');

        $oView = $this->getMock($this->getProxyClassName('Details'), array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertSame('page/details/ajax/fullproductinfo.tpl', $oView->render());
    }

    public function testRender_partial_detailsMain()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template.tpl');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl.tpl');
        $this->setRequestParameter('renderPartial', 'detailsMain');

        $oView = $this->getMock($this->getProxyClassName('Details'), array('getProduct'));
        $oView->expects($this->once())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertSame('page/details/ajax/productmain.tpl', $oView->render());
    }

    /**
     * Testing Rdfa
     *
     * @return null
     */
    public function testShowRdfa()
    {
        $this->setConfigParam('blRDFaEmbedding', true);
        $oDetails = oxNew('Details');
        $this->assertTrue($oDetails->showRdfa());
    }

    public function testGetRDFaNormalizedRatingNoRatings()
    {
        $this->setConfigParam('iRDFaMinRating', 1);
        $this->setConfigParam('iRDFaMaxRating', 5);
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');
        $oArt->oxarticles__oxratingcnt = new oxField(0);

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oArt));

        $this->assertFalse($oDetails->getRDFaNormalizedRating());
    }

    public function testGetRDFaNormalizedRating()
    {
        $this->setConfigParam('iRDFaMinRating', 1);
        $this->setConfigParam('iRDFaMaxRating', 5);
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');
        $oArt->oxarticles__oxratingcnt = new oxField('5');
        $oArt->oxarticles__oxrating = new oxField('10');

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oArt));

        $aNomalizedRating = $oDetails->getRDFaNormalizedRating();
        $this->assertEquals(5, $aNomalizedRating["count"]);
        $this->assertEquals(10, $aNomalizedRating["value"]);
    }

    public function testGetRDFaValidityPeriod()
    {
        $this->setConfigParam('iRDFaOfferingValidity', 30);
        $oDetails = oxNew('Details');

        $aValidity = $oDetails->getRDFaValidityPeriod('iRDFaOfferingValidity');
        $this->assertNotNull($aValidity["from"]);
        $this->assertNotNull($aValidity["through"]);
    }

    public function testGetRDFaValidityPeriodNotGiven()
    {
        $oDetails = oxNew('Details');
        $this->assertFalse($oDetails->getRDFaValidityPeriod(null));
    }

    public function testGetRDFaBusinessFnc()
    {
        $this->setConfigParam('sRDFaBusinessFnc', "B2B");
        $oDetails = oxNew('Details');
        $this->assertEquals('B2B', $oDetails->getRDFaBusinessFnc());
    }

    public function testGetRDFaCustomers()
    {
        $this->setConfigParam('aRDFaCustomers', "new");
        $oDetails = oxNew('Details');
        $this->assertEquals('new', $oDetails->getRDFaCustomers());
    }

    public function testGetRDFaVAT()
    {
        $this->setConfigParam('iRDFaVAT', "21");
        $oDetails = oxNew('Details');
        $this->assertEquals('21', $oDetails->getRDFaVAT());
    }

    public function testgetRDFaGenericCondition()
    {
        $this->setConfigParam('iRDFaCondition', true);
        $oDetails = oxNew('Details');
        $this->assertTrue($oDetails->getRDFaGenericCondition());
    }

    public function testGetRDFaPaymentMethods()
    {
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oArt));

        $this->assertTrue($oDetails->getRDFaPaymentMethods() instanceof paymentlist);
    }

    public function testGetRDFaDeliverySetMethods()
    {
        $oDetails = oxNew('Details');
        $this->assertTrue($oDetails->getRDFaDeliverySetMethods() instanceof deliverysetlist);
    }

    public function testGetProductsDeliveryList()
    {
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');

        $oDetails = $this->getMock('details', array('getProduct'));
        $oDetails->expects($this->any())->method('getProduct')->will($this->returnValue($oArt));

        $this->assertTrue($oDetails->getProductsDeliveryList() instanceof DeliveryList);
    }

    public function testGetRDFaDeliveryChargeSpecLoc()
    {
        $this->setConfigParam('sRDFaDeliveryChargeSpecLoc', "oxpayment");
        $oDetails = oxNew('Details');
        $this->assertEquals('oxpayment', $oDetails->getRDFaDeliveryChargeSpecLoc());
    }

    public function testGetRDFaPaymentChargeSpecLoc()
    {
        $this->setConfigParam('sRDFaPaymentChargeSpecLoc', 'oxpayment');
        $oDetails = oxNew('Details');
        $this->assertEquals('oxpayment', $oDetails->getRDFaPaymentChargeSpecLoc());
    }

    public function testGetRDFaBusinessEntityLoc()
    {
        $this->setConfigParam('sRDFaBusinessEntityLoc', 'oxagb');
        $oDetails = oxNew('Details');
        $this->assertEquals('oxagb', $oDetails->getRDFaBusinessEntityLoc());
    }

    public function testShowRDFaProductStock()
    {
        $this->setConfigParam('blShowRDFaProductStock', true);
        $oDetails = oxNew('Details');
        $this->assertTrue($oDetails->showRDFaProductStock());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     *
     * @return null
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue(''));
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(null, $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when default sorting is set
     *
     * @return null
     */
    public function testGetDefaultSortingDefinedSorting()
    {
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(array('sortby' => 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is undefined
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsUndefined()
    {
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(null));
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(array('sortby' => 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'asc'
     * This might be a little too much, but it's a case
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsAsc()
    {
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(false));

        $oController->setActiveCategory($oCategory);

        $this->assertEquals(array('sortby' => 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsDesc()
    {
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(true));

        $oController->setActiveCategory($oCategory);

        $this->assertEquals(array('sortby' => 'testsort', 'sortdir' => "desc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     *
     * @return null
     */
    public function testDefaultSorting_SortingDefinedCameFromSearch_doNotSort()
    {
        $this->setRequestParameter('listtype', 'search');
        $oController = oxNew('Details');

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(true));

        $oController->setActiveCategory($oCategory);

        $this->assertNull($oController->getDefaultSorting());
    }

    /**
     * testGetSortingParameters data provider
     *
     * @return array
     */
    public function getSortingDataProvider()
    {
        return array(
            array(array('alist', 'oxvarminprice', 'desc'), 'oxvarminprice|desc'),
            array(array('alist', null, null), "|"),
        );
    }

    /**
     * Test to check if sorting Parameters are formed correctly
     *
     * @dataProvider getSortingDataProvider
     */
    public function testGetSortingParameters($aParams, $sExpected)
    {
        $oController = oxNew('Details');
        list($sIdent, $sSortBy, $sSortOrder) = $aParams;
        $oController->setItemSorting($sIdent, $sSortBy, $sSortOrder);
        $this->assertEquals($sExpected, $oController->getSortingParameters());
    }

    /**
     * Test that method returns null when getSorting doesnt return an array
     */
    public function testGetSortingParameters_ExpectNull()
    {
        $oController = $this->getMock('Details', array('getSorting'));
        $oController->expects($this->any())->method('getSorting')->will($this->returnValue(null));

        $this->assertNull($oController->getSortingParameters());

    }
}


