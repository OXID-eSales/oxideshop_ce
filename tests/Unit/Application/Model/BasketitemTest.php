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
namespace Unit\Application\Model;

use oxArticleException;
use oxArticleInputException;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxArticle;
use \oxBasketItem;
use \oxField;
use oxNoArticleException;
use oxOutOfStockException;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

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
        return $this->{"_$sName"};
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


/**
 * Testing oxBasketItem class.
 */
class BasketitemTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxTestModules::addFunction('oxArticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxwrapping');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxartextends');

        parent::tearDown();
    }

    /**
     * Checking for stock control - stock in DB is positive.
     *
     * @return null
     */
    public function testInitProductIsNotBuyable()
    {
        $sProdId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275' : '2077';

        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->init($sProdId, 1);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException $oException) {
            return;
        }
        $this->fail("product should not be orderable");
    }

    /**
     * Checking for stock control - stock in DB is positive.
     *
     * @return null
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
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException $oException) {
            $this->assertEquals($article->oxarticles__oxstock->value, $oBasketItem->getAmount());
            $this->assertEquals($article->oxarticles__oxstock->value * $article->oxarticles__oxweight->value, $oBasketItem->getWeight());

            return;
        }
        $this->fail("failed stock related check");
    }

    /**
     * Test init from order article.
     *
     * @return null
     */
    public function testInitFromOrderArticle()
    {
    $oOrderArticle = $this->getMock("oxorderarticle", array("getOrderArticleSelectList", "getPersParams", "isBundle"));
    $oOrderArticle->expects($this->once())->method('getOrderArticleSelectList')->will($this->returnValue("aOrderArticleSelectList"));
    $oOrderArticle->expects($this->once())->method('getPersParams')->will($this->returnValue("aPersParams"));
    $oOrderArticle->expects($this->once())->method('isBundle')->will($this->returnValue(true));
    $oOrderArticle->oxorderarticles__oxamount = new oxField(999);

    $oBasketItem = $this->getMock("oxbasketitem", array("_setFromOrderArticle", "setAmount", "_setSelectList", "setPersParams", "setBundle"));
    $oBasketItem->expects($this->once())->method('_setFromOrderArticle')->with($this->equalTo($oOrderArticle));
    $oBasketItem->expects($this->once())->method('setAmount')->with($this->equalTo(999));
    $oBasketItem->expects($this->once())->method('_setSelectList')->with($this->equalTo("aOrderArticleSelectList"));
    $oBasketItem->expects($this->once())->method('setPersParams')->with($this->equalTo("aPersParams"));
    $oBasketItem->expects($this->once())->method('setBundle')->with($this->equalTo(true));

    $oBasketItem->initFromOrderArticle($oOrderArticle);
    }

    /**
     * Test set from order article.
     *
     * @return null
     */
    public function testSetFromOrderArticle()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array("getProductId"));
        $oOrderArticle->expects($this->once())->method('getProductId')->will($this->returnValue("sProductId"));
        $oOrderArticle->oxorderarticles__oxtitle = new oxField("oxarticles__oxtitle");
        $oOrderArticle->oxorderarticles__oxordershopid = new oxField("sNativeShopId");
        $oOrderArticle->UNITsetArticleParams();

        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->UNITsetFromOrderArticle($oOrderArticle);

        $this->assertEquals($oOrderArticle, $oBasketItem->getNonPublicVar("_oArticle"));
        $this->assertEquals("sProductId", $oBasketItem->getNonPublicVar("_sProductId"));
        $this->assertEquals("oxarticles__oxtitle", $oBasketItem->getNonPublicVar("_sTitle"));
        $this->assertEquals($this->getConfig()->getShopId(), $oBasketItem->getNonPublicVar("_sShopId"));
        $this->assertEquals("sNativeShopId", $oBasketItem->getNonPublicVar("_sNativeShopId"));
    }

    /**
     * Stock status getter check.
     *
     * @return null
     */
    public function testStockStatusGetterCheck()
    {
        $article = $this->createArticle();

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle', 'getStockCheckStatus'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($article));
        $oBasketItem->expects($this->once())->method('getStockCheckStatus')->will($this->returnValue(true));

        $oBasketItem->setAmount(100);
    }

    /**
     * Stock status setter check.
     *
     * @return null
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
     *
     * @return null
     */
    public function testInit()
    {
        $article = $this->createArticle();

        $oBasketItem = $this->getMock(
            'oxBasketItem', array('_setArticle',
                                  'setAmount',
                                  '_setSelectList',
                                  'setPersParams',
                                  'setBundle')
        );

        $oBasketItem->expects($this->once())->method('_setArticle');
        $oBasketItem->expects($this->once())->method('setAmount');
        $oBasketItem->expects($this->once())->method('_setSelectList');
        $oBasketItem->expects($this->once())->method('setPersParams');
        $oBasketItem->expects($this->once())->method('setBundle');

        $oBasketItem->init($article->getId(), 1);
    }

    /**
     * Testing discount marker
     *
     * @return null
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
     *
     * @return null
     */
    public function testSetAmount()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        $oBasketItem->setAmount(10);

        $this->assertEquals(10, $oBasketItem->getAmount());
        $this->assertEquals(100, $oBasketItem->getWeight());

        // additionally testing if amounts are acumulated
        $oBasketItem->setAmount(10, false);
        $this->assertEquals(20, $oBasketItem->getAmount());
        $this->assertEquals(200, $oBasketItem->getWeight());

        // checking if amounts are overwritten
        try {
            $oBasketItem->setAmount(101);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException $oEx) {
            $this->assertEquals(100, $oBasketItem->getAmount());
            $this->assertEquals(1000, $oBasketItem->getWeight());
            $oBasketItem->setAmount(10);
            $this->assertEquals(10, $oBasketItem->getAmount());
            $this->assertEquals(100, $oBasketItem->getWeight());

            return;
        }
        $this->fail("failed stock related check");
    }

    /**
     * Testing amount setter with added bundle
     *
     * @return null
     */
    public function testSetAmountIfBundleIsAdded()
    {
        $article = $this->createArticle();

        $oBasket = $this->getMock('oxbasket', array('getArtStockInBasket'));
        $oBasket->expects($this->any())->method('getArtStockInBasket')->with($this->equalTo($article->getId()), $this->equalTo('testItemKey'))->will($this->returnValue(1));
        $oSession = oxNew('oxSession');
        $oSession->setBasket($oBasket);
        $oBasketItem = $this->getMock('oxbasketitem', array('getSession'));
        $oBasketItem->expects($this->any())->method('getSession')->will($this->returnValue($oSession));

        $oBasketItem->UNITsetArticle($article->getId());
        $oBasketItem->setAmount(10, true, 'testItemKey');
        $this->assertEquals(10, $oBasketItem->getAmount());

        // checking if amounts are overwritten
        try {
            $oBasketItem->setAmount(101, true, 'testItemKey');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException $oEx) {
            $this->assertEquals(99, $oBasketItem->getAmount());

            return;
        }
        $this->fail("failed stock related check");
    }

    /**
     * Testing amount setter with bad input
     *
     * @return null
     */
    public function testSetAmountBadInput()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        try {
            $oBasketItem->setAmount('jhvjh');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException $oEx) {
            if ($oEx->getArticleNr() == $article->getId()) {
                return;
            }
        }
        $this->fail('Error executing test: testSetAmountBadInput');
    }

    /**
     * Testing amount setter with checking for stock control - stock in DB is positive
     *
     * @return null
     */
    public function testSetAmountStockIsMoreThanZero()
    {
        $article = $this->createArticle();

        $this->getConfig()->setConfigParam('blUseStock', true);
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        try {
            $oBasketItem->setAmount(9999999999999);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\OutOfStockException $oException) {
            $this->assertEquals($article->oxarticles__oxstock->value, $oBasketItem->getAmount());
            $this->assertEquals($article->oxarticles__oxstock->value * $article->oxarticles__oxweight->value, $oBasketItem->getWeight());

            return;
        }
        $this->fail("failed stock related check");
    }

    /**
     * Testing price setter
     *
     * @return null
     */
    public function testSetPrice()
    {
        $oBasketItem = $this->getMock('oxBasketItem', array());

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
     *
     * @return null
     */
    public function testGetArticleNoArticleSet()
    {
        $oBasketItem = oxNew('oxbasketitem');
        try {
            $oBasketItem->getArticle();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleException $oExcp) {
            return;
        }
        $this->fail('failed testing getArticle');
    }

    /**
     * Testing basket item article getter
     *
     * if article is set during init
     *
     * @return null
     */
    public function testGetArticleArticleIsSetDuringInit()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        $oArticle = $oBasketItem->getArticle();
        $this->assertTrue($oArticle instanceof article);
        //checking getter
        $oArticle2 = $oBasketItem->oProduct;
        $this->assertTrue($oArticle2 instanceof article);
    }

    /**
     * Testing basket item article getter
     *
     * #M773 Do not use article lazy loading on order save
     *
     * @return null
     */
    public function testGetArticleForSavingOrder()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);
        $oArticle = $oBasketItem->getArticle();
        $this->assertFalse(isset($oArticle->oxarticles__oxpic12));
        $oArticle = $oBasketItem->getArticle(true, null, true);
        $this->assertTrue($oArticle instanceof article);

        $this->assertTrue(isset($oArticle->oxarticles__oxpic12));
    }

    /**
     * Testing if method throws an exeption such article does not exists
     *
     * @return null
     */
    public function testGetArticle_noSuchArticle()
    {
        $oBasketItem = oxNew('oxBasketItem');

        try {
            $oBasketItem->getArticle(true, 'noSuchId');
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleException $oEx) {
            return;
        }

        $this->fail('Execption was not thrown when article does not exists');
    }

    /**
     * Testing if method throws an exception if article is not buyable
     *
     * @return null
     */
    public function testGetArticle_notBuyableArticle()
    {
        $article = $this->createArticle();

        oxAddClassModule('Unit\Application\Model\BasketItemTest_ArticleHelper', 'oxArticle');

        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->getArticle(true, $article->getId());
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleInputException $oEx) {
            oxRemClassModule('Unit\Application\Model\BasketItemTest_ArticleHelper');
            return;
        }

        oxRemClassModule('Unit\Application\Model\BasketItemTest_ArticleHelper');
        $this->fail('Execption was not thrown when article is not buyable');
    }

    /**
     * #1115: Usability Problem during checkout with products without stock
     *
     * @return null
     */
    public function testGetArticle_notVisibleArticle_doNotCheck()
    {
        oxAddClassModule('modOxArticle_notVisible_oxbasketItem', 'oxArticle');

        $article = $this->createArticle();
        $oBasketItem = oxNew('oxBasketItem');
        $oBasketItem->getArticle(false, $article->getId());

        oxRemClassModule('modOxArticle_notVisible_oxbasketItem');
    }

    /**
     * Testing if method throws an exception if article is not visible (M:1286)
     *
     * @return null
     */
    public function testGetArticle_notVisibleArticle()
    {
        oxAddClassModule('Unit\Application\Model\modOxArticle_notVisible_oxbasketItem', 'oxArticle');

        $article = $this->createArticle();
        $oBasketItem = oxNew('oxBasketItem');
        try {
            $oBasketItem->getArticle(true, $article->getId());
        } catch (\OxidEsales\EshopCommunity\Core\Exception\NoArticleException $oEx) {
            oxRemClassModule('Unit\Application\Model\modOxArticle_notVisible_oxbasketItem');
            return;
        }

        oxRemClassModule('Unit\Application\Model\modOxArticle_notVisible_oxbasketItem');
        $this->fail('Execption was not thrown when article is not visible');
    }

    /**
     * Testing bundle amount getter
     *
     * article is not bundle - returns 0
     *
     * @return null
     */
    public function testGetBundledAmountArticleIsNotBundled()
    {
        $article = $this->createArticle();
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 1);

        $this->assertEquals(0, $oBasketItem->getdBundledAmount());
    }

    /**
     * Testing bundle amount getter
     *
     * article is bundled - return 6
     *
     * @return null
     */
    public function testGetdBundledAmountArticleIsBundled()
    {
        $article = $this->createArticle();
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $oBasketItem->setVar('_blBundle', true);

        $this->assertEquals(6, $oBasketItem->getdBundledAmount());
    }

    /**
     * Testing price getter
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetAmount()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals(6, $oBasketItem->getAmount());
    }

    /**
     * Testing weight getter
     *
     * @return null
     */
    public function testGetWeight()
    {
        $article = $this->createArticle();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals(60, $oBasketItem->getWeight());
    }

    /**
     * Testing title getter
     *
     * @return null
     */
    public function testGetTitle()
    {
        $article = $this->createArticle();

        $article->oxarticles__oxvarselect = new oxField('xxx', oxField::T_RAW);
        $article->save();

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $sTitle = $article->oxarticles__oxtitle->value . ', ' . $article->oxarticles__oxvarselect->value;
        $this->assertEquals($sTitle, $oBasketItem->getTitle());

        //language is changed
        $article->oxarticles__oxtitle = new oxField('title2', oxField::T_RAW);
        $article->oxarticles__oxvarselect = new oxField('var2', oxField::T_RAW);
        $article->save();

        $oBasketItem->setLanguageId(2);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($article));

        $this->assertEquals("title2, var2", $oBasketItem->getTitle());
    }

    /**
     * Testing icon url getter
     *
     * @return null
     */
    public function testGetIconUrl()
    {
        $sIconUrl = $this->getConfig()->getConfigParam("sShopURL") . "out/pictures/generated/product/1/87_87_75/nopic.jpg";

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField('testicon.jpg');

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));

        $this->assertEquals($sIconUrl, $oBasketItem->getIconUrl());
    }

    /**
     * Testing icon url getter
     *
     * @return null
     */
    public function testGetIconUrlAfterSslSwitch()
    {
        $sIconUrl = $this->getConfig()->getConfigParam("sShopURL") . "out/pictures/master/product/icon/nopic_ico.jpg";

        $oArticle = $this->getMock('oxarticle', array('getIconUrl', 'getLink'));
        $oArticle->oxarticles__oxpic1 = new oxField('testicon.jpg');
        $oArticle->expects($this->once())->method('getIconUrl')->will($this->returnValue($sIconUrl));
        $oArticle->expects($this->any())->method('getLink');

        $oConfig = $this->getMock('oxConfig', array('isSsl', 'getShopId'));
        $oConfig->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(1));

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle', 'getConfig', "getTitle"));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->once())->method('getTitle');
        $oBasketItem->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        // initiating product
        $oBasketItem->UNITsetArticle("testId");
        $this->assertEquals($sIconUrl, $oBasketItem->getIconUrl());
    }

    /**
     * Testing details link getter
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetSelList()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals(array(0), $oBasketItem->getSelList());
    }

    /**
     * Testing select list gerrer
     *
     * @return null
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

        $this->assertEquals(array($oList), $oBasketItem->getChosenSelList());
    }

    /**
     * Testing bundle status getter
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function testSetArticleSettingNotExisting()
    {
        $oBasketItem = oxNew('oxbasketitem');
        try {
            $oBasketItem->init('xxx', 6, null, null, true);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\NoArticleException $oExcp) {
            return;
        }

        $this->fail('failed testing setArticle');
    }

    /**
     * Testing article setter
     *
     * @return null
     */
    public function testSetArticleSettingExisting()
    {
        $article = $this->createArticle();
        $article->oxarticles__oxvarselect = new oxField('xxx', oxField::T_RAW);
        $article->save();

        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6, null, null, true);

        $this->assertEquals($article->getId(), $oBasketItem->sProductId);
        $this->assertEquals($article->oxarticles__oxtitle->value . ", xxx", $oBasketItem->sTitle);
        $this->assertEquals('xxx', $oBasketItem->sVarSelect);

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
     *
     * @return null
     */
    public function testSetSelectList()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals(array(0), $oBasketItem->getSelList());
    }

    /**
     * Testing select lists setter passing as param empty array
     *
     * @return null
     */
    public function testSetSelectListWithEmptyArrayAsParam()
    {
        $article = $this->createArticle();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->init($article->getId(), 6);
        $this->assertEquals(array(0), $oBasketItem->getSelList(array()));
    }

    /**
     * Testing persistent params getters
     *
     * @return null
     */
    public function testSetPersParams()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setPersParams(array('something'));
        $this->assertEquals(array('something'), $oBasketItem->getPersParams());
    }

    /**
     * Testing bundle marker setter
     *
     * @return null
     */
    public function testSetBundle()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setBundle(true);
        $this->assertTrue($oBasketItem->blBundle);
    }

    /**
     * Testing skip discounts marker setter
     *
     * @return null
     */
    public function testSetSkipDiscounts()
    {
        $oBasketItem = new modForTestSetAsDiscountArticle();
        $oBasketItem->setSkipDiscounts(true);
        $this->assertTrue($oBasketItem->blSkipDiscounts);
    }

    /**
     * Testing product id getter
     *
     * @return null
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
     *
     * @return null
     */
    public function testSetWishArticleIdAndgetWishArticleId()
    {
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setWishArticleId('xxx');
        $this->assertEquals('xxx', $oBasketItem->getWishArticleId());
    }

    /**
     * Testing user wishinfo setter/getter
     *
     * @return null
     */
    public function testSetWishIdAndgetWishId()
    {
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->setWishId('xxx');
        $this->assertEquals('xxx', $oBasketItem->getWishId());
    }

    /**
     * Testing wrap object getter
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetFUnitPrice()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.158));
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $this->assertEquals("11,16", $oBasketItem->getFUnitPrice());
    }

    /**
     * Testing unit price getter
     *
     * @return null
     */
    public function testGetFTotalPrice()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.158));
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $this->assertEquals("11,16", $oBasketItem->getFTotalPrice());
    }

    /**
     * Testing set article and #M1141
     *
     * @return null
     */
    public function testSetArticle()
    {
        $article = $this->createArticle();
        $article->oxarticles__oxtitle = new oxField('title', oxField::T_RAW);
        $article->oxarticles__oxvarselect = new oxField('var1', oxField::T_RAW);
        $article->save();
        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($article));
        $oBasketItem->UNITsetArticle($article->getId());

        $this->assertEquals("title, var1", $oBasketItem->getTitle());
        $this->assertEquals("var1", $oBasketItem->getVarSelect());
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
        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($article));
        $oBasketItem->UNITsetArticle($article->getId());

        $this->assertEquals("0", $oBasketItem->getVarSelect());
    }

    /**
     * Test set languade id value.
     *
     * @return null
     */
    public function testSetLanguageId()
    {
        $oBasketItem = $this->getMock('oxbasketitem', array('_setArticle'));
        $oBasketItem->expects($this->never())->method('_setArticle');

        $oBasketItem->setLanguageId('17');
        $this->assertEquals('17', $oBasketItem->getLanguageId());
    }

    /**
     * Test change language id value.
     *
     * @return null
     */
    public function testSetLanguageId_change()
    {
        $oBasketItem = $this->getMock('oxbasketitem', array('_setArticle'));
        $oBasketItem->setLanguageId('17');
        $this->assertEquals('17', $oBasketItem->getLanguageId());

        $oBasketItem->expects($this->once())->method('_setArticle');
        $oBasketItem->setLanguageId('15');
        $this->assertEquals('15', $oBasketItem->getLanguageId());
    }

    /**
     * Test change language id value and the article is not available anymore.
     * 5910: When out of stock articles exists in basket and language is changed, shop for that session goes offline
     *
     * @return null
     */
    public function testSetLanguageId_change_noArticle()
    {
        $oBasketItem = $this->getMock( 'oxbasketitem', array( '_setArticle' ) );
        $oBasketItem->setLanguageId( '15' );
        $oEx = oxNew( "oxNoArticleException" );
        $oBasketItem->expects( $this->once() )->method( '_setArticle')->will( $this->throwException( $oEx ) );
        $oBasketItem->setLanguageId( '17' );
        $this->assertEquals( '17', $oBasketItem->getLanguageId() );
        $aErrors = $this->getSession()->getVariable( 'Errors' );

        $this->assertTrue( is_array( $aErrors ) );
        $this->assertEquals( 1, count( $aErrors ) );

        $oExcp = unserialize( current( $aErrors['default'] ));
        $this->assertNotNull( $oExcp );
        $this->assertTrue( $oExcp instanceof ExceptionToDisplay );
    }

    /**
     * Test change language id value and the article is sold out.
     *
     * @return null
     */
    public function testSetLanguageId_change_wrongArticleInput()
    {
        $oBasketItem = $this->getMock( 'oxbasketitem', array( '_setArticle' ) );
        $oBasketItem->setLanguageId( '15' );
        $oEx = oxNew( "oxArticleInputException" );
        $oBasketItem->expects( $this->once() )->method( '_setArticle')->will( $this->throwException( $oEx ) );
        $oBasketItem->setLanguageId( '17' );
        $this->assertEquals( '17', $oBasketItem->getLanguageId() );
        $aErrors = $this->getSession()->getVariable( 'Errors' );

        $this->assertTrue( is_array( $aErrors ) );
        $this->assertEquals( 1, count( $aErrors ) );

        $oExcp = unserialize( current( $aErrors['default'] ));
        $this->assertNotNull( $oExcp );
        $this->assertTrue( $oExcp instanceof ExceptionToDisplay );
    }

    /**
     * Testing set article and #M1141
     *
     * @return null
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

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($article));

        $this->assertEquals("var2", $oBasketItem->GetVarSelect());
    }

    /**
     * Creates article object
     *
     * @return oxArticle
     */
    protected function createArticle()
    {
        $articleId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275-01' : '8a142c4100e0b2f57.59530204';

        $newArticleId = oxRegistry::get('oxUtilsObject')->generateUId();

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
