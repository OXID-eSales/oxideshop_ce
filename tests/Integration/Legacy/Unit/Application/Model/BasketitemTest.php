<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxArticle;
use oxBasketItem;
use oxField;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use oxRegistry;
use oxTestModules;
use stdClass;

/**
 * Test oxArticle module - notBuyable
 */
class BasketItemTest_ArticleHelper extends oxArticle
{

    /**
     * Force isBuyable.
     *
     * @return bool
     */
    public function isBuyable()
    {
        return false;
    }
}

/**
 * Test oxArticle module - notVisible
 */
class modOxArticle_notVisible_oxbasketItem extends oxArticle
{

    /**
     * Force isVisible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return false;
    }
}

/**
 * Test oxBasketItem module
 */
class modForTestSetAsDiscountArticle extends oxBasketItem
{
    /**
     * Magic Geter for any protected field.
     *
     * @param string $sName Field name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        return $this->{'_' . $sName};
    }

    /**
     * Magic Seter for any protected field.
     *
     * @param string $sName  Field name
     * @param string $sValue Field value
     *
     * @return mixed
     */
    public function setVar($sName, $sValue)
    {
        $this->$sName = $sValue;
    }
}

class BasketitemTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        oxTestModules::addFunction('oxArticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
    }

    protected function tearDown(): void
    {
        $this->cleanUpTable('oxwrapping');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxartextends');

        parent::tearDown();
    }

    /**
     * Checking for stock control - stock in DB is positive.
     */
    public function testInitProductIsNotBuyable()
    {
        $sProdId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275' : '2077';

        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->init($sProdId, 1);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException) {
            return;
        }

        $this->fail("product should not be orderable");
    }

    /**
     * Checking for stock control - stock in DB is positive.
     */
    public function testSetAmountStockIsCritical()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);
        $oBasketItem = oxNew('oxbasketitem');
        $article = $this->createArticle();
        $article->oxarticles__oxstock = new oxField(1, oxField::T_RAW);
        $article->save();
        try {
            $oBasketItem->init($article->getId(), 1);
            $oBasketItem->setAmount(10);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException) {
            $this->assertEquals($article->oxarticles__oxstock->value, $oBasketItem->getAmount());
            $this->assertEquals($article->oxarticles__oxstock->value * $article->oxarticles__oxweight->value, $oBasketItem->getWeight());

            return;
        }

        $this->fail("failed stock related check");
    }

    /**
     * Test init from order article.
     */
    public function testInitFromOrderArticle()
    {
        $oOrderArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, ["getOrderArticleSelectList", "getPersParams", "isBundle"]);
        $oOrderArticle->expects($this->once())->method('getOrderArticleSelectList')->willReturn("aOrderArticleSelectList");
        $oOrderArticle->expects($this->once())->method('getPersParams')->willReturn("aPersParams");
        $oOrderArticle->expects($this->once())->method('isBundle')->willReturn(true);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(999);

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["setFromOrderArticle", "setAmount", "setSelectList", "setPersParams", "setBundle"]);
        $oBasketItem->expects($this->once())->method('setFromOrderArticle')->with($oOrderArticle);
        $oBasketItem->expects($this->once())->method('setAmount')->with(999);
        $oBasketItem->expects($this->once())->method('setSelectList')->with("aOrderArticleSelectList");
        $oBasketItem->expects($this->once())->method('setPersParams')->with("aPersParams");
        $oBasketItem->expects($this->once())->method('setBundle')->with(true);

        $oBasketItem->initFromOrderArticle($oOrderArticle);
    }

    /**
     * Test set from order article.
     */
    public function testSetFromOrderArticle()
    {
        $oOrderArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, ["getProductId"]);
        $oOrderArticle->expects($this->once())->method('getProductId')->willReturn("sProductId");
        $oOrderArticle->oxorderarticles__oxtitle = new oxField("oxarticles__oxtitle");
        $oOrderArticle->oxorderarticles__oxordershopid = new oxField("sNativeShopId");
        $oOrderArticle->setArticleParams();

        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->setFromOrderArticle($oOrderArticle);

        $this->assertEquals($oOrderArticle, $oBasketItem->getNonPublicVar("_oArticle"));
        $this->assertSame("sProductId", $oBasketItem->getNonPublicVar("_sProductId"));
        $this->assertSame("oxarticles__oxtitle", $oBasketItem->getNonPublicVar("_sTitle"));
        $this->assertEquals($this->getConfig()->getShopId(), $oBasketItem->getNonPublicVar("_sShopId"));
        $this->assertSame("sNativeShopId", $oBasketItem->getNonPublicVar("_sNativeShopId"));
    }

    /**
     * Stock status getter check.
     */
    public function testStockStatusGetterCheck()
    {
        $article = $this->createArticle();

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle', 'getStockCheckStatus']);
        $oBasketItem->expects($this->once())->method('getArticle')->willReturn($article);
        $oBasketItem->expects($this->once())->method('getStockCheckStatus')->willReturn(true);

        $oBasketItem->setAmount(100);
    }

    /**
     * Stock status setter check.
     */
    public function testStockStatusSetterCheck()
    {
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setStockCheckStatus(false);
        $this->assertFalse($oBasketItem->getStockCheckStatus());

        $oBasketItem->setStockCheckStatus(true);
        $this->assertTrue($oBasketItem->getStockCheckStatus());
    }

    /**
     * Testing init call
     */
    public function testInit()
    {
        $article = $this->createArticle();

        $oBasketItem = $this->getMock(
            'oxBasketItem',
            ['setArticle', 'setAmount', 'setSelectList', 'setPersParams', 'setBundle']
        );

        $oBasketItem->expects($this->once())->method('setArticle');
        $oBasketItem->expects($this->once())->method('setAmount');
        $oBasketItem->expects($this->once())->method('setSelectList');
        $oBasketItem->expects($this->once())->method('setPersParams');
        $oBasketItem->expects($this->once())->method('setBundle');

        $oBasketItem->init($article->getId(), 1);
    }

    /**
     * Testing discount marker
     */
    public function testSetAsDiscountArticle()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $this->assertFalse($oBasketItem->blIsDiscountArticle);
        $oBasketItem->setAsDiscountArticle(true);
        $this->assertTrue($oBasketItem->blIsDiscountArticle);
    }

    /**
     * Testing amount setter
     */
    public function testSetAmount()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        $oBasketItem->setAmount(10);

        $this->assertSame(10, $oBasketItem->getAmount());
        $this->assertSame(100, $oBasketItem->getWeight());

        // additionally testing if amounts are acumulated
        $oBasketItem->setAmount(10, false);
        $this->assertSame(20, $oBasketItem->getAmount());
        $this->assertSame(200, $oBasketItem->getWeight());

        // checking if amounts are overwritten
        try {
            $oBasketItem->setAmount(101);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException) {
            $this->assertSame(100, $oBasketItem->getAmount());
            $this->assertSame(1000, $oBasketItem->getWeight());
            $oBasketItem->setAmount(10);
            $this->assertSame(10, $oBasketItem->getAmount());
            $this->assertSame(100, $oBasketItem->getWeight());

            return;
        }

        $this->fail("failed stock related check");
    }

    /**
     * Testing amount setter with added bundle
     */
    public function testSetAmountIfBundleIsAdded()
    {
        $article = $this->createArticle();

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getArtStockInBasket']);
        $oBasket->method('getArtStockInBasket')->with($article->getId(), 'testItemKey')->willReturn(1);
        $session = oxNew('oxSession');
        $session->setBasket($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oBasketItem = oxNew(\OxidEsales\Eshop\Application\Model\BasketItem::class);

        $oBasketItem->setArticle($article->getId());
        $oBasketItem->setAmount(10, true, 'testItemKey');
        $this->assertSame(10, $oBasketItem->getAmount());

        // checking if amounts are overwritten
        try {
            $oBasketItem->setAmount(101, true, 'testItemKey');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException) {
            $this->assertSame(99, $oBasketItem->getAmount());

            return;
        }

        $this->fail("failed stock related check");
    }

    /**
     * Testing amount setter with bad input
     */
    public function testSetAmountBadInput()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        try {
            $oBasketItem->setAmount('jhvjh');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException $articleInputException) {
            if ($articleInputException->getArticleNr() == $article->getId()) {
                return;
            }
        }

        $this->fail('Error executing test: testSetAmountBadInput');
    }

    /**
     * Testing amount setter with checking for stock control - stock in DB is positive
     */
    public function testSetAmountStockIsMoreThanZero()
    {
        $article = $this->createArticle();

        $this->getConfig()->setConfigParam('blUseStock', true);
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        try {
            $oBasketItem->setAmount(9999999999999);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException) {
            $this->assertEquals($article->oxarticles__oxstock->value, $oBasketItem->getAmount());
            $this->assertEquals($article->oxarticles__oxstock->value * $article->oxarticles__oxweight->value, $oBasketItem->getWeight());

            return;
        }

        $this->fail("failed stock related check");
    }

    /**
     * Testing price setter
     */
    public function testSetPrice()
    {
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, []);

        $oBasketItem->setPrice(oxNew('oxprice'));
    }

    /**
     * Testing basketItemKey setter and getter
     */
    public function testSetGetBasketItemKey()
    {
        $basketItem = oxnew('oxBasketItem');
        $basketItem->setBasketItemKey('some_key');

        $this->assertSame('some_key', $basketItem->getBasketItemKey());
    }

    /**
     * Testing basket item article getter
     *
     * if no product id is set - exception must be thrown
     */
    public function testGetArticleNoArticleSet()
    {
        $oBasketItem = oxNew('oxbasketitem');
        try {
            $oBasketItem->getArticle();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleException) {
            return;
        }

        $this->fail('failed testing getArticle');
    }

    /**
     * Testing basket item article getter
     *
     * if article is set during init
     */
    public function testGetArticleArticleIsSetDuringInit()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);

        $oArticle = $oBasketItem->getArticle();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oArticle);
        //checking getter
        $oArticle2 = $oBasketItem->oProduct;
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oArticle2);
    }

    /**
     * Testing basket item article getter
     *
     * #M773 Do not use article lazy loading on order save
     */
    public function testGetArticleForSavingOrder()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);

        $oArticle = $oBasketItem->getArticle();

        $this->assertFalse($oArticle->isPropertyLoaded('oxarticles__oxpic12'));

        $oArticle = $oBasketItem->getArticle(true, null, true);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oArticle);

        $this->assertTrue($oArticle->isPropertyLoaded('oxarticles__oxpic12'));
    }

    /**
     * Testing if method throws an exeption such article does not exists
     */
    public function testGetArticle_noSuchArticle()
    {
        $oBasketItem = oxNew('oxBasketItem');

        try {
            $oBasketItem->getArticle(true, 'noSuchId');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleException) {
            return;
        }

        $this->fail('Execption was not thrown when article does not exists');
    }

    /**
     * Testing if method throws an exception if article is not buyable
     */
    public function testGetArticle_notBuyableArticle()
    {
        $article = $this->createArticle();

        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\BasketItemTest_ArticleHelper::class, 'oxArticle');

        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->getArticle(true, $article->getId());
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException) {
            return;
        }

        $this->fail('Exception was not thrown when article is not buyable');
    }

    /**
     * #1115: Usability Problem during checkout with products without stock
     */
    public function testGetArticle_notVisibleArticle_doNotCheck()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxBasketItem');
        $oBasketItem->getArticle(false, $article->getId());
    }

    /**
     * Testing if method throws an exception if article is not visible (M:1286)
     */
    public function testGetArticle_notVisibleArticle()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modOxArticle_notVisible_oxbasketItem::class, 'oxArticle');

        $article = $this->createArticle();
        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->getArticle(true, $article->getId());
        } catch (\OxidEsales\EshopCommunity\Core\Exception\NoArticleException) {
            return;
        }

        $this->fail('Exception was not thrown when article is not visible');
    }

    /**
     * Testing bundle amount getter
     *
     * article is not bundle - returns 0
     */
    public function testGetBundledAmountArticleIsNotBundled()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);

        $this->assertSame(0, $oBasketItem->getdBundledAmount());
    }

    /**
     * Testing bundle amount getter
     *
     * article is bundled - return 6
     */
    public function testGetdBundledAmountArticleIsBundled()
    {
        $article = $this->createArticle();
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $oBasketItem->setVar('_blBundle', true);

        $this->assertSame(6, $oBasketItem->getdBundledAmount());
    }

    /**
     * Testing price getter
     */
    public function testGetPrice()
    {
        $article = $this->createArticle();
        $oPrice = oxNew('oxPrice');

        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $oBasketItem->setVar('_oPrice', $oPrice);

        $this->assertEquals($oPrice, $oBasketItem->getPrice());
    }

    /**
     * Testing price getter
     */
    public function testGetUnitPrice()
    {
        $article = $this->createArticle();

        $dBruttoPricePrice = $article->getPrice()->getBruttoPrice();
        $dNettoPricePrice = $article->getPrice()->getNettoPrice();
        $dVAT = $article->getPrice()->getVAT();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6, null, null, true);
        $oBasketItem->setPrice($article->getPrice());

        $this->assertEquals($dBruttoPricePrice, $oBasketItem->getUnitPrice()->getBruttoPrice());
        $this->assertEquals($dNettoPricePrice, $oBasketItem->getUnitPrice()->getNettoPrice());
        $this->assertEquals($dVAT, $oBasketItem->getUnitPrice()->getVAT());

        $article->getPrice()->multiply(6);
        $this->assertEquals($article->getPrice(), $oBasketItem->getPrice());
    }

    /**
     * Testing amount getter
     */
    public function testGetAmount()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertSame(6, $oBasketItem->getAmount());
    }

    /**
     * Testing weight getter
     */
    public function testGetWeight()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertSame(60, $oBasketItem->getWeight());
    }

    /**
     * Testing title getter
     */
    public function testGetTitle()
    {
        $article = $this->createArticle();

        $article->oxarticles__oxvarselect = new oxField('xxx', oxField::T_RAW);
        $article->save();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);

        $sTitle = $article->oxarticles__oxtitle->value . ', ' . $article->oxarticles__oxvarselect->value;
        $this->assertSame($sTitle, $oBasketItem->getTitle());

        //language is changed
        $article->oxarticles__oxtitle = new oxField('title2', oxField::T_RAW);
        $article->oxarticles__oxvarselect = new oxField('var2', oxField::T_RAW);
        $article->save();

        $oBasketItem->setLanguageId(2);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle']);
        $oBasketItem->method('getArticle')->willReturn($article);

        $this->assertSame("title2, var2", $oBasketItem->getTitle());
    }

    /**
     * Testing icon url getter
     */
    public function testGetIconUrl()
    {
        $sIconUrl = $this->getConfig()->getConfigParam("sShopURL") . "out/pictures/generated/product/1/56_42_75/nopic.jpg";

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('testicon.jpg');

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle']);
        $oBasketItem->expects($this->once())->method('getArticle')->willReturn($oArticle);

        $this->assertSame($sIconUrl, $oBasketItem->getIconUrl());
    }

    /**
     * Testing icon url getter
     */
    public function testGetIconUrlAfterSslSwitch()
    {
        $sIconUrl = $this->getConfig()->getConfigParam("sShopURL") . "out/pictures/master/product/icon/nopic_ico.jpg";

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getIconUrl', 'getLink']);
        $oArticle->oxarticles__oxpic1 = new oxField('testicon.jpg');
        $oArticle->expects($this->once())->method('getIconUrl')->willReturn($sIconUrl);
        $oArticle->method('getLink');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isSsl', 'getShopId']);
        $oConfig->method('isSsl')->willReturn(false);
        $oConfig->method('getShopId')->willReturn(1);

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle', 'getConfig', "getTitle"]);
        $oBasketItem->method('getArticle')->willReturn($oArticle);
        $oBasketItem->expects($this->once())->method('getTitle');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        // initiating product
        $oBasketItem->setArticle("testId");
        $this->assertSame($sIconUrl, $oBasketItem->getIconUrl());
    }

    /**
     * Testing details link getter
     */
    public function testGetLink()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);

        $oArticle = oxNew('oxArticle');
        $oArticle->load($article->getId());
        $this->assertEquals($oArticle->getLink(), $oBasketItem->getLink());
    }

    /**
     * Returns original product shop id
     */
    public function testGetShopId()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals($this->getConfig()->getBaseShopId(), $oBasketItem->getShopId());
    }

    /**
     * Testing select list gerrer
     */
    public function testGetSelList()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertSame([0], $oBasketItem->getSelList());
    }

    /**
     * Testing select list gerrer
     */
    public function testGetChosenSelList()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);

        $oList = new stdClass();
        $oList->name = 'Test title';
        $oList->value = null;

        $this->assertEquals([$oList], $oBasketItem->getChosenSelList());
    }

    /**
     * Testing bundle status getter
     */
    public function testIsBundle()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6, null, null, true);
        $this->assertTrue($oBasketItem->isBundle());
    }

    /**
     * Testing discount article status getter
     */
    public function testIsDiscountArticle()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6, null, null, true);
        $oBasketItem->setAsDiscountArticle(true);

        $this->assertTrue($oBasketItem->isDiscountArticle());
    }

    /**
     * Testing skip discounts marker getter
     */
    public function testIsSkipDiscount()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6, null, null, true);
        $oBasketItem->setSkipDiscounts(true);

        $this->assertTrue($oBasketItem->isSkipDiscount());
    }

    /**
     * Testing article setter
     *
     * setting not existing article, expecting exception
     */
    public function testSetArticleSettingNotExisting()
    {
        $oBasketItem = oxNew('oxbasketitem');
        try {
            $oBasketItem->init('xxx', 6, null, null, true);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\NoArticleException) {
            return;
        }

        $this->fail('failed testing setArticle');
    }

    /**
     * Testing article setter
     */
    public function testSetArticleSettingExisting()
    {
        $article = $this->createArticle();
        $article->oxarticles__oxvarselect = new oxField('xxx', oxField::T_RAW);
        $article->save();

        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6, null, null, true);

        $this->assertEquals($article->getId(), $oBasketItem->sProductId);
        $this->assertSame($article->oxarticles__oxtitle->value . ", xxx", $oBasketItem->sTitle);
        $this->assertSame('xxx', $oBasketItem->sVarSelect);

        $expectedImageName = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275-01_ico.jpg' : '2077_p1_ico.jpg';
        $this->assertEquals($expectedImageName, $oBasketItem->sIcon);

        $this->assertEquals($article->getLink(), $oBasketItem->sLink);
        $this->assertEquals($this->getConfig()->getBaseShopId(), $oBasketItem->sShopId);
        $this->assertEquals($this->getConfig()->getBaseShopId(), $oBasketItem->sNativeShopId);
        $this->assertEquals($article->nossl_dimagedir, $oBasketItem->sDimageDirNoSsl);
        $this->assertEquals($article->ssl_dimagedir, $oBasketItem->sDimageDirSsl);
    }

    /**
     * Testing select lists setter
     */
    public function testSetSelectList()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $this->assertSame([0], $oBasketItem->getSelList());
    }

    /**
     * Testing select lists setter passing as param empty array
     */
    public function testSetSelectListWithEmptyArrayAsParam()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $this->assertSame([0], $oBasketItem->getSelList([]));
    }

    /**
     * Testing persistent params getters
     */
    public function testSetPersParams()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setPersParams(['something']);
        $this->assertSame(['something'], $oBasketItem->getPersParams());
    }

    /**
     * Testing bundle marker setter
     */
    public function testSetBundle()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setBundle(true);
        $this->assertTrue($oBasketItem->blBundle);
    }

    /**
     * Testing skip discounts marker setter
     */
    public function testSetSkipDiscounts()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setSkipDiscounts(true);
        $this->assertTrue($oBasketItem->blSkipDiscounts);
    }

    /**
     * Testing product id getter
     */
    public function testGetProductId()
    {
        $article = $this->createArticle();
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6, null, null, true);
        $this->assertEquals($article->getId(), $oBasketItem->sProductId);
    }

    /**
     * Testing wich article info setter/getter
     */
    public function testSetWishArticleIdAndgetWishArticleId()
    {
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setWishArticleId('xxx');
        $this->assertSame('xxx', $oBasketItem->getWishArticleId());
    }

    /**
     * Testing user wishinfo setter/getter
     */
    public function testSetWishIdAndgetWishId()
    {
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setWishId('xxx');
        $this->assertSame('xxx', $oBasketItem->getWishId());
    }

    /**
     * Testing wrap object getter
     */
    public function testGetWrappingAndSetWrappingAndGetWrappingId()
    {
        // creating wrap paper
        $wrapping = oxNew('oxWrapping');
        $wrapping->setId("_testwrap");

        $wrapping->oxwrapping__oxtype = new oxField("WRAP", oxField::T_RAW);
        $wrapping->oxwrapping__oxname = new oxField("Test card", oxField::T_RAW);
        $wrapping->oxwrapping__oxprice = new oxField(5, oxField::T_RAW);
        $wrapping->save();

        $sWrapId = $wrapping->getId();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setWrapping($sWrapId);

        // testing getter
        $this->assertEquals($sWrapId, $oBasketItem->getWrappingId());

        // testing object getter
        $oWrap = $oBasketItem->getWrapping();
        $this->assertEquals($sWrapId, $oWrap->getId());
    }

    /**
     * Testing unit price getter
     */
    public function testGetFUnitPrice()
    {
        $oPrice = $this->getMock(\OxidEsales\Eshop\Core\Price::class, ['getBruttoPrice']);
        $oPrice->expects($this->once())->method('getBruttoPrice')->willReturn(11.158);
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $this->assertSame("11,16", $oBasketItem->getFUnitPrice());
    }

    /**
     * Testing unit price getter
     */
    public function testGetFTotalPrice()
    {
        $oPrice = $this->getMock(\OxidEsales\Eshop\Core\Price::class, ['getBruttoPrice']);
        $oPrice->expects($this->once())->method('getBruttoPrice')->willReturn(11.158);
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $this->assertSame("11,16", $oBasketItem->getFTotalPrice());
    }

    /**
     * Testing set article and #M1141
     */
    public function testSetArticle()
    {
        $article = $this->createArticle();
        $article->oxarticles__oxtitle = new oxField('title', oxField::T_RAW);
        $article->oxarticles__oxvarselect = new oxField('var1', oxField::T_RAW);
        $article->save();
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle']);
        $oBasketItem->method('getArticle')->willReturn($article);
        $oBasketItem->setArticle($article->getId());

        $this->assertSame("title, var1", $oBasketItem->getTitle());
        $this->assertSame("var1", $oBasketItem->getVarSelect());
        $this->assertEquals($article->getId(), $oBasketItem->getProductId());
        $this->assertEquals($article->getLink(), $oBasketItem->getLink());
        $this->assertEquals($this->getConfig()->getShopId(), $oBasketItem->getShopId());
    }

    /**
     * @see https://bugs.oxid-esales.com/view.php?id=6053
     */
    public function testGetVarSelectKeepsZeroAsValue()
    {
        $article = $this->createArticle();
        $article->oxarticles__oxvarselect = new oxField('0', oxField::T_RAW);
        $article->save();
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle']);
        $oBasketItem->method('getArticle')->willReturn($article);
        $oBasketItem->setArticle($article->getId());

        $this->assertSame("0", $oBasketItem->getVarSelect());
    }

    /**
     * Test set languade id value.
     */
    public function testSetLanguageId()
    {
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['setArticle']);
        $oBasketItem->expects($this->never())->method('setArticle');

        $oBasketItem->setLanguageId('17');
        $this->assertSame('17', $oBasketItem->getLanguageId());
    }

    /**
     * Test change language id value.
     */
    public function testSetLanguageId_change()
    {
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['setArticle']);
        $oBasketItem->setLanguageId('17');
        $this->assertSame('17', $oBasketItem->getLanguageId());

        $oBasketItem->expects($this->once())->method('setArticle');
        $oBasketItem->setLanguageId('15');
        $this->assertSame('15', $oBasketItem->getLanguageId());
    }

    /**
     * Test change language id value and the article is not available anymore.
     * 5910: When out of stock articles exists in basket and language is changed, shop for that session goes offline
     */
    public function testSetLanguageId_change_noArticle()
    {
        $oBasketItem = $this->getMock('oxbasketitem', ['setArticle']);
        $oBasketItem->setLanguageId('15');

        $oEx = oxNew("oxNoArticleException");
        $oBasketItem->expects($this->once())->method('setArticle')->willThrowException($oEx);
        $oBasketItem->setLanguageId('17');
        $this->assertSame('17', $oBasketItem->getLanguageId());
        $aErrors = $this->getSession()->getVariable('Errors');

        $this->assertTrue(is_array($aErrors));
        $this->assertCount(1, $aErrors);

        $oExcp = unserialize(current($aErrors['default']));
        $this->assertNotNull($oExcp);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay::class, $oExcp);
    }

    /**
     * Test change language id value and the article is sold out.
     */
    public function testSetLanguageId_change_wrongArticleInput()
    {
        $oBasketItem = $this->getMock('oxbasketitem', ['setArticle']);
        $oBasketItem->setLanguageId('15');

        $oEx = oxNew("oxArticleInputException");
        $oBasketItem->expects($this->once())->method('setArticle')->willThrowException($oEx);
        $oBasketItem->setLanguageId('17');
        $this->assertSame('17', $oBasketItem->getLanguageId());
        $aErrors = $this->getSession()->getVariable('Errors');

        $this->assertTrue(is_array($aErrors));
        $this->assertCount(1, $aErrors);

        $oExcp = unserialize(current($aErrors['default']));
        $this->assertNotNull($oExcp);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay::class, $oExcp);
    }

    /**
     * Testing set article and #M1141
     */
    public function testGetVarSelect()
    {
        $article = $this->createArticle();

        $article->oxarticles__oxvarselect = new oxField('xxx', oxField::T_RAW);
        $article->save();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);

        $sTitle = $article->oxarticles__oxvarselect->value;
        $this->assertEquals($sTitle, $oBasketItem->GetVarSelect());

        //language is changed
        $article->oxarticles__oxvarselect = new oxField('var2', oxField::T_RAW);
        $article->save();

        $oBasketItem->setLanguageId(2);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getArticle']);
        $oBasketItem->method('getArticle')->willReturn($article);

        $this->assertSame("var2", $oBasketItem->GetVarSelect());
    }

    /**
     * Creates article object
     *
     * @return oxArticle
     */
    protected function createArticle()
    {
        $articleId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275-01' : '8a142c4100e0b2f57.59530204';

        $newArticleId = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId();

        $article = oxNew('oxArticle');
        $article->disableLazyLoading();
        $article->load($articleId);

        // making copy
        $article->setId($newArticleId);

        $article->oxarticles__oxweight = new oxField(10, oxField::T_RAW);
        $article->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $article->oxarticles__oxstock = new oxField(100, oxField::T_RAW);
        $article->oxarticles__oxparentid = new oxField(0, oxField::T_RAW);
        $article->save();

        // making select list
        $selectionList = oxNew('oxSelectList');
        $selectionList->oxselectlist__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $selectionList->oxselectlist__oxtitle = new oxField('Test title', oxField::T_RAW);
        $selectionList->oxselectlist__oxident = new oxField('Test ident', oxField::T_RAW);
        $selectionList->oxselectlist__oxvaldesc = new oxField('Test valdesc', oxField::T_RAW);
        $selectionList->save();

        // assigning select list
        $newGroup = oxNew("oxBase");
        $newGroup->init("oxobject2selectlist");

        $newGroup->oxobject2selectlist__oxobjectid = new oxField($article->getId(), oxField::T_RAW);
        $newGroup->oxobject2selectlist__oxselnid = new oxField($selectionList->getId(), oxField::T_RAW);
        $newGroup->oxobject2selectlist__oxsort = new oxField(0, oxField::T_RAW);
        $newGroup->save();

        return $article;
    }
}
