<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use OxidEsales\Facts\Facts;
use \stdClass;
use \oxField;
use \Exception;
use \oxDb;
use \oxTestModules;

/**
 * Tests for oxwArticleBox class
 */
class ArticleDetailsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test get active zoom picture.
     */
    public function testGetActZoomPic()
    {
        $oDetails = oxNew('oxwArticleDetails');
        $this->assertSame(1, $oDetails->getActZoomPic());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting']);
        $oCategory->method('getDefaultSorting')->willReturn('');
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(null, $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when default sorting is set
     */
    public function testGetDefaultSortingDefinedSorting()
    {
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oController->setActiveCategory($oCategory);

        $this->assertSame(['sortby' => 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is undefined
     */
    public function testDefaultSortingWhenSortingModeIsUndefined()
    {
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(null);
        $oController->setActiveCategory($oCategory);

        $this->assertSame(['sortby' => 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'asc'
     * This might be a little too much, but it's a case
     */
    public function testDefaultSortingWhenSortingModeIsAsc()
    {
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(false);

        $oController->setActiveCategory($oCategory);

        $this->assertSame(['sortby' => 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     */
    public function testDefaultSortingWhenSortingModeIsDesc()
    {
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(true);

        $oController->setActiveCategory($oCategory);

        $this->assertSame(['sortby' => 'testsort', 'sortdir' => "desc"], $oController->getDefaultSorting());
    }

    /**
     * Test get parent product.
     */
    public function testGetParentProduct()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["isBuyable"]);
        $oProduct->method('isBuyable')->willReturn(true);

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct"]);
        $oDetailsView->method('getProduct')->willReturn($oProduct);

        $oProduct = $oDetailsView->getParentProduct('1126');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oProduct);
        $this->assertSame('1126', $oProduct->getId());
    }

    /**
     * Test if ratings are activated.
     */
    public function testRatingIsActive()
    {
        $this->setConfigParam('bl_perfLoadReviews', true);
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $this->assertTrue($oDetails->ratingIsActive());
    }

    public function testCanRate()
    {
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $this->setConfigParam('bl_perfLoadReviews', true);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct', 'getUser']);
        $oDetails->method('getProduct')->willReturn($oArt);
        $oDetails->method('getUser')->willReturn($oUser);

        $this->assertTrue($oDetails->canRate());
    }

    /**
     * Test get attributes.
     */
    public function testGetAttributes()
    {
        $sArtID = '1672';
        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtID);

        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_oProduct", $oArticle);

        $sSelect = sprintf("select oxattrid from oxobject2attribute where oxobjectid = '%s'", $sArtID);
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = sprintf("select oxvalue from oxobject2attribute where oxattrid = '%s' and oxobjectid = '%s'", $sID, $sArtID);
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $aAttrList = $oDetails->getAttributes();
        $sAttribValue = $aAttrList[$sID]->value;
        $this->assertEquals($sExpectedValue, $sAttribValue);
    }

    /**
     * Test get link type.
     */
    public function testGetLinkType()
    {
        $this->setRequestParameter('listtype', 'vendor');
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertSame(OXARTICLE_LINKTYPE_VENDOR, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', 'manufacturer');
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertSame(OXARTICLE_LINKTYPE_MANUFACTURER, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getActiveCategory']);
        $oDetailsView->expects($this->once())->method('getActiveCategory')->willReturn(null);
        $this->assertSame(OXARTICLE_LINKTYPE_CATEGORY, $oDetailsView->getLinkType());

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->willReturn(true);

        $this->setRequestParameter('listtype', "recommlist");
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertSame(OXARTICLE_LINKTYPE_RECOMM, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getActiveCategory']);
        $oDetailsView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertSame(OXARTICLE_LINKTYPE_PRICECATEGORY, $oDetailsView->getLinkType());
    }

    /**
     * Test get variant list.
     */
    public function testGetVariantListExceptCurrent()
    {
        $oProd = oxNew('oxBase');
        $oProd->setId('asdasd');

        $oKeep1 = new stdClass();
        $oKeep1->asd = 'asd';

        $oKeep2 = new stdClass();
        $oKeep2->asdd = 'asasdd';

        $oList = oxNew('oxlist');
        $oList->assign(['asdasd' => $oKeep1, 'asd' => $oKeep2]);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getVariantList', 'getProduct']);
        $oDetails->expects($this->once())->method('getVariantList')->willReturn($oList);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oProd);

        $aRet = $oDetails->getVariantListExceptCurrent();

        $this->assertCount(1, $aRet);

        $oExpect = oxNew('oxlist');
        $oExpect->assign(['asd' => $oKeep2]);
        $this->assertEquals($oExpect->getArray(), $aRet->getArray());

        // do not reload nor clone articles
        $this->assertSame($oKeep2, $aRet['asd']);

        // original unchanged
        $this->assertCount(2, $oList);
    }

    /**
     * Test load variant information.
     */
    public function testLoadVariantInformation()
    {
        $this->setConfigParam('blVariantParentBuyable', true);

        // Get proxy creates class which is used in mock.
        $edition = 'Community';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $edition = 'Enterprise';
        }

        if ($this->getTestConfig()->getShopEdition() == 'PE') {
            $edition = 'Professional';
        }

        $proxy = $this->getProxyClass('\OxidEsales\Eshop' . $edition . '\Application\Model\Article');
        $articleProxyName = $proxy::class;

        $oProductParent = $this->getMock($articleProxyName, ['getSelectLists', 'getId']);
        $oProductParent->expects($this->once())->method('getSelectLists');
        $oProductParent->expects($this->atLeastOnce())->method('getId')->willReturn('123');
        $oProductParent->oxarticles__oxvarcount = new oxField(10);

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getParentArticle', 'getVariants', 'getId']);
        $oProduct->expects($this->never())->method('getVariants');
        $oProduct->expects($this->atLeastOnce())->method('getId')->willReturn('testArtId');
        $oProduct->oxarticles__oxvarcount = new oxField(10);

        $oVar1 = oxNew('oxArticle');
        $oVar1->setId('var1');

        $oVar2 = oxNew('oxArticle');
        $oVar2->setId('var2');

        $oVar3 = oxNew('oxArticle');
        $oVar3->setId('var3');

        $oVar4 = oxNew('oxArticle');
        $oVar4->setId('var4');

        $oVarList = oxNew('oxlist');
        $oVarList->offsetSet($oProduct->getId(), $oProduct);
        $oVarList->offsetSet($oVar1->getId(), $oVar1);
        $oVarList->offsetSet($oVar2->getId(), $oVar2);
        $oVarList->offsetSet($oVar3->getId(), $oVar3);
        $oVarList->offsetSet($oVar4->getId(), $oVar4);

        $oProductParent->setNonPublicVar('_aVariantsWithNotOrderables', ["full" => $oVarList]);
        $oProductParent->setNonPublicVar('_blNotBuyableParent', true);

        $oProduct->method('getParentArticle')->willReturn($oProductParent);

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct', 'getLinkType']);
        $oDetailsView->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $oDetailsView->expects($this->exactly(6))->method('getLinkType');
        $oDetailsView->loadVariantInformation();
    }

    /**
     * Test get variant list.
     */
    public function testGetVariantList()
    {
        $this->setRequestParameter('anid', '2077');
        if ((new Facts())->getEdition() === 'EE') {
            $this->setRequestParameter('anid', '2278');
        }

        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $this->assertSame(3, $oDetails->getVariantList()->count());
    }

    /**
     * Test get media files.
     */
    public function testGetMediaFiles()
    {
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc) values ('_test2', '2000', 'http://www.youtube.com/watch?v=ZN239G6aJZo', 'test2')";
        oxDb::getDb()->execute($sQ);

        $oArt = oxNew('oxArticle');
        $oArt->load('2000');

        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_oProduct", $oArt);

        $oMediaUrls = $oDetails->getMediaFiles();

        $this->assertCount(1, $oMediaUrls);
        $this->assertArrayHasKey('_test2', $oMediaUrls);
        $this->assertSame('test2', $oMediaUrls['_test2']->oxmediaurls__oxdesc->value);
    }

    /**
     * Test get last seen product list.
     */
    public function testGetLastProducts()
    {
        $this->setSessionParam('aHistoryArticles', ['1771']);

        $this->setRequestParameter('anid', '1771');
        $oDetails = oxNew('oxwArticleDetails');
        $oDetails->init();
        $oDetails->render();
        $oDetails->getLastProducts();

        $this->setRequestParameter('anid', '2000');
        $oDetails = oxNew('oxwArticleDetails');
        $oDetails->init();

        $this->assertSame('1771', $oDetails->getLastProducts()->current()->getId());
    }

    /**
     * Test get manufacturer.
     */
    public function testGetManufacturer()
    {
        $sManId = '68342e2955d7401e6.18967838';
        if ((new Facts())->getEdition() === 'EE') {
            $sManId = '88a996f859f94176da943f38ee067984';
        }

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getManufacturerId']);
        $oArticle->method('getManufacturerId')->willReturn(false);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oArticle);

        $oExpVendor = oxNew('oxVendor');
        $oExpVendor->load($sManId);

        $oVendor = $oDetails->getManufacturer();
        $this->assertEquals($oExpVendor->oxvendors__oxtitle->value, $oVendor->oxvendors__oxtitle->value);
    }

    /**
     * Test get vendor.
     */
    public function testGetVendor()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ((new Facts())->getEdition() === 'EE') {
            $sVendId = 'd2e44d9b31fcce448.08890330';
        }

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getVendorId']);
        $oArticle->method('getVendorId')->willReturn(false);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $oExpVendor = oxNew('oxVendor');
        $oExpVendor->load($sVendId);

        $oVendor = $oDetails->getVendor();
        $this->assertEquals($oExpVendor->oxvendors__oxtitle->value, $oVendor->oxvendors__oxtitle->value);
    }

    /**
     * Test get category.
     */
    public function testGetCategory()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');

        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_oProduct", $oArticle);

        $oCategory = $oDetails->getCategory();

        $sCatId = "8a142c3e49b5a80c1.23676990";
        if ((new Facts())->getEdition() === 'EE') {
            $sCatId = "30e44ab8593023055.23928895";
        }

        $this->assertNotNull($oCategory);
        $this->assertEquals($sCatId, $oCategory->getId());
    }

    /**
     * Test get picture gallery.
     */
    public function testGetPictureGallery()
    {
        $sArtID = "096a1b0849d5ffa4dd48cd388902420b";

        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArtID);

        $sActPic = $this->getConfig()->getPictureUrl(null) . "generated/product/1/250_200_75/" . basename($oArticle->oxarticles__oxpic1->value);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getPicturesProduct"]);
        $oDetails->expects($this->once())->method('getPicturesProduct')->willReturn($oArticle);
        $aPicGallery = $oDetails->getPictureGallery();

        $this->assertSame($sActPic, $aPicGallery['ActPic']);
    }

    /**
     * Test get active picture.
     */
    public function testGetActPicture()
    {
        $aPicGallery = ['ActPic' => 'aaa'];
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getActPicture());
    }

    /**
     * Test get more pictures.
     */
    public function testMorePics()
    {
        $aPicGallery = ['MorePics' => true];
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertTrue($oDetails->morePics());
    }

    /**
     * Test get icons.
     */
    public function testGetIcons()
    {
        $aPicGallery = ['Icons' => 'aaa'];
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getIcons());
    }

    /**
     * Test show zoom pictures.
     */
    public function testShowZoomPics()
    {
        $aPicGallery = ['ZoomPic' => true];
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertTrue($oDetails->showZoomPics());
    }

    /**
     * Test get zoom pictures.
     */
    public function testGetZoomPics()
    {
        $aPicGallery = ['ZoomPics' => 'aaa'];
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getZoomPics());
    }

    /**
     * Test get reviews.
     */
    public function testGetReviews()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getReviews']);
        $oArticle->method('getReviews')->willReturn("aaa");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame('aaa', $oDetails->getReviews());
    }

    /**
     * Test get crossselling.
     */
    public function testGetCrossSelling()
    {
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");

        $oDetails->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oDetails->getCrossSelling();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $oList);

        $iCount = 2;
        if ((new Facts())->getEdition() === 'EE') {
            $iCount = 3;
        }

        $this->assertEquals($iCount, $oList->count());
    }

    /**
     * Testing Account_Noticelist::getSimilarProducts()
     */
    public function testGetSimilarProductsEmptyProductList()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNoticeListController::class, ["getNoticeProductList"]);
        $oView->method('getNoticeProductList')->willReturn([]);
        $this->assertNull($oView->getSimilarProducts());
    }

    /**
     * Testing Account_Noticelist::getSimilarProducts()
     */
    public function testGetSimilarProducts()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["getSimilarProducts"]);
        $oProduct->method('getSimilarProducts')->willReturn("testSimilarProducts");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNoticeListController::class, ["getNoticeProductList"]);
        $oView->method('getNoticeProductList')->willReturn([$oProduct]);
        $this->assertSame("testSimilarProducts", $oView->getSimilarProducts());
    }

    /**
     * Test get ids for similar recomendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $articleId = "articleId";
        $aArrayKeys = [$articleId];
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getId"]);
        $oProduct->expects($this->once())->method("getId")->willReturn($articleId);

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct"]);
        $oDetails->expects($this->once())->method("getProduct")->willReturn($oProduct);
        $this->assertSame($aArrayKeys, $oDetails->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getProduct()");
    }

    /**
     * Test get accessories.
     */
    public function testGetAccessoires()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getAccessoires']);
        $oArticle->method('getAccessoires')->willReturn("aaa");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame("aaa", $oDetails->getAccessoires());
    }

    /**
     * Test get also bought these products.
     */
    public function testGetAlsoBoughtTheseProducts()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getCustomerAlsoBoughtThisProducts']);
        $oArticle->method('getCustomerAlsoBoughtThisProducts')->willReturn("aaa");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame("aaa", $oDetails->getAlsoBoughtTheseProducts());
    }

    /**
     * Test is product added to price alarm.
     */
    public function testIsPriceAlarm()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(1, oxField::T_RAW);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertEquals(false, $oView->isPriceAlarm());
    }

    /**
     * Test is product added to price alarm - true test.
     */
    public function testIsPriceAlarm_true()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(0, oxField::T_RAW);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertEquals(true, $oView->isPriceAlarm());
    }

    /**
     * Test search title setter/getter.
     */
    public function testSetGetSearchTitle()
    {
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setSearchTitle("tetsTitle");

        $this->assertSame("tetsTitle", $oDetails->getSearchTitle());
    }

    /**
     * Test category path setter/getter.
     */
    public function testSetGetCatTreePath()
    {
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $oDetails->setCatTreePath("tetsPath");

        $this->assertSame("tetsPath", $oDetails->getCatTreePath());
    }

    /**
     * Test is persistent parameter.
     */
    public function testIsPersParam()
    {
        $oArt = oxNew('oxArticle');
        $oArt->oxarticles__oxisconfigurable = new oxField(true);
        $oSubj = $this->getProxyClass("oxwArticleDetails");
        $oSubj->setNonPublicVar("_oProduct", $oArt);
        $oSubj->setNonPublicVar("_blIsInitialized", true);
        $this->assertTrue($oSubj->isPersParam());
    }

    /**
     * Test is persistent parameter navigative.
     */
    public function testIsPersParamNegative()
    {
        $oArt = oxNew('oxArticle');
        $oArt->oxarticles__oxisconfigurable = new oxField(false);
        $oSubj = $this->getProxyClass("oxwArticleDetails");
        $oSubj->setNonPublicVar("_oProduct", $oArt);
        $oSubj->setNonPublicVar("_blIsInitialized", true);
        $this->assertFalse($oSubj->isPersParam());
    }

    public function testGetRatingValue_active()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blShowVariantReviews')->willReturn(true);

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getArticleRatingAverage']);
        $oProduct->expects($this->once())->method('getArticleRatingAverage')->willReturn(123.855);

        $oView = $this->getMock($this->getProxyClassName('oxwArticleDetails'), ['getConfig', 'isReviewActive', 'getProduct']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->expects($this->once())->method('isReviewActive')->willReturn(true);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $this->assertEqualsWithDelta(123.9, $oView->getRatingValue(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(123.9, $oView->getNonPublicVar('_dRatingValue'), PHP_FLOAT_EPSILON);
    }

    public function testGetRatingValue_inactive()
    {
        $oView = $this->getMock($this->getProxyClassName('oxwArticleDetails'), ['getConfig', 'isReviewActive', 'getProduct']);
        $oView->expects($this->never())->method('getConfig');
        $oView->expects($this->once())->method('isReviewActive')->willReturn(false);
        $oView->expects($this->never())->method('getProduct');

        $this->assertEqualsWithDelta(0.0, $oView->getRatingValue(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(0.0, $oView->getNonPublicVar('_dRatingValue'), PHP_FLOAT_EPSILON);
    }

    public function testIsReviewActive()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('bl_perfLoadReviews')->willReturn('test_isactive');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame('test_isactive', $oView->isReviewActive());
    }

    public function testGetRatingCount_active()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blShowVariantReviews')->willReturn(true);

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getArticleRatingCount']);
        $oProduct->expects($this->once())->method('getArticleRatingCount')->willReturn(123);

        $oView = $this->getMock($this->getProxyClassName('oxwArticleDetails'), ['getConfig', 'isReviewActive', 'getProduct']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->expects($this->once())->method('isReviewActive')->willReturn(true);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $this->assertSame(123, $oView->getRatingCount());
        $this->assertSame(123, $oView->getNonPublicVar('_iRatingCnt'));
    }

    public function testGetRatingCount_inactive()
    {
        $oView = $this->getMock($this->getProxyClassName('oxwArticleDetails'), ['getConfig', 'isReviewActive', 'getProduct']);
        //$oView->expects( $this->never() )->method( 'getConfig' );
        $oView->expects($this->once())->method('isReviewActive')->willReturn(false);
        $oView->expects($this->never())->method('getProduct');

        $this->assertFalse($oView->getRatingCount());
        $this->assertFalse($oView->getNonPublicVar('_iRatingCnt'));
    }

    public function testGetPriceAlarmStatus()
    {
        $this->setRequestParameter('iPriceAlarmStatus', 514);
        $oDetails = oxNew('oxwArticleDetails');

        $this->assertSame(514, $oDetails->getPriceAlarmStatus());
    }

    public function testGetBidPrice()
    {
        $aParams = [];
        $aParams['price'] = '123.45';
        $this->setRequestParameter('pa', $aParams);

        $oView = $this->getProxyClass('oxwArticleDetails');

        $this->assertSame('123,45', $oView->getBidPrice());
        $this->assertSame('123,45', $oView->getNonPublicVar('_sBidPrice'));
    }

    /**
     * details::getVariantSelections() test case
     */
    public function testGetVariantSelections()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVariantSelections"]);
        $oProduct->expects($this->once())->method("getVariantSelections")->willReturn("varselections");
        //$oProduct->expects( $this->never() )->method( "getId" );

        // no parent
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct", "getParentProduct"]);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $oView->expects($this->once())->method('getParentProduct')->willReturn(false);

        $this->assertSame("varselections", $oView->getVariantSelections());

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVariantSelections"]);
        $oProduct->expects($this->never())->method('getVariantSelections')->willReturn("varselections");
        //$oProduct->expects( $this->once() )->method( 'getId');

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVariantSelections"]);
        $oParent->expects($this->once())->method('getVariantSelections')->willReturn("parentselections");

        // has parent
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct", "getParentProduct"]);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $oView->expects($this->once())->method('getParentProduct')->willReturn($oParent);

        $this->assertSame("parentselections", $oView->getVariantSelections());
    }

    /**
     * details::getPicturesProduct() test case
     */
    public function testGetPicturesProductNoVariantInfo()
    {
        $oProduct = $this->getMock("stdclass", ["getId"]);
        $oProduct->expects($this->never())->method('getId');

        // no picture product id
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct", 'getVariantSelections']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $oView->expects($this->once())->method('getVariantSelections')->willReturn(false);
        $this->assertSame($oProduct, $oView->getPicturesProduct());
    }

    public function testGetPicturesProductWithNoPerfectFitVariant()
    {
        $oProduct = $this->getMock("stdclass", ["getId"]);
        $oProduct->expects($this->never())->method('getId');

        $aInfo = ['oActiveVariant' => $oProduct, 'blPerfectFit'   => false];
        // no picture product id
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct", 'getVariantSelections']);
        $oView->expects($this->never())->method('getProduct');
        $oView->expects($this->once())->method('getVariantSelections')->willReturn($aInfo);
        $this->assertSame($oProduct, $oView->getPicturesProduct());
    }

    public function testGetPicturesProductWithPerfectFitVariant()
    {
        $oProduct = $this->getMock("stdclass", ["getId"]);
        $oProduct->expects($this->never())->method('getId');

        $aInfo = ['oActiveVariant' => $oProduct, 'blPerfectFit'   => true];
        // no picture product id
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ["getProduct", 'getVariantSelections']);
        $oView->expects($this->once())->method('getProduct')->willReturn('prod');
        $oView->expects($this->once())->method('getVariantSelections')->willReturn($aInfo);
        $this->assertSame('prod', $oView->getPicturesProduct());
    }

    /**
     * Test get invisible product.
     */
    public function testGetProductInvisibleProduct()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['isVisible']);
        $oProduct->expects($this->once())->method('isVisible')->willReturn(false);

        $this->setRequestParameter('anid', 'notexistingproductid');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( \$aA[0] ); }");

        try {
            $oDetailsView = $this->getProxyClass('oxwArticleDetails');
            $oDetailsView->setNonPublicVar('_oProduct', $oProduct);
            $oDetailsView->getProduct();
        } catch (Exception $exception) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $exception->getMessage(), 'result does not match');

            return;
        }

        $this->fail('product should not be returned');
    }

    /**
     * Test is multidimensionall variants enabled.
     */
    public function testIsMdVariantView()
    {
        $this->setConfigParam('blUseMultidimensionVariants', true);
        $oMdVariant = $this->getMock(\OxidEsales\Eshop\Application\Model\MdVariant::class, ['getMaxDepth']);
        $oMdVariant->method('getMaxDepth')->willReturn(2);
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getMdVariants']);
        $oProduct->method('getMdVariants')->willReturn($oMdVariant);
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oProduct);

        $this->assertTrue($oDetails->isMdVariantView());
    }

    /**
     * Test is multidimensionall variants disabled.
     */
    public function testIsMdVariantViewNotActive()
    {
        $this->setConfigParam('blUseMultidimensionVariants', false);
        $oDetails = $this->getProxyClass('oxwArticleDetails');
        $this->assertFalse($oDetails->isMdVariantView());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     */
    public function testDefaultSorting_SortingDefinedCameFromSearch_doNotSort()
    {
        $this->setRequestParameter('listtype', 'search');
        $oController = oxNew('oxwArticleDetails');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(true);

        $oController->setActiveCategory($oCategory);

        $this->assertNull($oController->getDefaultSorting());
    }
}
