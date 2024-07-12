<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Application\Controller\ArticleDetailsController;
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
class DetailsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getBaseSeoLink", "getBaseStdLink"]);
        $oProduct->expects($this->once())->method('getBaseSeoLink')->willReturn("testSeoUrl");
        $oProduct->expects($this->never())->method('getBaseStdLink')->willReturn("testStdUrl");

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct"]);
        $oDetailsView->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $this->assertSame("testSeoUrl", $oDetailsView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getBaseSeoLink", "getBaseStdLink"]);
        $oProduct->expects($this->never())->method('getBaseSeoLink')->willReturn("testSeoUrl");
        $oProduct->expects($this->once())->method('getBaseStdLink')->willReturn("testStdUrl");

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct"]);
        $oDetailsView->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $this->assertSame("testStdUrl", $oDetailsView->getCanonicalUrl());
    }

    /**
     * Test draw parent url when active product is a variant and only one is buyable.
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

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct"]);
        $oDetailsView->method('getProduct')->willReturn($oVariant);
        $this->assertTrue($oDetailsView->drawParentUrl());
    }

    /**
     * Returns variants and expected results
     *
     * @return array
     */
    public function variantProvider(): \Iterator
    {
        yield [null, null, []];
        yield [['abc'], null, ['varselid[0]' => 'abc']];
        yield [null, ['abc'], ['sel[0]' => 'abc']];
        yield [['abc', 'cbe', 'ghf'], null, ['varselid[0]' => 'abc', 'varselid[1]' => 'cbe', 'varselid[2]' => 'ghf']];
        yield [null, ['abc', 'cbe', 'ghf'], ['sel[0]' => 'abc', 'sel[1]' => 'cbe', 'sel[2]' => 'ghf']];
        yield [['abc', 'cbe', 'ghf'], ['efg', 'hjk', 'lmn'], ['varselid[0]' => 'abc', 'varselid[1]' => 'cbe', 'varselid[2]' => 'ghf', 'sel[0]' => 'efg', 'sel[1]' => 'hjk', 'sel[2]' => 'lmn']];
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
     */
    public function testgetAddDynUrlParams()
    {
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getListType", "getDynUrlParams"]);
        $oDetailsView->expects($this->once())->method('getListType')->willReturn("somelisttype");
        $oDetailsView->expects($this->never())->method('getDynUrlParams');
        $this->assertNull($oDetailsView->getAddDynUrlParams());

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getListType", "getDynUrlParams"]);
        $oDetailsView->expects($this->once())->method('getListType')->willReturn("search");
        $oDetailsView->expects($this->once())->method('getDynUrlParams')->willReturn("searchparams");
        $this->assertSame("searchparams", $oDetailsView->getAddDynUrlParams());
    }

    /**
     * Test process product urls.
     */
    public function testProcessProduct()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["setLinkType", "appendLink"]);
        $oProduct->expects($this->once())->method('setLinkType')->with("search");
        $oProduct->expects($this->once())->method('appendLink')->with("searchparams");

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getLinkType", "getAddDynUrlParams"]);
        $oDetailsView->expects($this->once())->method('getLinkType')->willReturn("search");
        $oDetailsView->expects($this->once())->method('getAddDynUrlParams')->willReturn("searchparams");

        $oDetailsView->processProduct($oProduct);
    }

    /**
     * Test get link type.
     */
    public function testGetLinkType()
    {
        $this->setRequestParameter('listtype', 'vendor');
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertEquals(OXARTICLE_LINKTYPE_VENDOR, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', 'manufacturer');
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory');
        $this->assertEquals(OXARTICLE_LINKTYPE_MANUFACTURER, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getActiveCategory']);
        $oDetailsView->expects($this->once())->method('getActiveCategory')->willReturn(null);
        $this->assertEquals(OXARTICLE_LINKTYPE_CATEGORY, $oDetailsView->getLinkType());

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->willReturn(true);

        $this->setRequestParameter('listtype', "recommlist");
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getActiveCategory']);
        $oDetailsView->expects($this->never())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertEquals(OXARTICLE_LINKTYPE_RECOMM, $oDetailsView->getLinkType());

        $this->setRequestParameter('listtype', null);
        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getActiveCategory']);
        $oDetailsView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertEquals(OXARTICLE_LINKTYPE_PRICECATEGORY, $oDetailsView->getLinkType());
    }

    /**
     * Test get parent product.
     */
    public function testGetParentProduct()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["isBuyable"]);
        $oProduct->method('isBuyable')->willReturn(true);

        $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct"]);
        $oDetailsView->method('getProduct')->willReturn($oProduct);

        $oProduct = $oDetailsView->getParentProduct('1126');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oProduct);
        $this->assertSame('1126', $oProduct->getId());
    }

    /**
     * Test get parent of non existing product.
     */
    public function testGetProductNotExistingProduct()
    {
        $_SERVER['REQUEST_URI'] = "index.php?cl=details&amp;anid=notexistingproductid";
        $this->setRequestParameter('anid', 'notexistingproductid');
        oxTestModules::addFunction("oxUtils", "handlePageNotFoundError", "{ throw new Exception( \$aA[0] ); }");

        try {
            $oDetailsView = oxNew('Details');
            $oDetailsView->getProduct();
        } catch (Exception $exception) {
            $this->assertSame($_SERVER['REQUEST_URI'], $exception->getMessage(), 'result does not match');

            return;
        }

        $this->fail('product should not be returned');
    }

    /**
     * Test case for #0002223: variant page works even if parent article is inactive
     */
    public function testForBugEntry0002223()
    {
        $sQ = "select oxid from oxarticles where oxparentid!='' and oxactive = 1";
        $this->setRequestParameter('anid', oxDb::getDb()->getOne($sQ));
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( \$aA[0] ); }");

        $oParentProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["isVisible"]);
        $oParentProduct->expects($this->once())->method('isVisible')->willReturn(false);

        try {
            $oDetailsView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getParentProduct"]);
            $oDetailsView->expects($this->once())->method('getParentProduct')->willReturn($oParentProduct);
            $oDetailsView->getProduct();
        } catch (Exception $exception) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $exception->getMessage(), 'result does not match');

            return;
        }

        $this->fail('product should not be returned');
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
            $oDetailsView = $this->getProxyClass('details');
            $oDetailsView->setNonPublicVar('_oProduct', $oProduct);
            $oDetailsView->getProduct();
        } catch (Exception $exception) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $exception->getMessage(), 'result does not match');

            return;
        }

        $this->fail('product should not be returned');
    }

    /**
     * Test noIndex property getter.
     */
    public function testNoIndex()
    {
        $this->setRequestParameter('listtype', 'vendor');

        $oDetailsView = oxNew('Details');
        $this->assertSame(2, $oDetailsView->noIndex());
    }

    /**
     * Test noIndex property getter.
     */
    public function testNoIndex_unknowntype()
    {
        $this->setRequestParameter('listtype', 'unknown');

        $oView = oxNew('Details');
        $this->assertSame(0, $oView->noIndex());
    }

    /**
     * Test get product.
     */
    public function testGetProduct()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $this->setRequestParameter('anid', '2000');
        $oDetails = $this->getProxyClass('details');
        $oDetails->init();
        $this->assertSame('2000', $oDetails->getProduct()->getId());
    }

    /**
     * Test get product.
     */
    public function testGetProductWithDirectVariant()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['load', 'getVariantSelections']);
        $oProduct->expects($this->once())->method('load')
            ->with('anid__')
            ->willReturn(1);
        $oProduct->expects($this->once())->method('getVariantSelections')
            ->with('varselid__')
            ->willReturn(['oActiveVariant' => 'actvar', 'blPerfectFit' => true]);
        oxTestModules::addModuleObject('oxarticle', $oProduct);

        $this->setRequestParameter('anid', 'anid__');
        $this->setRequestParameter('varselid', 'varselid__');

        $oDetailsView = $this->getProxyClass('details');
        $oDetailsView->setNonPublicVar('_blIsInitialized', 1);
        $this->assertSame('actvar', $oDetailsView->getProduct());
    }

    /**
     * Test get product.
     */
    public function testGetProductWithIndirectVariant()
    {
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['load', 'getVariantSelections']);
        $oProduct->expects($this->once())->method('load')
            ->with('anid__')
            ->willReturn(1);
        $oProduct->expects($this->once())->method('getVariantSelections')
            ->with('varselid__')
            ->willReturn(['oActiveVariant' => 'actvar', 'blPerfectFit' => false]);
        oxTestModules::addModuleObject('oxarticle', $oProduct);

        $this->setRequestParameter('anid', 'anid__');
        $this->setRequestParameter('varselid', 'varselid__');

        $oDetailsView = $this->getProxyClass('details');
        $oDetailsView->setNonPublicVar('_blIsInitialized', 1);
        $this->assertSame($oProduct, $oDetailsView->getProduct());
    }

    /**
     * Test draw parent url.
     */
    public function testDrawParentUrl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxparentid = new oxField('parent');

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oProduct);

        $this->assertTrue($oDetails->drawParentUrl());
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getPicturesProduct"]);
        $oDetails->expects($this->once())->method('getPicturesProduct')->willReturn($oArticle);
        $aPicGallery = $oDetails->getPictureGallery();

        $this->assertSame($sActPic, $aPicGallery['ActPic']);
    }

    /**
     * Test get active picture id.
     */
    public function testGetActPictureId()
    {
        $aPicGallery = ['ActPicID' => 'aaa'];
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getActPictureId());
    }


    /**
     * Test get active picture.
     */
    public function testGetActPicture()
    {
        $aPicGallery = ['ActPic' => 'aaa'];
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getActPicture());
    }

    /**
     * Test get pictures.
     */
    public function testGetPictures()
    {
        $aPicGallery = ['Pics' => 'aaa'];
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getPictures());
    }

    /**
     * Test show zoom pictures.
     */
    public function testShowZoomPics()
    {
        $aPicGallery = ['ZoomPic' => true];
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertTrue($oDetails->showZoomPics());
    }

    /**
     * Test get select lists.
     */
    public function testGetSelectLists()
    {
        $this->setConfigParam('bl_perfLoadSelectLists', true);
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getSelectLists']);
        $oArticle->method('getSelectLists')->willReturn("aaa");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame('aaa', $oDetails->getSelectLists());
    }

    /**
     * Test get reviews.
     */
    public function testGetReviews()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getReviews']);
        $oArticle->method('getReviews')->willReturn("aaa");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame('aaa', $oDetails->getReviews());
    }

    /**
     * Test get similar products.
     */
    public function testGetSimilarProducts()
    {
        $oDetails = $this->getProxyClass('Details');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");

        $oDetails->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oDetails->getSimilarProducts();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $oList);
        // Demo data is different in EE and CE
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 4 : 5;
        $this->assertCount($expectedCount, $oList);
    }

    /**
     * Test get crossselling.
     */
    public function testGetCrossSelling()
    {
        $oDetails = $this->getProxyClass('details');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");

        $oDetails->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oDetails->getCrossSelling();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\ArticleList::class, $oList);

        // Demo data is different in EE and CE
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 3 : 2;
        $this->assertEquals($expectedCount, $oList->count());
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct"]);
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertSame("aaa", $oDetails->getAlsoBoughtTheseProducts());
    }

    /**
     * Test is product added to price allarm.
     */
    public function testIsPriceAlarm()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(1, oxField::T_RAW);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oArticle);

        $this->assertEquals(false, $oView->isPriceAlarm());
    }

    /**
     * Test is product added to price allarm - true test.
     */
    public function testIsPriceAlarm_true()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxblfixedprice = new oxField(0, oxField::T_RAW);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oArticle);

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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct', 'getCategoryTree']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->method('getCategoryTree')->willReturn($oCategoryTree);

        $sKeywords = $oProduct->oxarticles__oxtitle->value;

        //adding breadcrumb
        $sKeywords .= ", Geschenke, Bar-Equipment";

        $oView = oxNew('oxUBase');
        $sTestKeywords = $oView->prepareMetaKeyword($sKeywords, true) . ", testvalue1, testvalue2, testvalue3";

        $this->assertSame($sTestKeywords, $oDetails->prepareMetaKeyword(null));
    }

    /**
     * Test meta meta description generation
     */
    public function testMetaDescriptionWithLongDesc()
    {
        $oProduct = oxNew("oxArticle");
        $oProduct->load("1849");

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $sMeta = $oProduct->oxarticles__oxtitle->value . ' - ' . $oProduct->getLongDescription();

        $oView = oxNew('oxUBase');
        $this->assertEquals($oView->prepareMetaDescription($sMeta, 200, false), $oDetails->prepareMetaDescription(null));
    }

    /**
     * Test search title setter/getter.
     */
    public function testSetGetSearchTitle()
    {
        $oDetails = $this->getProxyClass('details');
        $oDetails->setSearchTitle("tetsTitle");

        $this->assertSame("tetsTitle", $oDetails->getSearchTitle());
    }

    /**
     * Test category path setter/getter.
     */
    public function testSetGetCatTreePath()
    {
        $oDetails = $this->getProxyClass('details');
        $oDetails->setCatTreePath("tetsPath");

        $this->assertSame("tetsPath", $oDetails->getCatTreePath());
    }

    /**
     * Test article picture getter.
     */
    public function testGetArtPic()
    {
        $aPicGallery = ['Pics' => [1 => 'aaa']];
        $oDetails = $this->getProxyClass('details');
        $oDetails->setNonPublicVar("_aPicGallery", $aPicGallery);
        $this->assertSame('aaa', $oDetails->getArtPic(1));
    }

    /**
     * Test base view class title getter.
     */
    public function testGetTitle()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtitle = new oxField('product title');
        $oProduct->oxarticles__oxvarselect = new oxField('and varselect');

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $this->assertSame('product title and varselect', $oDetails->getTitle());
    }

    /**
     * Test base view class title getter - no product.
     */
    public function testGetTitle_noproduct()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn(null);
        $this->assertNull($oView->getTitle());
    }

    /**
     * Test cannonical URL getter - no product.
     */
    public function testGetCanonicalUrl_noproduct()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn(null);
        $this->assertNull($oView->getCanonicalUrl());
    }

    /**
     * Test review saving.
     */
    public function testSaveReview()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', 'oxdefaultadmin');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->method('addToRatingAverage');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oDetails */
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct', 'canAcceptFormData']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->method('canAcceptFormData')->willReturn(true);
        $oDetails->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving without user.
     */
    public function testSaveReviewIfUserNotSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', null);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->method('addToRatingAverage');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oDetails */
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving without rating.
     */
    public function testSaveReviewIfOnlyReviewIsSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', null);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oDetails */
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct', 'getUser', 'canAcceptFormData']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->method('getUser')->willReturn($oUser);
        $oDetails->method('canAcceptFormData')->willReturn(true);
        $oDetails->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select 1 from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test review saving with wrong rating.
     */
    public function testSaveReviewIfWrongRating()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', 6);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oDetails */
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct', 'getUser', 'canAcceptFormData']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->method('getUser')->willReturn($oUser);
        $oDetails->method('canAcceptFormData')->willReturn(true);
        $oDetails->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test only review rating saving.
     */
    public function testSaveReviewIfOnlyRatingIsSet()
    {
        $this->setRequestParameter('rvw_txt', null);
        $this->setRequestParameter('artrating', 3);
        $this->setRequestParameter('anid', 'test');
        $this->setSessionParam('usr', 'oxdefaultadmin');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->method('addToRatingAverage');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oDetails */
        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct', 'canAcceptFormData']);
        $oDetails->method('getProduct')->willReturn($oProduct);
        $oDetails->method('canAcceptFormData')->willReturn(true);
        $oDetails->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testAddToRecommIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->willReturn(false);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getViewConfig", 'getArticleList']);
        $oRecomm->expects($this->once())->method('getViewConfig')->willReturn($oCfg);
        $oRecomm->expects($this->never())->method('getArticleList');

        $this->setRequestParameter('anid', 'asd');
        oxTestModules::addFunction('oxrecommlist', 'load', '{throw new Exception("should not come here");}');

        $this->assertNull($oRecomm->addToRecomm());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testAddToRecommIfOn()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->willReturn(true);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId']);
        $oProduct->expects($this->once())->method('getId')->willReturn('test_artid');

        $this->setRequestParameter('recomm', 'test_recomm');
        $this->setRequestParameter('recomm_txt', 'test_recommtext');

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ['load', 'addArticle']);
        $oRecommList->expects($this->once())->method('load')->with('test_recomm');
        $oRecommList->expects($this->once())->method('addArticle')->with('test_artid', 'test_recommtext');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getViewConfig", 'getProduct']);
        $oRecomm->expects($this->once())->method('getViewConfig')->willReturn($oCfg);
        $oRecomm->expects($this->once())->method('getProduct')->willReturn($oProduct);

        oxTestModules::addModuleObject('oxrecommlist', $oRecommList);

        $this->assertNull($oRecomm->addToRecomm());
    }

    /**
     * Testing Details::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $details = oxNew('Details');

        $this->setRequestParameter('listtype', 'search');
        $this->assertGreaterThanOrEqual(1, count($details->getBreadCrumb()));

        $details = oxNew('details');
        $this->setRequestParameter('listtype', 'recommlist');
        $this->assertGreaterThanOrEqual(1, count($details->getBreadCrumb()));

        $details = oxNew('details');
        $this->setRequestParameter('listtype', 'vendor');
        $this->assertGreaterThanOrEqual(1, count($details->getBreadCrumb()));

        $this->setRequestParameter('listtype', 'aaa');

        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat1->expects($this->once())->method('getLink')->willReturn('linkas1');
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat2->expects($this->once())->method('getLink')->willReturn('linkas2');
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getCatTreePath"]);
        $oView->expects($this->once())->method('getCatTreePath')->willReturn([$oCat1, $oCat2]);

        $this->assertGreaterThanOrEqual(1, count($oView->getBreadCrumb()));
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct", "getParentProduct"]);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);
        $oView->expects($this->once())->method('getParentProduct')->willReturn(false);

        $this->assertSame("varselections", $oView->getVariantSelections());

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVariantSelections"]);
        $oProduct->expects($this->never())->method('getVariantSelections')->willReturn("varselections");
        //$oProduct->expects( $this->once() )->method( 'getId');

        $oParent = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getVariantSelections"]);
        $oParent->expects($this->once())->method('getVariantSelections')->willReturn("parentselections");

        // has parent
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct", "getParentProduct"]);
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct", 'getVariantSelections']);
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct", 'getVariantSelections']);
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ["getProduct", 'getVariantSelections']);
        $oView->expects($this->once())->method('getProduct')->willReturn('prod');
        $oView->expects($this->once())->method('getVariantSelections')->willReturn($aInfo);
        $this->assertSame('prod', $oView->getPicturesProduct());
    }

    public function testGetSearchParamForHtml()
    {
        $oDetails = $this->getProxyClass('details');
        $this->setRequestParameter('searchparam', 'aaa');

        $this->assertSame('aaa', $oDetails->getSearchParamForHtml());
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
        $aFilters = ['test_cnid' => [0 => 'test_filters']];
        $this->setSessionParam('session_attrfilter', $aFilters);

        $sExpected = $sBaseViewId . '|test_anid|';

        $sResp = $oView->getViewId();
        $this->assertSame($sExpected, $sResp);
        $this->assertSame($sExpected, $oView->getNonPublicVar('_sViewId'));
    }

    public function testIsReviewActive()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('bl_perfLoadReviews')->willReturn('test_isactive');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame('test_isactive', $oView->isReviewActive());
    }

    public function testAddme_invalidEmail()
    {
        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['sendPricealarmNotification']);
        $oEmail->expects($this->never())->method('sendPricealarmNotification');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        /** @var oxPriceAlarm|PHPUnit\Framework\MockObject\MockObject $oPriceAlarm */
        $oPriceAlarm = $this->getMock(\OxidEsales\Eshop\Application\Model\PriceAlarm::class, ['save']);
        $oPriceAlarm->expects($this->never())->method('save');
        oxTestModules::addModuleObject('oxpricealarm', $oPriceAlarm);

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getProxyClass('Details');

        $aParams = [];
        $aParams['email'] = 'test_email';

        $this->setRequestParameter('pa', $aParams);
        $oView->addme();
        $this->assertSame(0, $oView->getNonPublicVar('_iPriceAlarmStatus'));
    }

    public function testAddme_mailsent()
    {
        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['sendPricealarmNotification']);
        $oEmail->expects($this->once())->method('sendPricealarmNotification')->willReturn(123);
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        /** @var oxPriceAlarm|PHPUnit\Framework\MockObject\MockObject $oPriceAlarm */
        $oPriceAlarm = $this->getMock(\OxidEsales\Eshop\Application\Model\PriceAlarm::class, ['save']);
        $oPriceAlarm->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxpricealarm', $oPriceAlarm);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId']);
        $oProduct->expects($this->once())->method('getId')->willReturn('test_artid');

        /** @var Details|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock($this->getProxyClassName('Details'), ['getProduct']);
        $oView->expects($this->once())->method('getProduct')->willReturn($oProduct);

        $aParams = [];
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
        $aParams = [];
        $aParams['price'] = '123.45';
        $this->setRequestParameter('pa', $aParams);

        $oView = $this->getProxyClass('Details');

        $this->assertSame('123,45', $oView->getBidPrice());
        $this->assertSame('123,45', $oView->getNonPublicVar('_sBidPrice'));
    }

    public function testRender_customArtTpl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template');

        $oView = $this->getMock(ArticleDetailsController::class, ['getProduct']);
        $oView->method('getProduct')->willReturn($oProduct);

        $this->assertSame('test_template', $oView->render());
    }

    public function testRender_customParamTpl()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl');

        $oView = $this->getMock(ArticleDetailsController::class, ['getProduct']);
        $oView->method('getProduct')->willReturn($oProduct);

        $sExpected = 'custom/test_paramtpl';
        $this->assertSame($sExpected, $oView->render());
    }

    public function testRender_partial_productinfo()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl');
        $this->setRequestParameter('renderPartial', 'productInfo');

        $oView = $this->getMock(ArticleDetailsController::class, ['getProduct']);
        $oView->method('getProduct')->willReturn($oProduct);

        $this->assertSame('page/details/ajax/fullproductinfo', $oView->render());
    }

    public function testRender_partial_detailsMain()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtemplate = new oxField('test_template');
        $this->setRequestParameter('tpl', '../some/path/test_paramtpl');
        $this->setRequestParameter('renderPartial', 'detailsMain');

        $oView = $this->getMock(ArticleDetailsController::class, ['getProduct']);
        $oView->method('getProduct')->willReturn($oProduct);

        $this->assertSame('page/details/ajax/productmain', $oView->render());
    }

    /**
     * Testing Rdfa
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oArt);

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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oArt);

        $aNomalizedRating = $oDetails->getRDFaNormalizedRating();
        $this->assertSame(5, $aNomalizedRating["count"]);
        $this->assertSame(10, $aNomalizedRating["value"]);
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
        $this->assertSame('B2B', $oDetails->getRDFaBusinessFnc());
    }

    public function testGetRDFaCustomers()
    {
        $this->setConfigParam('aRDFaCustomers', "new");
        $oDetails = oxNew('Details');
        $this->assertSame('new', $oDetails->getRDFaCustomers());
    }

    public function testGetRDFaVAT()
    {
        $this->setConfigParam('iRDFaVAT', "21");
        $oDetails = oxNew('Details');
        $this->assertSame('21', $oDetails->getRDFaVAT());
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

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oArt);

        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\PaymentList::class, $oDetails->getRDFaPaymentMethods());
    }

    public function testGetRDFaDeliverySetMethods()
    {
        $oDetails = oxNew('Details');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliverySetList::class, $oDetails->getRDFaDeliverySetMethods());
    }

    public function testGetProductsDeliveryList()
    {
        $oArt = oxNew('oxArticle');
        $oArt->load('2000');

        $oDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getProduct']);
        $oDetails->method('getProduct')->willReturn($oArt);

        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliveryList::class, $oDetails->getProductsDeliveryList());
    }

    public function testGetRDFaDeliveryChargeSpecLoc()
    {
        $this->setConfigParam('sRDFaDeliveryChargeSpecLoc', "oxpayment");
        $oDetails = oxNew('Details');
        $this->assertSame('oxpayment', $oDetails->getRDFaDeliveryChargeSpecLoc());
    }

    public function testGetRDFaPaymentChargeSpecLoc()
    {
        $this->setConfigParam('sRDFaPaymentChargeSpecLoc', 'oxpayment');
        $oDetails = oxNew('Details');
        $this->assertSame('oxpayment', $oDetails->getRDFaPaymentChargeSpecLoc());
    }

    public function testGetRDFaBusinessEntityLoc()
    {
        $this->setConfigParam('sRDFaBusinessEntityLoc', 'oxagb');
        $oDetails = oxNew('Details');
        $this->assertSame('oxagb', $oDetails->getRDFaBusinessEntityLoc());
    }

    public function testShowRDFaProductStock()
    {
        $this->setConfigParam('blShowRDFaProductStock', true);
        $oDetails = oxNew('Details');
        $this->assertTrue($oDetails->showRDFaProductStock());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = oxNew('Details');

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
        $oController = oxNew('Details');

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
        $oController = oxNew('Details');

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
        $oController = oxNew('Details');

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
        $oController = oxNew('Details');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(true);

        $oController->setActiveCategory($oCategory);

        $this->assertSame(['sortby' => 'testsort', 'sortdir' => "desc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     */
    public function testDefaultSorting_SortingDefinedCameFromSearch_doNotSort()
    {
        $this->setRequestParameter('listtype', 'search');
        $oController = oxNew('Details');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(true);

        $oController->setActiveCategory($oCategory);

        $this->assertNull($oController->getDefaultSorting());
    }

    /**
     * testGetSortingParameters data provider
     *
     * @return array
     */
    public function getSortingDataProvider(): \Iterator
    {
        yield [['alist', 'oxvarminprice', 'desc'], 'oxvarminprice|desc'];
        yield [['alist', null, null], "|"];
    }

    /**
     * Test to check if sorting Parameters are formed correctly
     *
     * @dataProvider getSortingDataProvider
     */
    public function testGetSortingParameters($aParams, $sExpected)
    {
        $oController = oxNew('Details');
        [$sIdent, $sSortBy, $sSortOrder] = $aParams;
        $oController->setItemSorting($sIdent, $sSortBy, $sSortOrder);
        $this->assertEquals($sExpected, $oController->getSortingParameters());
    }

    /**
     * Test that method returns null when getSorting doesnt return an array
     */
    public function testGetSortingParameters_ExpectNull()
    {
        $oController = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class, ['getSorting']);
        $oController->method('getSorting')->willReturn(null);

        $this->assertNull($oController->getSortingParameters());
    }
}
