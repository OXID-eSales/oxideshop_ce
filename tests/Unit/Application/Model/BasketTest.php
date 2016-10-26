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

use oxArticleInputException;
use oxNoArticleException;
use oxOutOfStockException;
use \OxidEsales\EshopCommunity\Core\PriceList;
use \OxidEsales\EshopCommunity\Application\Model\Wrapping;
use oxArticleHelper;
use \oxbasket;
use \oxField;
use \oxPrice;
use \OxidEsales\EshopCommunity\Core\Price;
use oxUtilsObject;
use oxVoucherHelper;
use \stdClass;
use \oxbasketitem;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxVoucherHelper.php';

class modForTestAddBundles extends oxBasket
{

    public function setBasket($aBasket)
    {
        $this->_aBasketContents = $aBasket;
    }

    public function getVar($sVarName)
    {
        return $this->{'_' . $sVarName};
    }

    public function setVar($sName, $sValue)
    {
        $this->{'_' . $sName} = $sValue;
    }
}

class BasketTest extends \OxidTestCase
{

    public $oArticle = null;
    public $oCategory = null;
    public $oSelList = null;
    public $aDiscounts = array();
    public $blPerfLoadSelectLists;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute('delete from oxuserbaskets');
        oxDb::getDb()->execute('delete from oxuserbasketitems');

        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);

        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275' : '2077';

        $sNewId = oxUtilsObject::getInstance()->generateUId();

        oxTestModules::addFunction('oxarticle', 'getLink( $iLang = null, $blMain = false  )', '{return "htpp://link_for_article/".$this->getId();}');

        $this->oArticle = oxNew('oxArticle');
        //$this->oArticle->disableLazyLoading();
        $this->oArticle->Load($sId);

        // making copy
        $this->oArticle->setId($sNewId);
        $this->oArticle->oxarticles__oxweight = new oxField(10, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstock = new oxField(100, oxField::T_RAW);
        $this->oArticle->oxarticles__oxprice = new oxField(19, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();

        // making category
        $sCatId = oxUtilsObject::getInstance()->generateUId();
        $this->oCategory = oxNew('oxCategory');
        $this->oCategory->setId($sCatId);
        $this->oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $this->oCategory->oxcategories__oxrootid = new oxField($sCatId, oxField::T_RAW);
        $this->oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $this->oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);;
        $this->oCategory->oxcategories__oxtitle = new oxField('Test category 1', oxField::T_RAW);
        $this->oCategory->save();

        // assigning article to category
        $oArt2Cat = oxNew("oxobject2category");
        $oArt2Cat->oxobject2category__oxobjectid = new oxField($sNewId, oxField::T_RAW);
        $oArt2Cat->oxobject2category__oxcatnid = new oxField($sCatId, oxField::T_RAW);
        $oArt2Cat->save();

        // making select list
        $this->oSelList = oxNew('oxselectlist');
        $this->oSelList->oxselectlist__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->oSelList->oxselectlist__oxtitle = new oxField('Test title', oxField::T_RAW);
        $this->oSelList->oxselectlist__oxident = new oxField('Test ident', oxField::T_RAW);
        $this->oSelList->oxselectlist__oxvaldesc = new oxField('Test valdesc__@@', oxField::T_RAW);
        $this->oSelList->save();

        // assigning select list
        $oNewGroup = oxNew("oxBase");
        $oNewGroup->init("oxobject2selectlist");
        $oNewGroup->oxobject2selectlist__oxobjectid = new oxField($this->oArticle->getId(), oxField::T_RAW);
        $oNewGroup->oxobject2selectlist__oxselnid = new oxField($this->oSelList->getId(), oxField::T_RAW);
        $oNewGroup->oxobject2selectlist__oxsort = new oxField(0, oxField::T_RAW);
        $oNewGroup->save();

        // few discounts
        $this->aDiscounts[0] = oxNew("oxBase");
        $this->aDiscounts[0]->init("oxdiscount");
        $this->aDiscounts[0]->setId('testdiscount0');
        $this->aDiscounts[0]->oxdiscount__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxactive = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxtitle = new oxField('Test discount 0', oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxamountto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxpriceto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxaddsumtype = new oxField("itm", oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxaddsum = new oxField(50, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxitmartid = new oxField('xxx', oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxitmamount = new oxField(2, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxitmmultiple = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxsort = new oxField(9900, oxField::T_RAW);
        $this->aDiscounts[0]->save();

        $this->aDiscounts[1] = oxNew("oxBase");
        $this->aDiscounts[1]->init("oxdiscount");
        $this->aDiscounts[1]->setId('testdiscount1');
        $this->aDiscounts[1]->oxdiscount__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxactive = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxtitle = new oxField('Test discount 1', oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxamountto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxpriceto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxaddsumtype = new oxField("itm", oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxaddsum = new oxField(50, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxitmartid = new oxField('xxx', oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxitmamount = new oxField(2, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxitmmultiple = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[1]->oxdiscount__oxsort = new oxField(9910, oxField::T_RAW);
        $this->aDiscounts[1]->save();

        $this->aDiscounts[2] = oxNew("oxBase");
        $this->aDiscounts[2]->init("oxdiscount");
        $this->aDiscounts[2]->setId('testdiscount2');
        $this->aDiscounts[2]->oxdiscount__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxactive = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxtitle = new oxField('Test discount 2', oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxamountto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxpriceto = new oxField(99999, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxaddsumtype = new oxField("itm", oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxaddsum = new oxField(50, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxitmartid = new oxField('yyy', oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxitmamount = new oxField(2, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxitmmultiple = new oxField(1, oxField::T_RAW);
        $this->aDiscounts[2]->oxdiscount__oxsort = new oxField(9920, oxField::T_RAW);
        $this->aDiscounts[2]->save();

        // assigning discounts
        $oDisc2Art = oxNew("oxBase");
        $oDisc2Art->init("oxobject2discount");
        $oDisc2Art->setId("_dsci1");
        $oDisc2Art->oxobject2discount__oxdiscountid = new oxField($this->aDiscounts[0]->getId(), oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxobjectid = new oxField($sNewId, oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oDisc2Art->save();

        $oDisc2Art = oxNew("oxBase");
        $oDisc2Art->init("oxobject2discount");
        $oDisc2Art->setId("_dsci2");
        $oDisc2Art->oxobject2discount__oxdiscountid = new oxField($this->aDiscounts[1]->getId(), oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxobjectid = new oxField($sNewId, oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oDisc2Art->save();

        // adding variant for article
        $sNewVarId = oxUtilsObject::getInstance()->generateUId();
        $this->oVariant = oxNew('oxArticle');
        $this->oVariant->disableLazyLoading();
        $this->oVariant->Load($sNewId);
        $this->oVariant->setId($sNewVarId);
        $this->oVariant->oxarticles__oxparentid = new oxField($sNewId, oxField::T_RAW);
        $this->oVariant->save();

        $this->oArticle = oxNew('oxArticle');
        $this->oArticle->disableLazyLoading();
        $this->oArticle->Load($sNewId);

        // inserting vouchers
        $this->oVoucherSerie = oxNew('oxvoucherserie');
        $this->oVoucherSerie->oxvoucherseries__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxserienr = new oxField('_xxx', oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxdiscount = new oxField(10.00, oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxdiscounttype = new oxField('absolute', oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxallowsameseries = new oxField(1, oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxallowotherseries = new oxField(1, oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxallowuseanother = new oxField(1, oxField::T_RAW);
        $this->oVoucherSerie->oxvoucherseries__oxminimumvalue = new oxField(10.00, oxField::T_RAW);
        $this->oVoucherSerie->save();

        for ($i = 0; $i < 4; $i++) {
            $oVoucher = oxNew('oxvoucher');
            $oVoucher->oxvouchers__oxreserved = new oxField(0, oxField::T_RAW);
            $oVoucher->oxvouchers__oxvouchernr = new oxField(md5(uniqid(rand(), true)), oxField::T_RAW);
            $oVoucher->oxvouchers__oxvoucherserieid = new oxField($this->oVoucherSerie->getId(), oxField::T_RAW);
            $oVoucher->save();
            $this->aVouchers[$oVoucher->oxvouchers__oxvouchernr->value] = $oVoucher;
        }

        // creating delivery address
        $this->oDelAdress = oxNew('oxBase');
        $this->oDelAdress->Init('oxaddress');
        $this->oDelAdress->oxaddress__oxcountryid = new oxField('_xxx', oxField::T_RAW);
        $this->oDelAdress->save();

        // creating card
        $this->oCard = oxNew('oxwrapping');
        $this->oCard->oxwrapping__oxtype = new oxField("CARD", oxField::T_RAW);
        $this->oCard->oxwrapping__oxname = new oxField("Test card", oxField::T_RAW);
        $this->oCard->oxwrapping__oxprice = new oxField(10, oxField::T_RAW);
        $this->oCard->save();

        // creating wrap paper
        $this->oWrap = oxNew('oxwrapping');
        $this->oWrap->oxwrapping__oxtype = new oxField("WRAP", oxField::T_RAW);
        $this->oWrap->oxwrapping__oxname = new oxField("Test card", oxField::T_RAW);
        $this->oWrap->oxwrapping__oxprice = new oxField(5, oxField::T_RAW);
        $this->oWrap->save();

        // enabling stock control
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('blVariantParentBuyable', true);

        oxRegistry::get("oxDiscountList")->forceReload();

        $sName = $this->getName();
        if ($sName == 'testBasketCalculationWithSpecUseCaseDescribedAbove' ||
            $sName == 'testBasketCalculationWithSpecUseCaseDescribedAboveJustDiscountIsAppliedByPrice' ||
            $sName == 'testUpdateBasketTwoProductsWithSameSelectionList'
        ) {
            $this->_prepareDataForTestBasketCalculationWithSpecUseCaseDescribedAbove();
        }

        $this->blPerfLoadSelectLists = $this->getConfig()->getConfigParam('bl_perfLoadSelectLists');

        //empty oxuserbasket
        oxDb::getDb()->execute('delete from oxuserbaskets');
        oxDb::getDb()->execute('delete from oxuserbasketitems');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // deleting articles+variants
        if ($this->oArticle) {
            $this->oArticle->delete();
            $this->oArticle = null;
        }

        // deleting category
        if ($this->oCategory) {
            $this->oCategory->delete();
            $this->oCategory = null;
        }

        // deleting selection lists
        if ($this->oSelList) {
            $this->oSelList->delete();
            $this->oSelList = null;
        }

        // deleting delivery address info
        if ($this->oDelAdress) {
            $this->oDelAdress->delete();
            $this->oDelAdress = null;
        }

        // deleting demo wrapping
        if ($this->oCard) {
            $this->oCard->delete();
            $this->oCard = null;
        }

        if ($this->oWrap) {
            $this->oWrap->delete();
            $this->oWrap = null;
        }

        // deleting vouchers
        if ($this->aVouchers) {
            foreach ($this->aVouchers as $oVoucher) {
                $oVoucher->delete();
            }
            $this->aVouchers = null;
        }

        if ($this->oVoucherSerie) {
            $this->oVoucherSerie->delete();
            $this->oVoucherSerie = null;
        }

        // deleting discounts
        if ($this->aDiscounts) {
            foreach ($this->aDiscounts as $oDiscount) {
                $oDiscount->delete();
            }
            $this->aDiscounts = null;
        }

        $this->oVariant = null;


        oxDb::getDb()->execute('delete from oxuserbaskets');
        oxDb::getDb()->execute('delete from oxuserbasketitems');

        $sName = $this->getName();
        if ($sName == 'testBasketCalculationWithSpecUseCaseDescribedAbove' ||
            $sName == 'testBasketCalculationWithSpecUseCaseDescribedAboveJustDiscountIsAppliedByPrice' ||
            $sName == 'testUpdateBasketTwoProductsWithSameSelectionList'
        ) {
            $this->_cleanupDataAfterTestBasketCalculationWithSpecUseCaseDescribedAbove();
        }

        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxartextends');
        $this->cleanUpTable('oxseo', 'oxobjectid');
        $this->cleanUpTable('oxprice2article');
        $this->cleanUpTable('oxobject2discount');

        $this->addTableForCleanup('oxarticles');
        $this->addTableForCleanup('oxseo');
        $this->addTableForCleanup('oxobject2selectlist');
        $this->addTableForCleanup('oxselectlist');

        $this->addTableForCleanup('oxselectlist2shop');
        $this->addTableForCleanup('oxarticles2shop');
        $this->addTableForCleanup('oxdiscount2shop');

        oxArticleHelper::cleanup();
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', $this->blPerfLoadSelectLists);
        parent::tearDown();
    }

    /**
     * prepare data for test basket calculation
     *
     * @return null
     */
    protected function _prepareDataForTestBasketCalculationWithSpecUseCaseDescribedAbove()
    {

        oxDb::getDb()->execute('delete from oxuserbaskets');
        oxDb::getDb()->execute('delete from oxuserbasketitems');

        $sArtId = '1126';

        // creating select lists..
        $oSelList = oxNew('oxSelectlist');
        $oSelList->setId('_testoxsellist');
        $oSelList->oxselectlist__oxtitle = new oxfield('testsel');
        $oSelList->oxselectlist__oxvaldesc = new oxfield('Large__@@Medium__@@Small__@@');
        $oSelList->save();

        // assigning sel list
        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init("oxobject2selectlist");
        $oO2Sel->setId('_testoxobject2selectlist');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxfield($sArtId);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxfield($oSelList->getId());
        $oO2Sel->save();
    }

    /**
     * cleanup data after test basket calculation
     *
     * @return null
     */
    protected function _cleanupDataAfterTestBasketCalculationWithSpecUseCaseDescribedAbove()
    {
        $this->cleanUpTable('oxselectlist');
        $this->cleanUpTable('oxobject2selectlist');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxobject2discount');
        $this->cleanUpTable('oxobject2category');
    }

    /**
     * testing calculation of discount of brutto price
     *
     * @return null
     */
    public function testGetDiscountedProductsBruttoPrice()
    {
        $oProdPrice = new oxPrice(1199);
        $oProdPrice->setBruttoPriceMode();

        $oProdPriceList = oxNew('oxPriceList');
        $oProdPriceList->addToPriceList($oProdPrice);

        $oTotalDiscount = new oxPrice(100);
        $oTotalDiscount->setBruttoPriceMode();

        $oVoucherDiscount = new oxPrice(100);
        $oVoucherDiscount->setBruttoPriceMode();

        $oBasket = $this->getMock("oxbasket", array("getDiscountProductsPrice", "getTotalDiscount", "getVoucherDiscount"));
        $oBasket->expects($this->once())->method('getDiscountProductsPrice')->will($this->returnValue($oProdPriceList));
        $oBasket->expects($this->once())->method('getTotalDiscount')->will($this->returnValue($oTotalDiscount));
        $oBasket->expects($this->once())->method('getVoucherDiscount')->will($this->returnValue($oVoucherDiscount));

        $this->assertEquals(999, $oBasket->getDiscountedProductsBruttoPrice());
    }

    /**
     * test if basket regard min order price
     *
     * @return null
     */
    public function testIsBelowMinOrderPriceEmptyBasket()
    {
        $this->getConfig()->setConfigParam("iMinOrderPrice", 2);

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(0));
        $oBasket->expects($this->never())->method('getDiscountedProductsBruttoPrice');

        $this->assertFalse($oBasket->isBelowMinOrderPrice());
    }

    /**
     * test if basket regard min order price
     *
     * @return null
     */
    public function testIsBelowMinOrderPrice()
    {
        $oConfig = $this->getConfig();

        $oConfig->setConfigParam("iMinOrderPrice", 2);

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->any())->method('getDiscountedProductsBruttoPrice')->will($this->returnValue(1));

        $this->assertTrue($oBasket->isBelowMinOrderPrice());

        $oConfig->setConfigParam("iMinOrderPrice", 10.5);

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->any())->method('getDiscountedProductsBruttoPrice')->will($this->returnValue(10));

        $this->assertTrue($oBasket->isBelowMinOrderPrice());

        $oConfig->setConfigParam("iMinOrderPrice", 10.21);

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->any())->method('getDiscountedProductsBruttoPrice')->will($this->returnValue(10.2));

        $this->assertTrue($oBasket->isBelowMinOrderPrice());


    }

    /**
     * testing the update of basket after adding two products with same selection list
     *
     * @return null
     */
    public function testUpdateBasketTwoProductsWithSameSelectionList()
    {
        $sArtId = '1126';
        $oBasket = oxNew('oxBasket');

        // creating selection list
        $oSelList = oxNew('oxSelectlist');
        $oSelList->setId('_testoxsellist');
        $oSelList->oxselectlist__oxtitle = new oxfield('testsel');
        $oSelList->oxselectlist__oxvaldesc = new oxfield('Large__@@Medium__@@Small__@@');
        $oSelList->save();

        // assigning sel list
        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init("oxobject2selectlist");
        $oO2Sel->setId('_testoxobject2selectlist');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxfield($sArtId);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxfield($oSelList->getId());
        $oO2Sel->save();

        // storing products to basket with diff sel list
        $oBasket->addToBasket($sArtId, 1, array(0));
        $oBasket->calculateBasket();

        $oBasket->addToBasket($sArtId, 1, array(1));
        $oBasket->calculateBasket();

        // checking amounts
        $aContents = $oBasket->getContents();
        $this->assertEquals(2, count($aContents));

        // checking counts
        $oBasketItem = reset($aContents);
        $this->assertEquals(1, $oBasketItem->getAmount());

        next($aContents);

        // updating last product selection list
        $oBasket->addToBasket($sArtId, 1, array(0), null, true, false, key($aContents));
        $oBasket->calculateBasket();

        // checking final basket amount
        $aContents = $oBasket->getContents();
        $this->assertEquals(1, count($aContents));

        // checking counts
        $oBasketItem = reset($aContents);
        $this->assertEquals(2, $oBasketItem->getAmount());
    }

    /**
     * testing the setter setDiscountCalcMode()
     *
     * @return null
     */
    public function testSetDiscountCalcModeAndCanCalcDiscounts()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->setDiscountCalcMode(false);
        $this->assertFalse($oBasket->canCalcDiscounts());

        $oBasket->setDiscountCalcMode(true);
        $this->assertTrue($oBasket->canCalcDiscounts());
    }

    /**
     * testing setter setVoucherDiscount
     *
     * @return null
     */
    public function testSetVoucherDiscount()
    {
        $dDiscount = 9;
        $oDiscountPrice = oxNew('oxPrice');
        $oDiscountPrice->setBruttoPriceMode();
        $oDiscountPrice->add($dDiscount);

        $oBasket = oxNew('oxbasket');
        $oBasket->setVoucherDiscount($dDiscount);

        $this->assertEquals($oDiscountPrice, $oBasket->getVoucherDiscount());
    }

    /**
     * check if the basket handles an article with amount 0 correctly.
     *
     * @return null
     */
    public function testAddOrderArticleToBasketAmountIsZero()
    {
        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(0);
        $oOrderArticle->oxorderarticles__oxisbundle = new oxField(0);

        $oBasket = oxNew('oxbasket');
        $this->assertNull($oBasket->addOrderArticleToBasket($oOrderArticle));
    }

    /**
     * test adding an article to basket
     *
     * @return null
     */
    public function testAddOrderArticleToBasket()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId("sOrderArticleId");
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxwrapid = new oxField("swrapid");

        $oTestBasketItem = oxNew('oxBasketItem');
        $oTestBasketItem->initFromOrderArticle($oOrderArticle);
        $oTestBasketItem->setWrapping("swrapid");

        $oBasket = $this->getProxyClass("oxbasket");
        $oBasketItem = $oBasket->addOrderArticleToBasket($oOrderArticle);
        $aBasketContents = $oBasket->getNonPublicVar("_aBasketContents");

        $this->assertEquals($oTestBasketItem, $oBasketItem);
        $this->assertTrue(isset($aBasketContents["sOrderArticleId"]));
        $this->assertEquals($oTestBasketItem, $aBasketContents["sOrderArticleId"]);
    }

    /**
     * testing total discounts
     *
     * @return null
     */
    public function testSetTotalDiscount()
    {
        $oDiscount = oxNew('oxPrice');
        $oDiscount->setBruttoPriceMode();
        $oDiscount->add(999);

        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setTotalDiscount(999);

        $this->assertEquals($oDiscount, $oBasket->getNonPublicVar("_oTotalDiscount"));
    }

    /**
     * tests oxBasket::_canSaveBasket()
     *
     * @return null
     */
    public function testCanSaveBasket()
    {
        $oBasket = $this->getProxyClass('oxbasket');
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);
        $this->assertTrue($oBasket->UNITcanSaveBasket());
    }

    /**
     * Negative oxBasket::_canSaveBasket() test
     *
     * @return null
     */
    public function testCanSaveBasketNegative()
    {
        $oBasket = $this->getProxyClass('oxbasket');
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);
        $this->assertFalse($oBasket->UNITcanSaveBasket());
    }

    /**
     * test the merging of basket
     *
     * @return null
     */
    public function testSaveLoad()
    {
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket('1126', 2);
        $oBasket->addToBasket('1127', 2);

        $oBasket->UNITsave();

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $aContents = $oBasket->getContents();
        $this->assertEquals(0, count($aContents));

        $oBasket->load();
        $aContents = $oBasket->getContents();
        $this->assertEquals(2, count($aContents));

        $oItem = current($aContents);
        $this->assertEquals('1126', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());

        $oItem = next($aContents);
        $this->assertEquals('1127', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());
    }

    /**
     * Test saved basket loading
     *
     * @return null;
     */
    public function testLoad()
    {
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket('1126', 2);
        $oBasket->calculateBasket(true);
        $oBasket->addToBasket('1127', 2);
        $oBasket->calculateBasket(true);

        //create new basket, calling load will restore basket from database
        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $aContents = $oBasket->getContents();
        $this->assertEquals(0, count($aContents));
        $oBasket->load();

        $aContents = $oBasket->getContents();
        $this->assertEquals(2, count($aContents));


        $oItem = current($aContents);
        $this->assertEquals('1126', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());

        $oItem = next($aContents);
        $this->assertEquals('1127', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());

    }

    /**
     * test the merging of basket, after all itmes were deleted
     * from basket.
     *
     * @return null
     */
    public function testLoadDelete()
    {
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket('1126', 2);
        $oBasket->calculateBasket(true);
        $oBasket->addToBasket('1127', 2);
        $oBasket->calculateBasket(true);

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $oBasket->load();

        $aContents = $oBasket->getContents();
        $this->assertEquals(2, count($aContents));
        $oItem = current($aContents);
        $this->assertEquals('1126', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());

        $oItem = next($aContents);
        $this->assertEquals('1127', $oItem->getArticle()->getId());
        $this->assertEquals(2, $oItem->getAmount());

        $oBasket->addToBasket('1126', 0, null, null, true);
        $oBasket->addToBasket('1127', 0, null, null, true);
        $oBasket->calculateBasket(true);

        $aContents = $oBasket->getContents();
        $this->assertEquals(0, count($aContents));

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);
        $oBasket->calculateBasket(true);
        $aContents = $oBasket->getContents();
        $this->assertEquals(0, count($aContents));
    }

    /**
     * testing getter getStockCheckMode
     *
     * @return null
     */
    public function testStockStatusGetterCheck()
    {
        $oBasket = $this->getMock('oxbasket', array('isEnabled', 'getStockCheckMode'));
        $oBasket->expects($this->exactly(2))->method('isEnabled')->will($this->returnValue(true));
        $oBasket->expects($this->exactly(2))->method('getStockCheckMode');

        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oArticle->getId(), 10);
    }

    /**
     * testing setter setStockCheckMode
     *
     * @return null
     */
    public function testStockStatusSetterCheck()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->setStockCheckMode(false);
        $this->assertFalse($oBasket->getStockCheckMode());

        $oBasket->setStockCheckMode(true);
        $this->assertTrue($oBasket->getStockCheckMode());
    }

    /**
     * Testing basket articles getter
     *
     * @return null
     */
    public function testGetBasketArticles()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $aIds = array($this->oArticle->getId(), $this->oVariant->getId());

        $iSelArticleId = $this->oArticle->getId();
        $blSelFound = false;

        foreach ($oBasket->getBasketArticles() as $oArticle) {

            // selection list check
            if ($iSelArticleId == $oArticle->getId()) {
                $aSelectList = $oArticle->getDispSelList();
                $blSelFound = isset($aSelectList);
            }

            $this->assertTrue(in_array($oArticle->getId(), $aIds));
        }

        if (!$blSelFound) {
            $this->fail('missing selection list');
        }
    }

    /**
     * #1115: Usability Problem during checkout with products without stock
     *
     * @return null
     */
    public function testGetBasketArticlesIfArtIsOffline()
    {
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->init($this->oArticle->getId(), 1);
        $oBasketItem->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem));

        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();
        $this->assertEquals(0, count($oBasket->getBasketArticles()));
    }

    /**
     * #1318: exception is thrown if product (not orderable if out of stock) goes out of stock during order process
     *
     * @return null
     */
    public function testGetBasketArticlesIfArtIsNotBuyable()
    {
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->init($this->oArticle->getId(), 1);
        $oBasketItem->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem));

        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $this->oArticle->save();
        $this->assertEquals(0, count($oBasket->getBasketArticles()));
    }

    /**
     * Testing adding to basket process
     * adding to basket is disabled, should return null after adding ...
     *
     * @return null
     */
    public function testAddToBasketDisabled()
    {
        $oBasket = $this->getMock('oxbasket', array('isEnabled'));
        $oBasket->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->assertNull($oBasket->addToBasket($this->oArticle->getId(), 10));
    }

    /**
     * Testing adding to basket process
     * normally adding item to basket and testing if it was added
     *
     * @return null
     */
    public function testAddToBasketNormalArticle()
    {
        $oBasket = oxNew('oxbasket');
        $oBasketItem = $oBasket->addToBasket($this->oArticle->getId(), 10);
        $this->assertEquals(10, $oBasketItem->getAmount());
        $this->assertEquals(100, $oBasketItem->getWeight());
        $this->assertEquals($this->oArticle->getId(), $oBasketItem->getProductId());
    }

    /**
     * Testing adding to basket process
     * user wrote bad amount information
     *
     * @return null
     */
    public function testAddToBasketBadInput()
    {
        $oBasket = oxNew('oxbasket');
        try {
            $oBasket->addToBasket($this->oArticle->getId(), 'xxx');
        } catch (oxArticleInputException $oExcp) {
            return;
        }
        $this->fail('failed testing addToBasket');
    }

    /**
     * Testing adding to basket process
     * user wrote bad amount information
     *
     * @return null
     */
    public function testAddToBasketOutOfStock()
    {
        $oBasket = oxNew('oxbasket');
        try {
            $oBasket->addToBasket($this->oArticle->getId(), 666);
        } catch (oxOutOfStockException $oExcp) {
            return;
        }
        $this->fail('failed testing addToBasket');
    }

    /**
     * Testing adding to basket process
     * normally adding item to basket and testing if it was added
     *
     * @return null
     */
    public function testAddToBasketAddingTwiceAncCheckingAmounts()
    {
        $oBasket = oxNew('oxBasket');
        $oBasketItem = $oBasket->addToBasket($this->oArticle->getId(), 10, null, null, false, true);
        $oBasketItem = $oBasket->addToBasket($this->oArticle->getId(), 10, null, null, false, true);
        $this->assertEquals(20, $oBasketItem->getAmount());
        $this->assertEquals(200, $oBasketItem->getWeight());
        $this->assertEquals($this->oArticle->getId(), $oBasketItem->getProductId());
    }

    /**
     * Testing adding to basket process
     * normally adding item to basket and testing if it was added
     *
     * @return null
     */
    public function testAddToBasketAddingArticleWithSelectlist()
    {
        $oBasket = oxNew('oxBasket');
        $oBasketItem = $oBasket->addToBasket($this->oArticle->getId(), 10, array('0'), null, false, true);
        $oBasketItem = $oBasket->addToBasket($this->oArticle->getId(), 10, null, null, false, true);
        $this->assertEquals(20, $oBasketItem->getAmount());
        $this->assertEquals(200, $oBasketItem->getWeight());
        $this->assertEquals($this->oArticle->getId(), $oBasketItem->getProductId());
    }

    /**
     * Testing adding to basket process
     * removing item by setting amount 0
     *
     * @return null
     */
    public function testAddToBasketRemovingBySettingZero()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 0, null, null);
        $this->assertEquals(0, count($oBasket->getBasketArticles()));
    }

    /**
     * Testing adding to basket process
     *
     * @return null
     */
    public function testAddToBasketSavingBasketHistory()
    {
        $oBasket = $this->getMock('oxbasket', array('_addItemToSavedBasket'));
        $oBasket->expects($this->never())->method('_addItemToSavedBasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
    }

    /**
     * Testing adding bundled price to basket
     *
     * @return null
     */
    public function testAddToBasketBundle()
    {
        $oBasket = $this->getMock('oxbasket', array('_addItemToSavedBasket'));
        $oBasket->expects($this->never())->method('_addItemToSavedBasket');
        $this->assertFalse($oBasket->isNewItemAdded());
        $oBasket->addToBasket($this->oArticle->getId(), 10, null, false, true);
        $this->assertFalse($oBasket->isNewItemAdded());
    }

    /**
     * Testing item key generator
     *
     * @return null
     */
    public function testGetItemKey()
    {
        $sKey = md5('_xxx' . '|' . serialize(array('_xxx')) . '|' . serialize(array('_xxx')) . '|' . ( int ) true . '|' . serialize('_xxx'));

        $oBasket = oxNew('oxbasket');
        $this->assertEquals($sKey, $oBasket->getItemKey('_xxx', array('_xxx'), array('_xxx'), true, '_xxx'));
    }

    /**
     * Testing item key generator if selectlist is null
     *
     * @return null
     */
    public function testGetItemKeyIfSelListEmpty()
    {
        $sKey = md5('_xxx' . '|' . serialize(array('0')) . '|' . serialize(array('_xxx')) . '|' . ( int ) true . '|' . serialize('_xxx'));

        $oBasket = oxNew('oxbasket');
        $this->assertEquals($sKey, $oBasket->getItemKey('_xxx', null, array('_xxx'), true, '_xxx'));
    }

    /**
     * Testing if article removal from basket really works
     *
     * @return null
     */
    public function testRemoveItem()
    {
        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 10, null, null, false, true);
        $sKey = key($oBasket->getContents());

        // testing if THIS item was added
        $this->assertEquals($this->oArticle->getId(), $oItem->getProductId());

        $oBasket->removeItem($sKey);

        // testing if it was removed
        $this->assertEquals(array(), $oBasket->getContents());
    }

    /**
     * Testing if article removal from reserved basket really works
     *
     * @return null
     */
    public function testRemoveItemReserved()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $oBR = $this->getMock('oxBasketReservation', array('discardArticleReservation'));
        $oBR->expects($this->once())->method('discardArticleReservation')->with($this->equalTo($this->oArticle->getId()))->will($this->returnValue(null));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oBasket = $this->getMock('oxbasket', array('getSession'));
        $oBasket->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 10, null, null, false, true);
        $sKey = key($oBasket->getContents());

        // testing if THIS item was added
        $this->assertEquals($this->oArticle->getId(), $oItem->getProductId());

        $oBasket->removeItem($sKey);

        // testing if it was removed
        $this->assertEquals(array(), $oBasket->getContents());
    }

    /**
     * Testing if bundle article removal from basket really works
     *
     * @return null
     */
    public function testClearBundles()
    {
        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $oBasket->addToBasket($this->oArticle->getId(), 2, null, null, false, true);

        // first bundle is added separatelly
        $this->assertEquals(2, count($oBasket->getContents()));

        $oBasket->UNITclearBundles();

        // first bundle is added separatelly
        $oItem2 = $oBasket->getContents();
        $this->assertEquals(1, count($oItem2));
        $this->assertEquals($oItem, reset($oItem2));
    }

    /**
     * Testing if article bundle information if collected fine (PE only)
     * has no bundle article
     *
     * @return null
     */
    public function testGetArticleBundlesHasNoBundles()
    {
        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1, null, null, false, true);
        $this->assertEquals(array(), $oBasket->UNITgetArticleBundles($oItem));
    }

    /**
     * Testing if article bundle information if collected fine (PE only)
     * has bundle article information (PE only)
     *
     * @return null
     */
    public function testGetArticleBundlesHasSomeBundle()
    {
        $this->oArticle->oxarticles__oxbundleid = new oxField('xxx', oxField::T_RAW);
        $this->oArticle->save();

        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals(array('xxx' => 1), $oBasket->UNITgetArticleBundles($oItem));
    }

    /**
     * Testing how correctly bundle information is loaded
     * basket item is bundle itself, so it does not load additional bundle information
     *
     * @return null
     */
    public function testGetItemBundlesItemIsBundle()
    {
        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1, null, null, false, true);
        $this->assertEquals(array(), $oBasket->UNITgetItemBundles($oItem));
    }

    /**
     * Testing how correctly bundle information is loaded
     * basket item has no bundle assigned
     *
     * @return null
     */
    public function testGetItemBundlesItemHasNoBundles()
    {
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals(array(), $oBasket->UNITgetItemBundles($oItem));
    }

    /**
     * Testing how correctly bundle information is loaded
     * basket item has some bundled items
     *
     * @return null
     */
    public function testGetItemBundlesItemHasBundles()
    {
        $this->aDiscounts[1]->oxdiscount__oxitmmultiple = new oxField(0, oxField::T_RAW);
        $this->aDiscounts[1]->save();

        $this->aDiscounts[0]->oxdiscount__oxitmmultiple = new oxField(0, oxField::T_RAW);
        $this->aDiscounts[0]->save();
        $aArray = array('xxx' => (double) 2);

        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals($aArray, $oBasket->UNITgetItemBundles($oItem, $aArray));
    }

    /**
     * Testing how correctly bundle information is loaded
     * basket item has some bundled items
     *
     * @return null
     */
    public function testGetItemBundlesItemHasBundlesMultiplay()
    {
        $aArray = array('xxx' => (double) 2);

        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals(array('xxx' => (double) 6), $oBasket->UNITgetItemBundles($oItem, $aArray));
    }

    /**
     * Whole basket bundles
     * has one bundle item
     *
     * @return null
     */
    public function testGetBasketBundlesHasBundledItem()
    {
        $aArray = array('yyy' => (double) 2);

        $oBasket = $this->getProxyClass("oxbasket");
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals($aArray, $oBasket->UNITgetBasketBundles());
    }

    // has no bundle items
    public function testGetBasketBundlesHasNoBundledItem()
    {
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 1);
        $this->assertEquals(array(), $oBasket->UNITgetBasketBundles());
    }

    /**
     * Testing bundles adding method
     *
     * @return null
     */
    public function testAddBundles()
    {

        // simulating basket contents
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($this->oArticle->getId(), 1);

        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('_getItemBundles', 'addToBasket', '_getBasketBundles'));
        $oBasket->expects($this->once())->method('_getItemBundles')->will($this->returnValue(array('x' => 1)));
        $oBasket->expects($this->exactly(1))->method('addToBasket')->will($this->returnValue($oBasketItem));
        $oBasket->expects($this->once())->method('_getBasketBundles')->will($this->returnValue(array('x' => 1)));

        // testing
        $oBasket->setBasket(array($oBasketItem));
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITaddBundles();
        $this->assertTrue($oBasketItem->isDiscountArticle());
    }

    /**
     * #1115: Usability Problem during checkout with products without stock
     *
     * @return null
     */
    public function testAddBundlesIfArtIsOffline()
    {
        // simulating basket contents
        $oBasketItem = $this->getMock('oxbasketitem', array('isBundle'));
        $oBasketItem->expects($this->any())->method('isBundle')->will($this->returnValue(true));
        $oBasketItem->init($this->oArticle->getId(), 1);

        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();
        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('addToBasket'));
        $oBasket->expects($this->never())->method('addToBasket')->will($this->returnValue($oBasketItem));

        // testing
        $oBasket->setBasket(array($oBasketItem));
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITaddBundles();
        $this->assertFalse($oBasketItem->isDiscountArticle());
    }

    /**
     * #1318: exception is thrown if product (not orderable if out of stock) goes out of stock during order process
     *
     * @return null
     */
    public function testAddBundlesIfArtIsNotBuyable()
    {
        // simulating basket contents
        $oBasketItem = $this->getMock('oxbasketitem', array('isBundle'));
        $oBasketItem->expects($this->any())->method('isBundle')->will($this->returnValue(true));
        $oBasketItem->init($this->oArticle->getId(), 1);

        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(3, oxField::T_RAW);
        $this->oArticle->save();
        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('addToBasket'));
        $oBasket->expects($this->never())->method('addToBasket')->will($this->returnValue($oBasketItem));

        // testing
        $oBasket->setBasket(array($oBasketItem));
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITaddBundles();
        $this->assertFalse($oBasketItem->isDiscountArticle());
    }

    /**
     * Testing bundles adding method
     *
     * @return null
     */
    public function testAddBundlesIfDiscountArticle()
    {
        // simulating basket contents
        $oBasketItem = $this->getMock('oxbasketitem', array('isDiscountArticle'));
        $oBasketItem->expects($this->any())->method('isDiscountArticle')->will($this->returnValue(true));
        $oBasketItem->init($this->oArticle->getId(), 1);

        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('_getItemBundles', 'addToBasket', '_getBasketBundles'));
        $oBasket->expects($this->never())->method('_getItemBundles');
        $oBasket->expects($this->never())->method('addToBasket');
        $oBasket->expects($this->once())->method('_getBasketBundles');

        // testing
        $oBasket->setBasket(array($oBasketItem));
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITaddBundles();
    }

    /**
     * Testing bundles adding method
     * #2576
     *
     * @return null
     */
    public function testAddBundlesIfAssignedCategory()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticle');
        $oArticle->oxarticles__oxweight = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(100, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(19, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();

        // assigning article to category
        $oArt2Cat = oxNew("oxobject2category");
        $oArt2Cat->oxobject2category__oxobjectid = new oxField('_testArticle', oxField::T_RAW);
        $oArt2Cat->oxobject2category__oxcatnid = new oxField($this->oCategory->getId(), oxField::T_RAW);
        $oArt2Cat->save();

        $this->aDiscounts[2]->oxdiscount__oxitmmultiple = new oxField(0, oxField::T_RAW);
        $this->aDiscounts[2]->save();

        $oDisc2Art = oxNew("oxBase");
        $oDisc2Art->init("oxobject2discount");
        $oDisc2Art->setId("_dsci3");
        $oDisc2Art->oxobject2discount__oxdiscountid = new oxField($this->aDiscounts[2]->getId(), oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxobjectid = new oxField($this->oCategory->getId(), oxField::T_RAW);
        $oDisc2Art->oxobject2discount__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oDisc2Art->save();

        // simulating basket contents
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($this->oArticle->getId(), 1);

        $oBasketItem2 = oxNew('oxbasketitem');
        $oBasketItem2->init($oArticle->getId(), 1);

        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('_addBundlesToBasket'));
        $oBasket->expects($this->exactly(3))->method('_addBundlesToBasket');

        // testing
        $oBasket->setBasket(array($oBasketItem, $oBasketItem2));
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITaddBundles();
    }

    /**
     * Testing item price calculator adding bundles
     *
     * @return null
     */
    public function testCalcItemsPriceAddBundles()
    {
        $oBasket = new modForTestAddBundles();

        // simulating basket contents
        $oBasketItem = $this->getMock('oxbasketitem', array('isDiscountArticle', 'isBundle'));
        $oBasketItem->expects($this->any())->method('isDiscountArticle')->will($this->returnValue(true));
        $oBasketItem->expects($this->any())->method('isBundle')->will($this->returnValue(true));
        $oBasketItem->init($this->oArticle->getId(), 1);
        $oBasket->setBasket(array($oBasketItem));
        $oBasket->UNITcalcItemsPrice();
        $aBasketContents = $oBasket->getVar('aBasketContents');
        foreach ($aBasketContents as $oBasketItem) {
            $this->assertEquals(0, $oBasketItem->getPrice()->getBruttoPrice());
        }
    }

    /**
     * Testing item price calculator
     *
     * @return null
     */
    public function testCalcItemsPrice()
    {
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = new modForTestAddBundles();
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->UNITcalcItemsPrice();

        $this->assertEquals(1, $oBasket->getVar('iProductsCnt'));
        $this->assertEquals(10, $oBasket->getVar('dItemsCnt'));
        $this->assertEquals(100, $oBasket->getVar('dWeight'));
    }

    /**
     * Testing basket item price calculation, if there is discount and bundles
     * #M320
     *
     * @return null
     */
    /* public function testCalculateBasketSetDiscountsAndBundles()
    {
        $this->aDiscounts[0]->oxdiscount__oxaddsumtype = new oxField("abs", oxField::T_RAW);
        $this->aDiscounts[0]->oxdiscount__oxaddsum = new oxField("5", oxField::T_RAW);
        $this->aDiscounts[0]->save();
        $this->aDiscounts[1]->oxdiscount__oxitmartid = new oxField('2000', oxField::T_RAW);
        $this->aDiscounts[1]->save();

        $this->oArticle->oxarticles__oxvat = new oxField(10, oxField::T_RAW);
        $this->oArticle->oxarticles__oxprice = new oxField(60, oxField::T_RAW);
        $this->oArticle->save();

        $oBasket = $this->getProxyClass( "oxBasket" );
        $oBasket->addToBasket( $this->oArticle->getId(), 2 );

        $oBasket->calculateBasket( false );

        $aVAT = $oBasket->getDiscountProductsPrice()->getVatInfo();
        $this->assertEquals(10, round($aVAT[10], 2));
        $this->assertEquals(110, $oBasket->getDiscountProductsPrice()->getBruttoSum());
        $this->assertEquals(100, $oBasket->getDiscountedNettoPrice());
        $this->assertEquals(120, $oBasket->getProductsPrice()->getBruttoSum());

        $aItmList = $oBasket->getContents();
        $oArt1 = $aItmList[$oBasket->getItemKey($this->oArticle->getId())];
        $oArt2 = $aItmList[$oBasket->getItemKey('2000', null, null, true)];
        $this->assertEquals(120, $oArt1->getPrice()->getBruttoPrice());
        $this->assertEquals('120,00', $oArt1->getFTotalPrice());
        $this->assertEquals(0, $oArt2->getPrice()->getBruttoPrice());
        $this->assertEquals('0,00', $oArt2->getFTotalPrice());
    }

    /**
     * test merge method if it works fine
     *
     * @return null
     */
    public function testMergeDiscounts()
    {
        $oDiscount1 = new stdClass();
        $oDiscount1->dDiscount = 10;
        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 20;
        $oDiscount3 = new stdClass();
        $oDiscount3->dDiscount = 30;
        $oDiscount4 = new stdClass();
        $oDiscount4->dDiscount = 40;
        $aDiscounts = array();
        $aDiscounts['1'] = $oDiscount1;
        $aDiscounts['2'] = $oDiscount2;
        $aItemDiscounts['1'] = $oDiscount3;
        $oBasket = oxNew('oxbasket');
        $aReturn = $oBasket->UNITmergeDiscounts($aDiscounts, $aItemDiscounts);
        $aDiscounts['1'] = $oDiscount4;
        $this->assertEquals($aDiscounts, $aReturn);
    }

    /**
     * Testing delivery price calculation
     * no user, blCalculateDelCostIfNotLoggedIn = false - no costs
     *
     * @return null
     */
    public function testCalcDeliveryCostNoUser()
    {
        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', false);

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser(false);
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oPrice = $oBasket->UNITcalcDeliveryCost();

        $this->assertEquals(0, $oPrice->getVat());
        $this->assertEquals(0, $oPrice->getBruttoPrice());
        $this->assertEquals(0, $oPrice->getNettoPrice());
        $this->assertEquals(0, $oPrice->getVatValue());
        $this->assertEquals(0, $oPrice->getVatValue());
    }

    /**
     * Testing delivery price calculation
     * if free shipped ...
     *
     * @return null
     */
    public function testCalcDeliveryCostFreeShipped()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', false);
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);

        $oAdmin = oxNew('oxuser');
        $oAdmin->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oAdmin);
        $this->oArticle->oxarticles__oxfreeshipping = new oxField(true, oxField::T_RAW);
        $this->oArticle->save();
        $oBasket->addToBasket($this->oArticle->getId(), 1);
        $oBasket->calculateBasket(false);

        $oPrice = $oBasket->getCosts('oxdelivery');

        $this->assertEquals(0, $oPrice->getBruttoPrice());
    }

    /**
     * Tests setting and calculation of delivery costs
     *
     * @return null
     */
    public function testSetAndCalcDeliveryCost()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', false);
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);

        $oSetPrice = oxNew("oxprice");
        $oSetPrice->setPrice(5);

        $oAdmin = oxNew('oxuser');
        $oAdmin->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oAdmin);
        $this->oArticle->oxarticles__oxfreeshipping = new oxField(true, oxField::T_RAW);
        $this->oArticle->save();
        $oBasket->addToBasket($this->oArticle->getId(), 1);
        $oBasket->setDeliveryPrice($oSetPrice);
        $oBasket->calculateBasket(false);

        $oPrice = $oBasket->getCosts('oxdelivery');

        $this->assertEquals(5, $oPrice->getBruttoPrice());
    }

    /**
     * Testing basket user setter/getter
     *
     * @return null
     */
    public function testSetBasketUserAndGetBasketUserInOneTest()
    {
        $oUser = oxNew('oxUser');
        $oUser->xxx = 'yyy';

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);

        $this->assertEquals($oUser, $oBasket->getBasketUser());
    }

    /**
     * Testing basket user setter/getter
     *
     * @return null
     */
    public function testGetBasketUser()
    {
        $oUser = oxNew('oxUser');
        $oUser->xxx = 'qqq';

        $oBasket = oxNew('oxbasket');
        $oBasket->setUser($oUser);

        $this->assertEquals($oUser, $oBasket->getBasketUser());
    }

    /**
     * testing the setter setBasketUser
     *
     * @return null
     */
    public function testSetGetBasketUser()
    {
        $oSubj = oxNew('oxBasket');
        $oSubj->setBasketUser('testUser');
        $this->assertEquals('testUser', $oSubj->getBasketUser());
    }

    /**
     * testing basket method getBasketUser
     *
     * @return null
     */
    public function testGetBasketUserGlobal()
    {
        $oSubj = oxNew('oxBasket');
        $oSubj->setUser('testUser');
        $this->assertEquals('testUser', $oSubj->getBasketUser());
    }

    /**
     * Testing basket getter getBasketUser, if the correct user will return
     *
     * @return null
     */
    public function testGetBasketUserNonGlobal()
    {
        $oSubj = oxNew('oxBasket');
        $oSubj->setUser('testUser');
        $oSubj->setBasketUser('testLocalUser');
        $this->assertEquals('testLocalUser', $oSubj->getBasketUser());
    }

    /**
     * Testing most used VAT percent getter
     *
     * @return null
     */
    public function testGetMostUsedVatPercent()
    {
        $oProductsPriceList = $this->getMock('oxpricelist', array('getMostUsedVatPercent'));
        $oProductsPriceList->expects($this->once())->method('getMostUsedVatPercent');

        $oBasket = new modForTestAddBundles();
        $oBasket->setVar('oProductsPriceList', $oProductsPriceList);
        $oBasket->getMostUsedVatPercent();
    }

    /**
     * Testing how voucher calculation works
     *
     * @return null
     */
    public function testCalcVoucherDiscount()
    {
        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(999);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $aV = array();
        foreach ($this->aVouchers as $oV) {
            $aV[$oV->getId()] = $oV->getSimpleVoucher();
        }
        $oBasket->setNonPublicVar('_aVouchers', $aV);
        $oBasket->setNonPublicVar('_oTotalDiscount', oxNew('oxprice'));
        $this->assertNull($oBasket->getVoucherDiscount());
        $oBasket->UNITcalcVoucherDiscount();
        $aVouch = $oBasket->getVouchers();
        $this->assertEquals(10, end($aVouch)->dVoucherdiscount);
        $this->assertEquals(40, $oBasket->getVoucherDiscount()->getBruttoPrice());
    }

    /**
     * Testing how voucher calculation works
     *
     * @return null
     */
    public function testCalcVoucherDiscountIfVoucherIsWrong()
    {
        $oStdVoucher = new stdClass();
        $oStdVoucher->sVoucherId = "aaa";

        $oProductsPriceList = $this->getMock('oxpricelist', array('getBruttoSum'));
        $oProductsPriceList->expects($this->once())->method('getBruttoSum')->will($this->returnValue(9));

        $oBasket = new modForTestAddBundles();
        $oBasket->setVar('oDiscountProductsPriceList', $oProductsPriceList);
        $oBasket->setVar('oTotalDiscount', oxNew('oxprice'));
        $aV = array();
        foreach ($this->aVouchers as $oV) {
            $aV[$oV->getId()] = $oV->getSimpleVoucher();
        }
        $oBasket->setVar('aVouchers', $aV);
        $aV = $oBasket->getVouchers();
        $this->assertEquals(4, count($aV));

        $oBasket->UNITcalcVoucherDiscount();

        $aV = $oBasket->getVouchers();
        $this->assertEquals(0, count($aV));
    }

    /**
     * test if vouhers availability checking was skipped if skip param is on
     *
     * @return null
     */
    public function testCalcVoucherDiscountSkipChecking()
    {

        oxAddClassModule('oxVoucherHelper', 'oxvoucher');
        oxVoucherHelper::$blCheckWasPerformed = false;

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(999);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $aV = array();
        foreach ($this->aVouchers as $oV) {
            $aV[$oV->getId()] = $oV->getSimpleVoucher();
        }
        $oBasket->setNonPublicVar('_aVouchers', $aV);
        $oBasket->setNonPublicVar('_oTotalDiscount', oxNew('oxprice'));
        $oBasket->setSkipVouchersChecking(true);
        $oBasket->UNITcalcVoucherDiscount();

        $this->assertFalse(oxVoucherHelper::$blCheckWasPerformed);
    }

    /**
     * Testing wrapping calculation
     *
     * @return null
     */
    public function testCalcBasketWrapping()
    {
        $sWrapId = $this->oWrap->getId();
        $sCardId = $this->oCard->getId();

        // forcing some config params for deeper execution
        $this->getConfig()->setConfigParam('blWrappingVatOnTop', true);

        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');

        $this->assertFalse($oBasket->getFWrappingCosts());
        $this->assertFalse($oBasket->getWrappCostNet());
        $this->assertFalse($oBasket->getWrappCostVat());
        $this->assertFalse($oBasket->getFGiftCardCosts());
        $this->assertFalse($oBasket->getGiftCardCostNet());
        $this->assertFalse($oBasket->getGiftCardCostVat());

        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 2);
        $oItem->setWrapping($sWrapId);

        $oItem = $oBasket->addToBasket($this->oVariant->getId(), 3);
        $oItem->setWrapping($sWrapId);

        $oBasket->setCardId($sCardId);
        $oBasket->calculateBasket(false);

        $oWrapPrice = $oBasket->UNITcalcBasketWrapping();

        // wrapping
        $this->assertEquals((5 * 5) * 1.19, $oWrapPrice->getBruttoPrice());
        $this->assertEquals((5 * 5), $oWrapPrice->getNettoPrice());
        $this->assertEquals(19, (int) $oWrapPrice->getVat());

        // gift card
        $oCardPrice = $oBasket->UNITcalcBasketGiftCard();
        $this->assertEquals(11.9, $oCardPrice->getBruttoPrice());
        $this->assertEquals(10, $oCardPrice->getNettoPrice());
        $this->assertEquals(19, (int) $oCardPrice->getVat());

        $this->getConfig()->setConfigParam('blShowVATForWrapping', true);

        $this->assertEquals('29,75', $oBasket->getFWrappingCosts());
        $this->assertEquals('25,00', $oBasket->getWrappCostNet());
        $this->assertEquals('4,75', $oBasket->getWrappCostVat());
        $this->assertEquals('11,90', $oBasket->getFGiftCardCosts());
        $this->assertEquals('10,00', $oBasket->getGiftCardCostNet());
        $this->assertEquals('1,90', $oBasket->getGiftCardCostVat());

    }

    /**
     * Testing payment costs calculation
     *
     * @return null
     */
    public function testCalcPaymentCost()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);

        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        // choosing first payment which is active and has costs
        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 2);
        $oBasket->addToBasket($this->oVariant->getId(), 3);
        $oBasket->calculateBasket(false);
        $oBasket->setPayment('oxidcashondel');

        $oPayCost = $oBasket->UNITcalcPaymentCost(false, false);

        $this->assertEquals(7.5, $oPayCost->getBruttoPrice());
        $this->assertTrue(7.5 > $oPayCost->getNettoPrice());
        $this->assertEquals(19, $oPayCost->getVat());
    }

    /**
     * Testing payment costs calculation
     *
     * @return null
     */
    public function testCalcPaymentCostInNetto()
    {
        $this->getConfig()->setConfigParam('blPaymentVatOnTop', true);

        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        // choosing first payment which is active and has costs
        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 2);
        $oBasket->addToBasket($this->oVariant->getId(), 3);
        $oBasket->calculateBasket(false);
        $oBasket->setPayment('oxidcashondel');

        $oPayCost = $oBasket->UNITcalcPaymentCost(false, false);

        $this->assertEquals(8.93, $oPayCost->getBruttoPrice());
        $this->assertEquals(7.5, $oPayCost->getNettoPrice());
        $this->assertEquals(19, $oPayCost->getVat());
    }

    /**
     * Testing costs setter and getter
     *
     * @return null
     */
    public function testSetCostAndGetCosts()
    {
        $oCost = new stdClass();
        $oCost->xxx = 'yyy';

        $oBasket = new modForTestAddBundles();
        $oBasket->setCost('xxx', $oCost);
        $this->assertEquals(array('xxx' => $oCost), $oBasket->getCosts());
    }

    /**
     * Testing final basket calculator
     *
     * @return null
     */
    public function testCalculateBasket()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $aMethodsToTest = array('isEnabled',
                                '_clearBundles',
                                '_addBundles',
                                '_calcItemsPrice',
                                '_calcBasketDiscount',
                                '_calcBasketTotalDiscount',
                                '_calcVoucherDiscount',
                                '_applyDiscounts',
                                'setCost',
                                '_calcTotalPrice',
                                'formatDiscount',
                                '_calcBasketWrapping',
                                '_save',
                                'afterUpdate',
                                'getSession');
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');
        $oBasket = $this->getMock('oxbasket', $aMethodsToTest);
        $oBasket->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oBasket->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $oBasket->expects($this->once())->method('_save');
        $oBasket->expects($this->once())->method('_clearBundles');
        $oBasket->expects($this->once())->method('_addBundles');
        $oBasket->expects($this->once())->method('_calcItemsPrice');
        $oBasket->expects($this->once())->method('_calcBasketDiscount');
        $oBasket->expects($this->once())->method('_calcBasketTotalDiscount');
        $oBasket->expects($this->once())->method('_calcVoucherDiscount');
        $oBasket->expects($this->once())->method('_applyDiscounts');
        $oBasket->expects($this->exactly(4))->method('setCost');
        $oBasket->expects($this->once())->method('_calcTotalPrice');
        $oBasket->expects($this->once())->method('formatDiscount');
        $oBasket->expects($this->once())->method('afterUpdate');
        $oBasket->expects($this->once())->method('_calcBasketWrapping');

        $oBasket->calculateBasket(false);
    }

    /**
     * Testing final basket calculator
     *
     * @return null
     */
    public function testCalculateBasketReserveBasket()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $aMethodsToTest = array('isEnabled',
                                '_clearBundles',
                                '_addBundles',
                                '_calcItemsPrice',
                                '_calcBasketDiscount',
                                '_calcBasketTotalDiscount',
                                '_calcVoucherDiscount',
                                '_applyDiscounts',
                                'setCost',
                                '_calcTotalPrice',
                                'formatDiscount',
                                '_calcBasketWrapping',
                                '_save',
                                'afterUpdate',
                                'getSession',
                                'deleteBasket');
        $oBasket = $this->getMock('oxbasket', $aMethodsToTest);
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $oBR = $this->getMock('oxBasketReservation', array('reserveBasket', 'getTimeLeft'));
        $oBR->expects($this->once())->method('reserveBasket')->with($this->equalTo($oBasket))->will($this->returnValue(null));
        $oBR->expects($this->never())->method('getTimeLeft');
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->exactly(1))->method('getBasketReservations')->will($this->returnValue($oBR));
        $oBasket->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oBasket->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $oBasket->expects($this->once())->method('_save');
        $oBasket->expects($this->once())->method('_clearBundles');
        $oBasket->expects($this->once())->method('_addBundles');
        $oBasket->expects($this->once())->method('_calcItemsPrice');
        $oBasket->expects($this->once())->method('_calcBasketDiscount');
        $oBasket->expects($this->once())->method('_calcBasketTotalDiscount');
        $oBasket->expects($this->once())->method('_calcVoucherDiscount');
        $oBasket->expects($this->once())->method('_applyDiscounts');
        $oBasket->expects($this->exactly(4))->method('setCost');
        $oBasket->expects($this->once())->method('_calcTotalPrice');
        $oBasket->expects($this->once())->method('formatDiscount');
        $oBasket->expects($this->once())->method('afterUpdate');
        $oBasket->expects($this->once())->method('_calcBasketWrapping');
        $oBasket->expects($this->never())->method('deleteBasket');

        $oBasket->calculateBasket(false);
    }

    /**
     * Testing update status markers onUpdate/afterUpdate
     *
     * @return null
     */
    public function testOnUpdateAndAfterUpdate()
    {
        $oBasket = new modForTestAddBundles();
        $oBasket->onUpdate();
        $this->assertTrue($oBasket->getVar('blUpdateNeeded'));
        $oBasket->afterUpdate();
        $this->assertFalse($oBasket->getVar('blUpdateNeeded'));
    }

    /**
     * Testing how basket summary getter works
     * 1. checks if this method returns empty basket summary object
     *
     * @return null
     */
    public function testGetBasketSummaryDisabledByConfig()
    {
        $oBasket = $this->getMock('oxbasket', array('isEnabled'));
        $oBasket->expects($this->once())->method('isEnabled')->will($this->returnValue(false));

        $oSummary = $oBasket->getBasketSummary();
        $this->assertEquals(array(), $oSummary->aArticles);
        $this->assertEquals(array(), $oSummary->aCategories);
        $this->assertEquals(0, $oSummary->iArticleCount);
        $this->assertEquals(0, $oSummary->dArticlePrice);
    }

    /**
     * Testing how basket summary getter works
     * 2. checks if price loading for article is disabled
     *
     * @return null
     */
    public function testGetBasketSummaryPriceDisabled()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadPrice', false);
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->addToBasket($this->oArticle->getId(), 10);

        $oSummary = $oBasket->getBasketSummary();
        $this->assertEquals(2, count($oSummary->aArticles));
        $this->assertTrue(isset($oSummary->aArticles[$this->oArticle->getId()]));
        $this->assertTrue(isset($oSummary->aArticles[$this->oVariant->getId()]));
        $this->assertEquals(20, $oSummary->iArticleCount);
        $this->assertEquals(0, $oSummary->dArticlePrice);
        $this->assertTrue(isset($oSummary->aCategories[$this->oCategory->getId()]));
    }

    /**
     * Testing how basket summary getter works
     * 3. checks if this method returns filled basket summary
     *
     * @return null
     */
    public function testGetBasketSummaryRawCall()
    {
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->addToBasket($this->oArticle->getId(), 10);

        $oSummary = $oBasket->getBasketSummary();
        $this->assertEquals(2, count($oSummary->aArticles));
        $this->assertTrue(isset($oSummary->aArticles[$this->oArticle->getId()]));
        $this->assertTrue(isset($oSummary->aArticles[$this->oVariant->getId()]));
        $this->assertEquals(20, $oSummary->iArticleCount);
        $this->assertEquals(20 * 19, $oSummary->dArticlePrice);
        $this->assertTrue(isset($oSummary->aCategories[$this->oCategory->getId()]));
    }

    /**
     * #1115: Usability Problem during checkout with products without stock
     *
     * @return null
     */
    public function testGetBasketSummaryIfArtOffline()
    {
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();
        $oSummary = $oBasket->getBasketSummary();
        $this->assertEquals(2, count($oSummary->aArticles));
        $this->assertTrue(isset($oSummary->aArticles[$this->oArticle->getId()]));
        $this->assertTrue(isset($oSummary->aArticles[$this->oVariant->getId()]));
        $this->assertEquals(20, $oSummary->iArticleCount);
        $this->assertEquals(20 * 19, $oSummary->dArticlePrice);
        $this->assertTrue(isset($oSummary->aCategories[$this->oCategory->getId()]));
    }

    /**
     * adding non existing voucher and testing if exeption was thrown
     *
     * @return null
     */
    public function testAddVoucherNonExistingVoucher()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);
        $oBasket->addVoucher('_xxx');
        $this->assertEquals(0, count($oBasket->getVouchers()));
    }

    /**
     * adding existing voucher and testing if it is stored in voucher array
     *
     * @return null
     */
    public function testAddVoucherNormalVoucher()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        $sVoucher = key($this->aVouchers);
        $oVoucher = $this->aVouchers[$sVoucher];

        // deleting discounts
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        $oBasket->addVoucher($sVoucher);

        $aVouchers = $oBasket->getVouchers();

        $this->assertEquals(1, count($aVouchers));
        $this->assertTrue(isset($aVouchers[$oVoucher->oxvouchers__oxid->value]));
    }

    /**
     * test if vouhers availability checking was skipped if skip param is on
     *
     * @return null
     */
    public function testAddVoucherSkipChecking()
    {
        oxAddClassModule('oxVoucherHelper', 'oxvoucher');
        oxVoucherHelper::$blCheckWasPerformed = false;

        $sVoucher = key($this->aVouchers);
        $oVoucher = $this->aVouchers[$sVoucher];

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(999);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);

        $oBasket->setSkipVouchersChecking(true);
        $oBasket->addVoucher($sVoucher);

        $this->assertFalse(oxVoucherHelper::$blCheckWasPerformed);
    }

    /**
     * Testing how voucher removal works
     * removing added voucher
     *
     * @return null
     */
    public function testRemoveVoucher()
    {
        $myDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $sVoucherNr = key($this->aVouchers);
        $sVoucherId = $this->aVouchers[$sVoucherNr]->getId();
        $sSql = "update oxvouchers set oxreserved = '" . time() . "' where oxid = '" . $sVoucherId . "'";
        $myDb->execute($sSql);

        $oBasket = new modForTestAddBundles();
        $oBasket->setVar('aVouchers', array($sVoucherId => 1));

        // testing if voucher is really removed
        $oBasket->removeVoucher($sVoucherId);
        $this->assertEquals(array(), $oBasket->getVouchers());

        $sSql = "select oxreserved from oxvouchers where oxid = '" . $sVoucherId . "'";
        $this->assertEquals(0, $myDb->getOne($sSql));
    }

    /**
     * removing added voucher calls onUpdatet() method
     *
     * @return null
     */
    public function testRemoveVoucherCallsOnUpdateCommand()
    {
        $sVoucherId = '_testVoucherId';

        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate');

        $oBasket->setVar('aVouchers', array($sVoucherId => 1));
        $oBasket->removeVoucher($sVoucherId);
    }

    /**
     * removing not assigned voucher
     *
     * @return null
     */
    public function testRemoveVoucherWithNotAssignedVoucherId()
    {
        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('onUpdate'));
        $oBasket->expects($this->never())->method('onUpdate');

        $oBasket->setVar('aVouchers', array('_xxx' => 1));
        $oBasket->removeVoucher('_zzz');

        $this->assertEquals(array('_xxx' => 1), $oBasket->getVouchers());
    }

    /**
     * Tests if formatting discounts
     *
     * @return null
     */
    public function testFormatDiscount()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        $aTestValues = array('aDiscounts');

        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
            $oDiscount->save();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->setPayment('oxidcashondel');
        $oBasket->setCardId($this->oCard->getId());
        $oBasket->setCardMessage('message');

        $oItem = $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oItem->setWrapping($this->oWrap->getId());


        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        foreach ($aTestValues as $sName) {
            $this->assertTrue(isset($oBasket->{$sName}), " $sName is not set ");
        }
    }

    /**
     * test _save() without user id
     *
     * @return null
     */
    public function testSaveNoUser()
    {
        $oBasket = $this->getMock('oxBasket', array('addToBasket'));
        $oBasket->expects($this->never())->method('addToBasket');
        $oBasket->setBasketUser(false);

        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->UNITsave();
    }

    /**
     * test _save() and if all methods are called.
     *
     * @return null
     */
    public function testLoadCalls()
    {
        $oUserBasketItem = $this->getMock('oxuserbasketitem', array('getPersParams', 'getSelList'));
        $oUserBasketItem->expects($this->once())->method('getSelList');
        $oUserBasketItem->expects($this->once())->method('getPersParams');

        $oUserBasket = $this->getMock('oxuserbasket', array('getItems'));
        $oUserBasket->expects($this->once())->method('getItems')->will($this->returnValue(array($oUserBasketItem)));

        $oUser = $this->getMock('oxuser', array('getBasket'));
        $oUser->expects($this->once())->method('getBasket')->will($this->returnValue($oUserBasket));

        $oBasket = $this->getMock('Unit\Application\Model\modForTestAddBundles', array('getBasketUser', 'addToBasket', '_canSaveBasket'));
        $oBasket->expects($this->once())->method('getBasketUser')->will($this->returnValue($oUser));
        $oBasket->expects($this->once())->method('addToBasket');
        $oBasket->setVar('aBasketContents', array(new oxbasketitem()));

        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->load();
    }

    /**
     * Testing item adding to saved basket
     *
     * @return null
     */
    public function testAddItemToSavedBasket()
    {
        $oUserBasket = $this->getMock('oxUserBasket', array('addItemToBasket'));
        $oUserBasket->expects($this->once())->method('addItemToBasket');

        $oUser = $this->getMock('oxUser', array('getBasket'));
        $oUser->expects($this->once())->method('getBasket')->will($this->returnValue($oUserBasket));

        $oBasket = $this->getMock('oxBasket', array('getBasketUser', 'isSaveToDataBaseEnabled'));
        $oBasket->expects($this->once())->method('getBasketUser')->will($this->returnValue($oUser));
        $oBasket->expects($this->once())->method('isSaveToDataBaseEnabled')->will($this->returnValue(true));

        $oBasket->addToBasket('1127', 10, 'testSel', 'testPersParam');

        $oBasket->UNITsave();
    }

    /**
     * Testing saved basket deletion
     *
     * @return null
     */
    public function testDeleteSavedBasket()
    {
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);

        $oUserBasket = $this->getMock('oxUserBasket', array('delete'));
        $oUserBasket->expects($this->once())->method('delete');

        $oUser = $this->getMock('oxUser', array('getBasket'));
        $oUser->expects($this->once())->method('getBasket')->will($this->returnValue($oUserBasket));

        $oBasket = $this->getMock('oxBasket', array('getBasketUser'));
        $oBasket->expects($this->once())->method('getBasketUser')->will($this->returnValue($oUser));
        $oBasket->UNITdeleteSavedBasket();
    }

    /**
     * Testing how correctly delivery country getter works. No user and no special config - must return null
     *
     * @return null
     */
    public function testFindDelivCountryNoUserAtAll()
    {
        $this->getConfig()->setConfigParam('aHomeCountry', null);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser(false);
        $this->assertNull($oBasket->UNITfindDelivCountry());
    }

    /**
     * no user and special config for home country
     *
     * @return null
     */
    public function test_findDelivCountry_noUserIsHomeCountry()
    {
        $this->getConfig()->setConfigParam('aHomeCountry', array('_xxx'));
        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', true);

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser(false);
        $this->assertEquals('_xxx', $oBasket->UNITfindDelivCountry());
    }

    /**
     * user exists and returns his country ID
     *
     * @return null
     */
    public function testFindDelivCountryAdminUserCountryId()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxBasket');
        $oBasket->setBasketUser($oUser);
        $this->assertEquals($oUser->oxuser__oxcountryid->value, $oBasket->UNITfindDelivCountry());
    }

    /**
     * user exists and returns delcountryid which usually is defined dy some method
     *
     * @return null
     */
    public function testFindDelivCountry_delcountryid()
    {
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);

        $this->getConfig()->setGlobalParameter('delcountryid', '_yyy');
        $this->assertEquals('_yyy', $oBasket->UNITfindDelivCountry());
    }

    /**
     * user exists and returns country ID my user delivery address
     *
     * @return null
     */
    public function testFindDelivCountryDeladrId()
    {
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxbasket');
        $oBasket->setBasketUser($oUser);

        $this->getConfig()->setGlobalParameter('delcountryid', null);
        $this->getSession()->setVariable('deladrid', $this->oDelAdress->getId());
        $this->assertEquals('_xxx', $oBasket->UNITfindDelivCountry());
    }

    /**
     * Testing if basket deletion really destroys session basket
     *
     * @return null
     */
    public function testDeleteBasketNotReserved()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');
        $oBasket = $this->getMock(
            oxTestModules::addFunction('oxbasket', 'iniTestContents', '{$this->_aBasketContents = "asd";}'),
            array('getSession')
        );
        $oBasket->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oBasket->iniTestContents();

        $oBasket->setOrderId('xxx');
        $oBasket->deleteBasket();

        // now loading and testing if its the same
        $oBasket = oxRegistry::getSession()->getBasket();
        $this->assertNull($oBasket->getOrderId());

        $this->assertSame(array(), $oBasket->getContents());
    }

    /**
     * Testing if basket deletion really destroys session basket
     *
     * @return null
     */
    public function testDeleteBasketReserved()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $oBR = $this->getMock('oxBasketReservation', array('discardReservations'));
        $oBR->expects($this->once())->method('discardReservations')->will($this->returnValue(null));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oBasket = $this->getMock('oxbasket', array('getSession'));
        $oBasket->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oBasket->setOrderId('xxx');
        $oBasket->deleteBasket();

        // now loading and testing if its the same
        $oBasket = oxRegistry::getSession()->getBasket();
        $this->assertNull($oBasket->getOrderId());
    }

    /**
     * Testing active payment id getter/setter
     *
     * @return null
     */
    public function testSetPaymentAndGetPaymentId()
    {
        // testing if value is taken from request
        $this->getSession()->setVariable('paymentid', 'xxx');
        $oBasket = oxNew('oxbasket');
        $this->assertEquals('xxx', $oBasket->getPaymentId());

        // testing if value is taken from setter
        $oBasket->setPayment('yyy');
        $this->assertEquals('yyy', $oBasket->getPaymentId());
    }

    /**
     * Testing shipping setter/getter
     *
     * @return null
     */
    public function testSetShippingAndGetShippingId()
    {
        // testing if default value is set
        $oBasket = oxNew('oxbasket');
        $this->assertEquals('oxidstandard', $oBasket->getShippingId());

        // testing if value is taken from request
        $this->getSession()->setVariable('sShipSet', 'xxx');
        $oBasket = oxNew('oxbasket');
        $this->assertEquals('xxx', $oBasket->getShippingId());

        // testing if value is taken from setter
        $oBasket->setShipping('yyy');
        $this->assertEquals('yyy', $oBasket->getShippingId());
    }

    public function testGetShippingIdWhenPaymentIdIsOxEmpty()
    {
        $this->setRequestParameter("sShipSet", null);

        $oBasket = $this->getMock("oxbasket", array("getPaymentId"));
        $oBasket->expects($this->once())->method('getPaymentId')->will($this->returnValue("oxempty"));
        $this->assertNull($oBasket->getShippingId());
    }

    /**
     * selection lists must be NOT set
     *
     * @return null
     */
    public function testGetBasketArticlesSelListsAreOff()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', false);

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $aIds = array($this->oArticle->getId(), $this->oVariant->getId());

        $blSelNotSet = true;

        foreach ($oBasket->getBasketArticles() as $oArticle) {

            // selection list check
            $blSelNotSet = $blSelNotSet & !isset($oArticle->selectlist);

            $this->assertTrue(in_array($oArticle->getId(), $aIds));
        }

        if (!$blSelNotSet) {
            $this->fail('selection list must be NOT set');
        }
    }

    /**
     * Testing discount products price getter
     *
     * @return null
     */
    public function testGetDiscountProductsPrice()
    {
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        $oPrice = $oBasket->getDiscountProductsPrice();
        $this->assertEquals(20 * 19, $oPrice->getBruttoSum());
    }

    /**
     * Testing total products price getter
     *
     * @return null
     */
    public function testGetProductsPrice()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket();

        $oProdPrice = $oBasket->getProductsPrice();
        $this->assertTrue($oProdPrice instanceof pricelist);

        $this->assertEquals(20 * 19, $oProdPrice->getBruttoSum(), 'brutto sum');
        $this->assertEquals(20 * 19 / 1.19, $oProdPrice->getNettoSum(), 'netto sum', 0.01);
        $this->assertEquals(array(19 => 20 * 19 - 20 * 19 / 1.19), $oProdPrice->getVatInfo(false), 'get vat info');
        $this->assertEquals(array(19 => 20 * 19), $oProdPrice->getPriceInfo(), 'get price info');
        $this->assertEquals(19, $oProdPrice->getMostUsedVatPercent());
    }

    /**
     * Testing total products price getter
     *
     * @return null
     */
    public function testGetProductsPriceIfNotSet()
    {
        $oBasket = oxNew('oxbasket');
        $oProdPrice = $oBasket->getProductsPrice();
        $this->assertTrue($oProdPrice instanceof pricelist);
    }

    /**
     * Testing total basket price getter
     *
     * @return null
     */
    public function testGetPrice()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        $oPrice = $oBasket->getPrice();
        $this->assertTrue($oPrice instanceof price);
        $this->assertEquals(0, $oPrice->getVat());
        $this->assertEquals(19 * 20, $oPrice->getBruttoPrice());
        $this->assertEquals(19 * 20, $oPrice->getNettoPrice());
        $this->assertEquals(0, $oPrice->getVatValue());
    }

    /**
     * Testing total basket price getter
     *
     * @return null
     */
    public function testGetPriceIfNotSet()
    {
        $oBasket = oxNew('oxbasket');
        $oPrice = $oBasket->getPrice();
        $this->assertTrue($oPrice instanceof price);
    }

    /**
     * Testing order id setter/getter
     *
     * @return null
     */
    public function testSetOrderIdAndGetOrderId()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->setOrderId('xxx');
        $this->assertEquals('xxx', $oBasket->getOrderId());
    }

    /**
     * Testing if costs getter returns all default costs
     *
     * @return null
     */
    public function testGetCosts()
    {
        $this->getConfig()->setConfigParam('blEnterNetPrice', true);
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 2);
        $oBasket->addToBasket($this->oVariant->getId(), 11);
        $oBasket->calculateBasket(false);
        $this->assertEquals(array('oxdelivery', 'oxwrapping', 'oxgiftcard', 'oxpayment'), array_keys($oBasket->getCosts()));
    }

    /**
     * Testing voucher info getter
     *
     * @return null
     */
    public function testGetVouchers()
    {
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        foreach ($this->aVouchers as $oVoucher) {
            $oBasket->addVoucher($oVoucher->oxvouchers__oxvouchernr->value);
        }

        $aVouchers = $oBasket->getVouchers();

        // testing if they are the same
        $this->assertEquals(count($this->aVouchers), count($aVouchers));
        foreach ($aVouchers as $oStdVoucher) {
            $this->assertTrue(isset($this->aVouchers[$oStdVoucher->sVoucherNr]));
        }
    }

    /**
     * Testing basket products count getter
     *
     * @return null
     */
    public function testGetProductsCount()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 2);
        $oBasket->addToBasket($this->oVariant->getId(), 11);
        $oBasket->calculateBasket(false);
        $this->assertEquals(2, $oBasket->getProductsCount());
    }

    /**
     * Testing item count getter
     *
     * @return null
     */
    public function testGetItemsCount()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 7);
        $oBasket->addToBasket($this->oVariant->getId(), 6);
        $oBasket->calculateBasket(false);
        $this->assertEquals(13, $oBasket->getItemsCount());
    }

    /**
     * Testing basket total weight getter
     *
     * @return null
     */
    public function testGetWeight()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);
        $this->assertEquals(200, $oBasket->getWeight());
    }

    /**
     * Testing basket items array getter
     *
     * @return null
     */
    public function testGetContents()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $aIds = array($this->oArticle->getId(), $this->oVariant->getId());

        // testing
        foreach ($oBasket->getContents() as $oBasketItem) {
            $this->assertTrue(in_array($oBasketItem->getProductId(), $aIds));
        }
    }

    /**
     * Testing products VAT getter
     *
     * @return null
     */
    public function testGetProductVats()
    {
        // deleting discounts to ignore bundle problems
        foreach ($this->aDiscounts as $oDiscount) {
            $oDiscount->delete();
        }

        // setting custom VAT for variant
        $this->oVariant->oxarticles__oxvat = new oxField(9, oxField::T_RAW);
        $this->oVariant->save();

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        $aTestVats = array(19 => oxRegistry::getLang()->formatCurrency((10 * 19 - 10 * 19 / 1.19)),
                           9  => oxRegistry::getLang()->formatCurrency((10 * 19 - 10 * 19 / 1.09))
        );

        $this->assertEquals($aTestVats, $oBasket->getProductVats());
    }

    /**
     * Testing products VAT getter
     *
     * @return null
     */
    public function testGetProductVatsIfPriceNotSet()
    {
        $oBasket = oxNew('oxbasket');
        $this->assertEquals(0, count($oBasket->getProductVats()));
    }

    /**
     * Testing gift card message getter/setter
     *
     * @return null
     */
    public function testSetCardMessageAndGetCardMessage()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->setCardMessage('xxx');
        $this->assertEquals('xxx', $oBasket->getCardMessage());
    }

    /**
     * Testing gift card setter and getter
     *
     * @return null
     */
    public function testGetCard()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->setCardId($this->oCard->getId());

        // testing Id getter
        $this->assertEquals($this->oCard->getId(), $oBasket->getCardId());

        // testing card getter
        $oCard = $oBasket->getCard();
        $this->assertEquals($this->oCard->getId(), $oCard->getId());
        $this->assertTrue($oCard instanceof wrapping);
    }

    /**
     * Testing discounts getter
     *
     * @return null
     */
    public function testGetDiscounts()
    {
        $oDiscount1 = new stdClass();
        $oDiscount1->dDiscount = 5;

        $aDiscounts[] = $oDiscount1;
        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 10;

        $aDiscounts[] = $oDiscount2;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aItemDiscounts', array($oDiscount1));
        $oBasket->setNonPublicVar('_aDiscounts', array($oDiscount2));
        $oBasket->UNITcalcBasketTotalDiscount();

        $this->assertEquals($aDiscounts, $oBasket->getDiscounts());
    }

    /**
     * Testing discounts getter
     *
     * @return null
     */
    public function testGetDiscountsIfZeroDiscount()
    {
        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 0;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aItemDiscounts', array());
        $oBasket->setNonPublicVar('_aDiscounts', array($oDiscount2));
        $oBasket->UNITcalcBasketTotalDiscount();

        $this->assertNull($oBasket->getDiscounts());
    }

    /**
     * Testing discounts getter
     *
     * @return null
     */
    public function testGetDiscountsIfItemDiscount()
    {
        $oDiscount1 = new stdClass();
        $oDiscount1->dDiscount = 1;

        $aDiscounts[] = $oDiscount1;
        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 0;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aItemDiscounts', array($oDiscount1));
        $oBasket->setNonPublicVar('_aDiscounts', array());
        $oBasket->UNITcalcBasketTotalDiscount();

        $this->assertEquals($aDiscounts, $oBasket->getDiscounts());
    }

    /**
     * Testing voucher discount getter
     *
     * @return null
     */
    public function testGetVoucherDiscount()
    {
        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        foreach ($this->aVouchers as $oVoucher) {
            $oBasket->addVoucher($oVoucher->oxvouchers__oxvouchernr->value);
        }

        $oBasket->calculateBasket(false);

        $oPrice = $oBasket->getVoucherDiscount();
        $this->assertEquals(40, $oPrice->getBruttoPrice());
    }

    /**
     * Testing voucher discount getter - when voucher is value is percent.
     * Voucher discount value should be calculated after applying general discount
     *
     * @return null
     */
    public function testGetVoucherDiscountWithPercentageVoucher()
    {
        $oDiscount = reset($this->aDiscounts);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(10, oxField::T_RAW);
        $oDiscount->save();

        $this->oVoucherSerie->oxvoucherseries__oxdiscounttype = new oxField('percent', oxField::T_RAW);
        $this->oVoucherSerie->save();

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($this->oArticle->getId(), 10);
        $oBasket->addToBasket($this->oVariant->getId(), 10);
        $oBasket->calculateBasket(false);

        $oVoucher = reset($this->aVouchers);
        $oBasket->addVoucher($oVoucher->oxvouchers__oxvouchernr->value); // 10 %

        $oBasket->calculateBasket(false);

        // basket price 380, total discount 10% (38), so voucher discount = (380 - 38) * 10% = 34.2
        $oPrice = $oBasket->getVoucherDiscount();
        $this->assertEquals(34.2, $oPrice->getBruttoPrice());
    }

    /**
     * Testing basket currency setter and getter
     *
     * @return null
     */
    public function testSetAndGetBasketCurrency()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $oCur = new stdClass();
        $oCur->name = 'testCurrencyName';
        $oCur->desc = 'testDescription';

        $oBasket->setBasketCurrency($oCur);;

        $this->assertEquals($oCur, $oBasket->getBasketCurrency());
    }

    /**
     * Testing basket currency getter by default return active shop currency object
     *
     * @return null
     */
    public function testGetBasketCurrencyByDefaultReturnsActiveShopCurrencyObject()
    {
        $oBasket = $this->getProxyClass("oxBasket");

        $oCur = $this->getConfig()->getActShopCurrencyObject();
        $this->assertEquals($oCur, $oBasket->getBasketCurrency());
    }

    /**
     * Testing setter for skipping vouchers availability checking
     *
     * @return null
     */
    public function testSetSkipVouchersChecking()
    {
        $oBasket = $this->getProxyClass("oxBasket");

        $oBasket->setSkipVouchersChecking(true);
        $this->assertTrue($oBasket->getNonPublicVar('_blSkipVouchersAvailabilityChecking'));

        $oBasket->setSkipVouchersChecking(false);
        $this->assertFalse($oBasket->getNonPublicVar('_blSkipVouchersAvailabilityChecking'));
    }

    /**
     * Testing basket discount calculation
     *
     * @return null
     */
    public function testCalcBasketDiscount()
    {
        $oDiscount2 = oxNew("oxDiscount");
        $oDiscount2->setId('_testDiscountId2');
        $oDiscount2->oxdiscount__oxtitle = new oxField('Test discount title 2', oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsumtype = new oxField("%", oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsum = new oxField(15, oxField::T_RAW);

        $aDiscounts[] = $oDiscount2;

        $oDiscountList = $this->getMock('oxDiscountList', array('getBasketDiscounts'));
        $oDiscountList->expects($this->any())->method("getBasketDiscounts")->will($this->returnValue($aDiscounts));

        oxTestModules::addModuleObject('oxDiscountList', $oDiscountList);


        $oBasket = $this->getProxyClass("oxBasket");

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(20);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $aDiscounts = $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $oBasket->UNITcalcBasketDiscount();

        $aDiscounts = $oBasket->getNonPublicVar('_aDiscounts');

        $this->assertEquals(1, count($aDiscounts));

        //asserting second discount values
        $this->assertEquals('Test discount title 2', $aDiscounts['_testDiscountId2']->sDiscount);
        //checking 15 % discount (after first discount discountable items price = 20)
        $this->assertEquals(3, $aDiscounts['_testDiscountId2']->dDiscount);
    }

    /**
     * Testing basket discount calculation FS#1675
     *
     * @return null
     */
    public function testCalcBasketDiscountWithSpecialPrice()
    {
        $oDiscount2 = oxNew("oxDiscount");
        $oDiscount2->setId('_testDiscountId2');
        $oDiscount2->oxdiscount__oxtitle = new oxField('Test discount title 2', oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsumtype = new oxField("%", oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsum = new oxField(15, oxField::T_RAW);

        $aDiscounts[] = $oDiscount2;

        $oDiscountList = $this->getMock('oxDiscountList', array('getBasketDiscounts'));
        $oDiscountList->expects($this->any())->method("getBasketDiscounts")->will($this->returnValue($aDiscounts));

        oxTestModules::addModuleObject('oxDiscountList', $oDiscountList);

        $oBasket = $this->getProxyClass("oxBasket");

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(79.5);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $aDiscounts = $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $oBasket->UNITcalcBasketDiscount();

        $aDiscounts = $oBasket->getNonPublicVar('_aDiscounts');

        $this->assertEquals(1, count($aDiscounts));

        //asserting first discount values
        $this->assertEquals('Test discount title 2', $aDiscounts['_testDiscountId2']->sDiscount);
        //checking 15 % discount (discountable items price = 79.5)
        $this->assertEquals(11.925, $aDiscounts['_testDiscountId2']->dDiscount, '', 0.0001);
    }

    /**
     * Testing basket discount calculation when no discounts exists
     *
     * @return null
     */
    public function testCalcBasketDiscountWithNoDiscounts()
    {
        $oBasket = $this->getProxyClass("oxBasket");

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(20);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $aDiscounts = $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $oBasket->UNITcalcBasketDiscount();

        $aDiscounts = $oBasket->getNonPublicVar('_aDiscounts');

        $this->assertEquals(0, count($aDiscounts));
    }

    /**
     * Testing total basket discount calculation
     *
     * @return null
     */
    public function testCalcBasketTotalDiscount()
    {
        $oDiscount1 = new stdClass();
        $oDiscount1->dDiscount = 5;

        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 7;

        $oDiscount3 = new stdClass();
        $oDiscount3->dDiscount = 7;
        $oDiscount3->sType = 'itm';

        $aDiscounts[] = $oDiscount1;
        $aDiscounts[] = $oDiscount2;
        $aDiscounts[] = $oDiscount3;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aDiscounts', $aDiscounts);
        $oBasket->UNITcalcBasketTotalDiscount();

        $oTotalDiscount = $oBasket->getNonPublicVar('_oTotalDiscount');

        $this->assertEquals(12, $oTotalDiscount->getBruttoPrice());
    }

    /**
     * M#884 Testing total basket discount calculation before and after discount list update
     *
     * @return null
     */
    public function testCalcBasketTotalDiscountAfterUpdate()
    {
        $oDiscount1 = new stdClass();
        $oDiscount1->dDiscount = 5;

        $oDiscount2 = new stdClass();
        $oDiscount2->dDiscount = 7;

        $aDiscounts[] = $oDiscount1;
        $aDiscounts[] = $oDiscount2;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aDiscounts', $aDiscounts);
        $oBasket->UNITcalcBasketTotalDiscount();

        $oTotalDiscount = $oBasket->getNonPublicVar('_oTotalDiscount');

        $this->assertEquals(12, $oTotalDiscount->getBruttoPrice());

        // remove one discount, and calculate again
        unset($aDiscounts[0]);

        $oBasket->setNonPublicVar('_aDiscounts', $aDiscounts);
        $oBasket->UNITcalcBasketTotalDiscount();

        $oTotalDiscount = $oBasket->getNonPublicVar('_oTotalDiscount');

        $this->assertEquals(7, $oTotalDiscount->getBruttoPrice());
    }

    /**
     * M#884 Testing skipp of total basket discount calculation in Admin, after discount list was updated
     *
     * @return null
     */
    public function testCalcBasketTotalDiscountAfterUpdateInAdminMode()
    {
        $oDiscount = new stdClass();
        $oDiscount->dDiscount = 50;

        $aDiscounts[] = $oDiscount;

        $oTotalDiscount = new oxPrice(100);

        $oBasket = $this->getMock($this->getProxyClassName('oxBasket'), array('isAdmin'));
        $oBasket->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oBasket->setTotalDiscount(100);
        $oBasket->setNonPublicVar('_aDiscounts', $aDiscounts);
        $oBasket->UNITcalcBasketTotalDiscount();

        $oTotalDiscount = $oBasket->getNonPublicVar('_oTotalDiscount');

        $this->assertEquals(100, $oTotalDiscount->getBruttoPrice());
    }

    /**
     * Testing total basket discount calculation with no discount
     *
     * @return null
     */
    public function testCalcBasketTotalDiscountWithNoDiscounts()
    {
        $aDiscounts = null;

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aDiscounts', $aDiscounts);
        $oBasket->UNITcalcBasketTotalDiscount();

        $oTotalDiscount = $oBasket->getNonPublicVar('_oTotalDiscount');

        $this->assertEquals(0, $oTotalDiscount->getBruttoPrice());
    }

    /**
     * Testing if method _changeBasketItemKey() was called in addToBasket()
     *
     * @return null
     */
    public function testIfChangeBasketItemKeyCalledInAddToBasket()
    {
        $oBasket = $this->getMock('oxbasket', array('_changeBasketItemKey'));
        $oBasket->expects($this->never())->method('_changeBasketItemKey');
        $oBasket->addToBasket($this->oArticle->getId(), 1, null, null, true, false);
        $oBasket->addToBasket($this->oArticle->getId(), 2, null, null, true, false);
        $oBasket = $this->getMock('oxbasket', array('_changeBasketItemKey'));
        $oBasket->expects($this->once())->method('_changeBasketItemKey');
        $oBasket->addToBasket($this->oArticle->getId(), 1, null, null, true, false);
        $oBasket->addToBasket($this->oArticle->getId(), 1, null, null, true, false, $this->oArticle->getId());
        try {
            $oBasket->addToBasket('ra', 1, null, null, true, false, $this->oArticle->getId());
        } catch (oxNoArticleException $e) { //whatever.. we interested only before this func.
        }
    }

    /**
     * Testing method _changeBasketItemKey()
     *
     * @return null
     */
    public function testChangeBasketItemKey()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $arr1 = array('a' => 1, 'b' => 4, 'g' => 's', 'ds' => 'aaa');
        $arr2 = array('a' => 1, 'c' => 222, 'g' => 's', 'ds' => 'aaa');
        $oBasket->setNonPublicVar('_aBasketContents', $arr1);
        $oBasket->UNITchangeBasketItemKey('b', 'c', 222);
        $this->assertEquals($arr2, $oBasket->getNonPublicVar('_aBasketContents'));
        $arr2 = array('a' => 1, 'c' => 222, 'g' => 's', 'dsa' => null);
        $oBasket->UNITchangeBasketItemKey('ds', 'dsa');
        $this->assertEquals($arr2, $oBasket->getNonPublicVar('_aBasketContents'));
    }

    /**
     * Testing if reset functionality executes all dep. methods
     *
     * @return null
     */
    public function testResetUserInfo()
    {
        $oBasket = $this->getMock('oxbasket', array('setPayment', 'setShipping'));
        $oBasket->expects($this->once())->method('setPayment')->with($this->equalTo(null));
        $oBasket->expects($this->once())->method('setShipping')->with($this->equalTo(null));
        $oBasket->resetUserInfo();
    }

    /**
     * Testing skip discounts marker setter/getter
     *
     * @return null
     */
    public function testSetGetSkipDiscounts()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setSkipDiscounts(true);
        $this->assertTrue($oBasket->hasSkipedDiscount());
    }

    /**
     * Testing formatted products price getter
     *
     * @return null
     */
    public function testGetFProductsPriceIfPriceNotSet()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $this->assertEquals('0,00', $oBasket->getFProductsPrice());
    }

    /**
     * Testing delivery cost Vat getter
     *
     * @return null
     */
    public function testGetDelCostVatPercent()
    {
        $oPrice = $this->getMock('oxprice', array('getVat'));
        $oPrice->expects($this->once())->method('getVat')->will($this->returnValue(19));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertEquals(19, $oBasket->getDelCostVatPercent());
    }

    /**
     * Testing formatted delivery vat value getter
     *
     * @return null
     */
    public function testGetDelCostVat()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', true);
        $oPrice = $this->getMock('oxprice', array('getVatValue'));
        $oPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertEquals("11,59", $oBasket->getDelCostVat());
    }

    /**
     * Testing formatted delivery vat value getter
     *
     * @return null
     */
    public function testGetDelCostVatDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', false);
        $oPrice = $this->getMock('oxprice', array('getVatValue'));
        $oPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertFalse($oBasket->getDelCostVat());
    }

    /**
     * Testing formatted delivery netto price getter
     *
     * @return null
     */
    public function testGetDelCostNet()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', true);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->once())->method('getNettoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $oBasket->setNonPublicVar('_oUser', true);
        $this->assertEquals("11,59", $oBasket->getDelCostNet());
    }

    /**
     * Testing formatted delivery netto price getter
     *
     * @return null
     */
    public function testGetDelCostNetDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', false);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->never())->method('getNettoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $oBasket->setNonPublicVar('_oUser', true);
        $this->assertFalse($oBasket->getDelCostNet());
    }

    /**
     * Testing formatted delivery netto price getter
     *
     * @return null
     */
    public function testGetDelCostNetWithoutUser()
    {
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->any())->method('getNettoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $oBasket->setNonPublicVar('_oUser', false);

        $this->assertFalse($oBasket->getDelCostNet());
    }

    /**
     * Testing formatted delivery netto price getter
     *
     * @return null
     */
    public function testGetDelCostNetCalculateWithoutUser()
    {
        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', true);
        $this->getConfig()->setConfigParam('blShowVATForDelivery', true);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->once())->method('getNettoPrice')->will($this->returnValue(0));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $oBasket->setNonPublicVar('_oUser', false);
        $this->assertFalse($oBasket->getDelCostNet());
    }

    /**
     * Testing payment cost Vat getter
     *
     * @return null
     */
    public function testGetPayCostVatPercent()
    {
        $oPrice = $this->getMock('oxprice', array('getVat'));
        $oPrice->expects($this->once())->method('getVat')->will($this->returnValue(19));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertEquals(19, $oBasket->getPayCostVatPercent());
    }

    /**
     * Testing formatted payment vat value getter
     *
     * @return null
     */
    public function testGetPayCostVat()
    {
        $this->getConfig()->setConfigParam('blShowVATForPayCharge', true);
        $oPrice = $this->getMock('oxprice', array('getVatValue'));
        $oPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertEquals("11,59", $oBasket->getPayCostVat());
    }

    /**
     * Testing formatted payment vat value getter
     *
     * @return null
     */
    public function testGetPayCostVatDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForPayCharge', false);
        $oPrice = $this->getMock('oxprice', array('getVatValue'));
        $oPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertFalse($oBasket->getPayCostVat());
    }

    /**
     * Testing formatted payment netto price getter
     *
     * @return null
     */
    public function testGetPayCostNet()
    {
        $this->getConfig()->setConfigParam('blShowVATForPayCharge', true);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->any())->method('getNettoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertEquals("11,59", $oBasket->getPayCostNet());
    }

    /**
     * Testing formatted payment netto price getter
     *
     * @return null
     */
    public function testGetPayCostNetDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForPayCharge', false);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->never())->method('getNettoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertFalse($oBasket->getPayCostNet());
    }

    /**
     * Testing payment brutto price getter
     *
     * @return null
     */
    public function testGetPaymentCosts()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxpayment" => $oPrice));
        $this->assertEquals(11.588, $oBasket->getPaymentCosts());
    }

    /**
     * Testing voucher discount getter
     *
     * @return null
     */
    public function testGetVoucherDiscValue()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oVoucherDiscount', $oPrice);
        $this->assertEquals(11.588, $oBasket->getVoucherDiscValue());
    }

    /**
     * Testing voucher discount getter
     *
     * @return null
     */
    public function testGetVoucherDiscValueIfNotSet()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $this->assertFalse($oBasket->getVoucherDiscValue());
    }

    /**
     * Testing wrapping cost Vat getter
     *
     * @return null
     */
    public function testGetWrappCostVatPercent()
    {
        $oPrice = $this->getMock('oxprice', array('getVat'));
        $oPrice->expects($this->once())->method('getVat')->will($this->returnValue(19));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertEquals(19, $oBasket->getWrappCostVatPercent());
    }

    /**
     * Testing formatted wrapping vat value getter
     *
     * @return null
     */
    public function testGetWrappCostVat()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', true);
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(5, 19);
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertEquals("0,80", $oBasket->getWrappCostVat());
    }

    /**
     * Testing formatted wrapping vat value getter - vat = 0
     *
     * @return null
     */
    public function testGetWrappCostVat_priceIsZero()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', true);
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(0, 0);
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertFalse($oBasket->getWrappCostVat());
    }

    /**
     * Testing formatted wrapping vat value getter
     *
     * @return null
     */
    public function testGetWrappCostVatDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', false);
        $oPrice = $this->getMock('oxprice', array('getVatValue'));
        $oPrice->expects($this->never())->method('getVatValue');
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertFalse($oBasket->getWrappCostVat());
    }

    /**
     * Testing formatted wrapping netto price getter
     *
     * @return null
     */
    public function testGetWrappCostNet()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', true);
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(5, 19);
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertEquals("4,20", $oBasket->getWrappCostNet());
    }

    /**
     * Testing formatted wrapping netto price getter - price is zero
     *
     * @return null
     */
    public function testGetWrappCostNet_priceIsZero()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', true);
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(0, 0);
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertFalse($oBasket->getWrappCostNet());
    }

    /**
     * Testing formatted wrapping netto price getter
     *
     * @return null
     */
    public function testGetWrappCostNetDoNotShow()
    {
        $this->getConfig()->setConfigParam('blShowVATForWrapping', false);
        $oPrice = $this->getMock('oxprice', array('getNettoPrice'));
        $oPrice->expects($this->never())->method('getNettoPrice');
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxwrapping" => $oPrice));
        $this->assertFalse($oBasket->getWrappCostNet());
    }

    /**
     * Testing formatted basket total price
     *
     * @return null
     */
    public function testGetFPrice()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oPrice', $oPrice);
        $this->assertEquals("11,59", $oBasket->getFPrice());
    }

    /**
     * Testing formatted delivery price getter
     *
     * @return null
     */
    public function testGetFDeliveryCosts()
    {
        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', true);
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertEquals("11,59", $oBasket->getFDeliveryCosts());
    }

    /**
     * Testing formatted delivery price getter
     *
     * @return null
     */
    public function testGetFDeliveryCostsSetToZero()
    {
        $this->getConfig()->setConfigParam('blCalculateDelCostIfNotLoggedIn', true);
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(0));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertEquals("0,00", $oBasket->getFDeliveryCosts());
    }

    /**
     * Testing formatted delivery price getter
     *
     * @return null
     */
    public function testGetFDeliveryCostsIfNotSet()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $this->assertFalse($oBasket->getFDeliveryCosts());
    }

    /**
     * Testing delivery price getter
     *
     * @return null
     */
    public function testGetDeliveryCosts()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aCosts', array("oxdelivery" => $oPrice));
        $this->assertEquals(11.588, $oBasket->getDeliveryCosts());
    }

    public function testGetDeliveryCostsIfNotSet()
    {
        $oBasket = $this->getProxyClass("oxBasket");
        $this->assertFalse($oBasket->getDeliveryCosts());
    }

    /**
     * Testing total discount getter
     *
     * @return null
     */
    public function testGetTotalDiscount()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_oTotalDiscount', $oPrice);
        $this->assertEquals(11.588, $oBasket->getTotalDiscount()->getBruttoPrice());
    }

    /**
     * Testing getting basket price for payment costs calculations
     * (M:1190, M:1145)
     *
     * @return null
     */
    public function testGetPriceForPayment()
    {
        $oProductsPrice = $this->getMock('oxPriceList', array('getBruttoSum'));
        $oProductsPrice->expects($this->once())->method('getBruttoSum')->will($this->returnValue(100));

        $oVoucher = $this->getMock('oxPrice', array('getBruttoPrice'));
        $oVoucher->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(40));

        $oBasket = $this->getMock('oxBasket', array('getDiscountProductsPrice', 'getVoucherDiscount'));
        $oBasket->expects($this->once())->method('getDiscountProductsPrice')->will($this->returnValue($oProductsPrice));
        $oBasket->expects($this->once())->method('getVoucherDiscount')->will($this->returnValue($oVoucher));

        $oBasket->setCost('oxpayment', new oxPrice(30));
        $oBasket->setCost('oxdelivery', new oxPrice(25));

        //final price  = products price - voucher + delivery cost (100 - 40 + 25)
        //payment costs should not be included
        $this->assertEquals(85, $oBasket->getPriceForPayment());
    }

    /**
     * Testing getting basket price for payment costs calculations
     * (M:1905) not discounted products should be included in payment
     * amount calculation
     *
     * @return null
     */
    public function testGetPriceForPaymentIfWithNotDiskcountedArticles()
    {
        $oProductsPrice = $this->getMock('oxPriceList', array('getBruttoSum'));
        $oProductsPrice->expects($this->any())->method('getBruttoSum')->will($this->returnValue(100));

        $oVoucher = $this->getMock('oxPrice', array('getBruttoPrice'));
        $oVoucher->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(40));

        $oBasket = $this->getMock('oxBasket', array('getDiscountProductsPrice', 'getVoucherDiscount', 'getNotDiscountProductsPrice'));
        $oBasket->expects($this->once())->method('getDiscountProductsPrice')->will($this->returnValue($oProductsPrice));
        $oBasket->expects($this->once())->method('getVoucherDiscount')->will($this->returnValue($oVoucher));
        $oBasket->expects($this->once())->method('getNotDiscountProductsPrice')->will($this->returnValue($oProductsPrice));

        $oBasket->setCost('oxpayment', new oxPrice(30));
        $oBasket->setCost('oxdelivery', new oxPrice(25));

        //final price  = products price - voucher + delivery cost (100 - 40 + 25 + 100)
        //payment costs should not be included
        $this->assertEquals(185, $oBasket->getPriceForPayment());
    }

    /**
     * Testing getting formatted payment cost
     *
     * @return null
     */
    public function testGetFPaymentCosts()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(10.992));

        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue($oPrice));

        $this->assertEquals("10,99", $oBasket->getFPaymentCosts());
    }

    /**
     * Testing getting formatted payment cost when cost is not setted
     *
     * @return null
     */
    public function testGetFPaymentCosts_noCost()
    {
        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue(false));

        $this->assertFalse($oBasket->getFPaymentCosts());
    }

    /**
     * Testing getting formatted payment cost when cost = 0
     *
     * @return null
     */
    public function testGetFPaymentCosts_zeroValue()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(0));

        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue($oPrice));

        $this->assertFalse($oBasket->getFPaymentCosts());
    }

    /**
     * Testing getting formatted wrapping cost
     *
     * @return null
     */
    public function testGetFWrappingCosts()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(10.992));

        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue($oPrice));

        $this->assertEquals("10,99", $oBasket->getFWrappingCosts());
    }

    /**
     * Testing getting formatted wrapping cost when cost is not setted
     *
     * @return null
     */
    public function testGetFWrappingCosts_noCost()
    {
        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue(false));

        $this->assertFalse($oBasket->getFWrappingCosts());
    }

    /**
     * Testing getting formatted wrapping cost when cost = 0
     *
     * @return null
     */
    public function testGetFWrappingCosts_zeroValue()
    {
        $oPrice = $this->getMock('oxprice', array('getBruttoPrice'));
        $oPrice->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(0));

        $oBasket = $this->getMock('oxbasket', array('getCosts'));
        $oBasket->expects($this->once())->method('getCosts')->will($this->returnValue($oPrice));

        $this->assertFalse($oBasket->getFWrappingCosts());
    }

    /**
     * Testing getting formatted wrapping cost
     *
     * @return null
     */
    public function testGetArtStockInBasket()
    {
        // simulating basket contents
        $oBasketItem = oxNew('oxbasketitem');
        $oBasketItem->init($this->oArticle->getId(), 1, null, null, true);
        $oBasketItem2 = oxNew('oxbasketitem');
        $oBasketItem2->init($this->oArticle->getId(), 2);

        $oBasket = $this->getProxyClass("oxBasket");
        $oBasket->setNonPublicVar('_aBasketContents', array("_testItem" => $oBasketItem, "_testItem2" => $oBasketItem2));

        $this->assertEquals(3, $oBasket->getArtStockInBasket($this->oArticle->getId()));
        $this->assertEquals(1, $oBasket->getArtStockInBasket($this->oArticle->getId(), "_testItem2"));
    }

    /**
     * Testing calcBasketDiscount() and checking if minimize discount if it bigger than total price.
     * #1818
     *
     * @return null
     */
    public function testCalcBasketDiscountMinimizeDiscountIfBiggerThanTotal()
    {

        $oDiscount2 = oxNew("oxDiscount");
        $oDiscount2->setId('_testDiscountId2');
        $oDiscount2->oxdiscount__oxtitle = new oxField('Test discount title 123', oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsumtype = new oxField("abs", oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsum = new oxField(150, oxField::T_RAW);

        $aDiscounts[] = $oDiscount2;

        $oDiscountList = $this->getMock('oxDiscountList', array('getBasketDiscounts'));
        $oDiscountList->expects($this->once())->method('getBasketDiscounts')->will($this->returnValue($aDiscounts));
        oxTestModules::addModuleObject('oxDiscountList', $oDiscountList);

        $oBasket = $this->getProxyClass("oxBasket");

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(20);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $aDiscounts = $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $oBasket->UNITcalcBasketDiscount();

        $aDiscounts = $oBasket->getNonPublicVar('_aDiscounts');

        $this->assertEquals(1, count($aDiscounts));

        //asserting second discount values
        $this->assertEquals('Test discount title 123', $aDiscounts['_testDiscountId2']->sDiscount);
        $this->assertEquals(20, $aDiscounts['_testDiscountId2']->dDiscount);
    }

    /**
     * Testing calcBasketDiscount() and checking if discount is minus (-10)
     * #1934
     *
     * @return null
     */
    public function testCalcBasketDiscountIfDiscountIsMinus()
    {

        $oDiscount2 = oxNew("oxDiscount");
        $oDiscount2->setId('_testDiscountId2');
        $oDiscount2->oxdiscount__oxtitle = new oxField('Test discount title 123', oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsumtype = new oxField("abs", oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsum = new oxField(-10, oxField::T_RAW);

        $aDiscounts[] = $oDiscount2;

        $oDiscountList = $this->getMock('oxDiscountList', array('getBasketDiscounts'));
        $oDiscountList->expects($this->once())->method('getBasketDiscounts')->will($this->returnValue($aDiscounts));
        oxTestModules::addModuleObject('oxDiscountList', $oDiscountList);

        $oBasket = $this->getProxyClass("oxBasket");

        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(20);
        $oPriceList = oxNew("oxPriceList");
        $oPriceList->addToPriceList($oPrice);

        $aDiscounts = $oBasket->setNonPublicVar('_oDiscountProductsPriceList', $oPriceList);
        $oBasket->UNITcalcBasketDiscount();

        $aDiscounts = $oBasket->getNonPublicVar('_aDiscounts');

        $this->assertEquals(1, count($aDiscounts));

        //asserting second discount values
        $this->assertEquals('Test discount title 123', $aDiscounts['_testDiscountId2']->sDiscount);
        $this->assertEquals(-10, $aDiscounts['_testDiscountId2']->dDiscount);
    }

    /**
     * Check if method isBelowMinOrderPrice() works correctly
     *
     * @return null
     */
    public function testIsBelowMinOrderPriceRecognise0AsValue()
    {
        $this->getConfig()->setConfigParam("iMinOrderPrice", 0);

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->once())->method('getDiscountedProductsBruttoPrice')->will($this->returnValue(-1));

        $this->assertTrue($oBasket->isBelowMinOrderPrice());


        $this->getConfig()->setConfigParam("iMinOrderPrice", '');

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice"));
        $oBasket->expects($this->never())->method('getProductsCount');
        $oBasket->expects($this->never())->method('getDiscountedProductsBruttoPrice');

        $this->assertFalse($oBasket->isBelowMinOrderPrice());
    }

    /**
     * Check if method isBelowMinOrderPrice() works correctly
     *
     * @return null
     */
    public function testIsBelowMinOrderPriceAddNotDiscountedProducts()
    {
        $this->getConfig()->setConfigParam("iMinOrderPrice", 2);

        $oPrice = $this->getMock("oxprice", array("getBruttoSum"));
        $oPrice->expects($this->once())->method('getBruttoSum')->will($this->returnValue(2));

        $oBasket = $this->getMock("oxbasket", array("getProductsCount", "getDiscountedProductsBruttoPrice", "getNotDiscountProductsPrice"));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(2));
        $oBasket->expects($this->once())->method('getDiscountedProductsBruttoPrice')->will($this->returnValue(1));
        $oBasket->expects($this->once())->method('getNotDiscountProductsPrice')->will($this->returnValue($oPrice));

        $this->assertFalse($oBasket->isBelowMinOrderPrice());
    }

    /**
     * oxbasket::showCatChangeWarning() & oxbasket::setCatChangeWarningState() test case
     *
     * @return null
     */
    public function testScSetCatChangeWarningState()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oBasket = oxNew('oxBasket');
        $this->assertFalse($oBasket->showCatChangeWarning());

        $oBasket->setCatChangeWarningState(true);
        $this->assertTrue($oBasket->showCatChangeWarning());
    }

    /**
     * oxbasket::isProductInRootCategory() test case
     *
     * @return null
     */
    public function testScIsProductInRootCategory()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oDb = oxDb::getDb();
        $sVariantId = $oDb->getOne("select oxid from oxarticles where oxparentid != ''");
        $sProductId = $oDb->getOne("select oxparentid from oxarticles where oxid = '{$sVariantId}'");
        $sCategoryId = $oDb->getOne("select oxcatnid from oxobject2category where oxobjectid = '{$sProductId}'");

        $sQ = "select oxcategories.oxrootid from oxobject2category
                 left join oxcategories on oxcategories.oxid = oxobject2category.oxcatnid
                 where oxobject2category.oxobjectid = '{$sProductId}'";
        $sRootCatId = $oDb->getOne($sQ);


        $oBasket = oxNew('oxBasket');

        // regular product
        $this->assertTrue($oBasket->UNITisProductInRootCategory($sProductId, $sRootCatId), "first fail");
        $this->assertFalse($oBasket->UNITisProductInRootCategory($sProductId, "anycategory"), "first fail");

        // variant
        $this->assertTrue($oBasket->UNITisProductInRootCategory($sVariantId, $sRootCatId), "first fail");
        $this->assertFalse($oBasket->UNITisProductInRootCategory($sVariantId, "anycategory"), "first fail");
    }

    /**
     * oxbasket::addToBasket() test case
     *
     * @return null
     */
    public function testScAddToBasket()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oBasket = $this->getMock("oxbasket", array("canAddProductToBasket", "setCatChangeWarningState"));
        $oBasket->expects($this->once())->method('canAddProductToBasket')->with($this->equalTo("1126"))->will($this->returnValue(true));
        $oBasket->expects($this->once())->method('setCatChangeWarningState')->with($this->equalTo(false));
        $oBasket->addToBasket("1126", 1);

        $oBasket = $this->getMock("oxbasket", array("canAddProductToBasket", "setCatChangeWarningState"));
        $oBasket->expects($this->once())->method('canAddProductToBasket')->with($this->equalTo("1126"))->will($this->returnValue(false));
        $oBasket->expects($this->once())->method('setCatChangeWarningState')->with($this->equalTo(true));
        $oBasket->addToBasket("1126", 1);
    }

    /**
     * oxbasket::canAddProductToBasket() test case
     *
     * @return null
     */
    public function testScCanAddProductToBasketEmptyBasketNoViewCategory()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $this->setRequestParameter('cnid', null);

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->canAddProductToBasket("1126"));
    }

    /**
     * oxbasket::canAddProductToBasket() test case
     *
     * @return null
     */
    public function testScCanAddProductToBasketEmptyBasketFittingViewCategory()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCategory = oxNew('oxCategory');
        $oCategory->load(oxDb::getDb()->getOne("select oxcatnid from oxobject2category where oxobjectid = '1126'"));
        $this->getConfig()->getActiveView()->setActiveCategory($oCategory);

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->canAddProductToBasket("1126"));
    }

    /**
     * oxbasket::canAddProductToBasket() test case
     *
     * @return null
     */
    public function testScCanAddProductToBasketEmptyBasketNotFittingViewCategory()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCategory = oxNew('oxCategory');
        $oCategory->load(oxDb::getDb()->getOne("select oxcatnid from oxobject2category where oxobjectid != '1126'"));
        $this->getConfig()->getActiveView()->setActiveCategory($oCategory);

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->canAddProductToBasket("1126"));
    }

    /**
     * oxbasket::canAddProductToBasket() test case
     *
     * @return null
     */
    public function testScCanAddProductToBasketEmptyBasket()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $this->setRequestParameter('cnid', oxDb::getDb()->getOne("select oxcatnid from oxobject2category where oxobjectid != '1126'"));

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->canAddProductToBasket("1126"));
    }

    /**
     * oxbasket::canAddProductToBasket() test case
     *
     * @return null
     */
    public function testScCanAddProductToBasket()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oDb = oxDb::getDb();

        $sCatId = $oDb->getOne("select oxcatnid, count(oxcatnid) as _cnt from oxobject2category group by oxcatnid having _cnt > 1");
        $sRootCatId = $oDb->getOne("select oxrootid from oxcategories where oxid = '{$sCatId}'");
        $this->setRequestParameter('cnid', $sCatId);

        $sProductId1 = $oDb->getOne("select oxobjectid from oxobject2category where oxcatnid = '{$sCatId}'");
        $sProductId2 = $oDb->getOne("select oxobjectid from oxobject2category where oxcatnid = '{$sCatId}' and oxobjectid != '{$sProductId1}'");
        $sProductId3 = $oDb->getOne("select oxid from oxcategories where oxrootid != '{$sRootCatId}' ");

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->canAddProductToBasket($sProductId1));
        $this->assertTrue($oBasket->canAddProductToBasket($sProductId2));
        $this->assertFalse($oBasket->canAddProductToBasket($sProductId3));
    }

    /**
     * oxbasket::setBasketRootCatId() test case
     *
     * @return null
     */
    public function testScSetBasketRootCatId()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oBasket = $this->getProxyClass("oxbasket");
        $this->assertNull($oBasket->getNonPublicVar("_sBasketCategoryId"));

        $oBasket->setBasketRootCatId('_testExclRoot');
        $this->assertEquals('_testExclRoot', $oBasket->getNonPublicVar("_sBasketCategoryId"));
    }

    /**
     * oxbasket::getBasketRootCatId() test case
     *
     * @return null
     */
    public function testScGetBasketRootCatId()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oBasket = $this->getProxyClass("oxbasket");
        $this->assertNull($oBasket->getBasketRootCatId());

        $oBasket->setNonPublicVar("_sBasketCategoryId", '_testExclRoot');
        $this->assertEquals('_testExclRoot', $oBasket->getBasketRootCatId());
    }

    /**
     * Testing oxbasket::_oNotDiscountedProductsPriceList getter
     *
     * @return null
     */
    public function testGetNotDiscountProductsPrice()
    {
        $oBasket = $this->getProxyClass("oxbasket");
        $this->assertNull($oBasket->setNonPublicVar("_oNotDiscountedProductsPriceList", "testPrice"));
        $this->assertEquals("testPrice", $oBasket->getNotDiscountProductsPrice());
    }


    /**
     * #0002163: itm discount option "multiple" is not working if several products/categires are assigned to discount
     *
     * @return null
     */
    public function testForBugEntry2163()
    {
        // cleaning up
        $this->tearDown();

        $sShopId = $this->getConfig()->getBaseShopId();

        // create new discount
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId('_testDiscount');
        $oDiscount->oxdiscount__oxshopid = new oxField($sShopId);
        $oDiscount->oxdiscount__oxactive = new oxField(1);
        $oDiscount->oxdiscount__oxtitle = new oxField("Item discount");
        $oDiscount->oxdiscount__oxamount = new oxField(3);
        $oDiscount->oxdiscount__oxamountto = new oxField(9999);
        $oDiscount->oxdiscount__oxprice = new oxField(0);
        $oDiscount->oxdiscount__oxpriceto = new oxField(0);
        $oDiscount->oxdiscount__oxaddsum = new oxField(0);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField("itm");
        $oDiscount->oxdiscount__oxitmartid = new oxField('1142');
        $oDiscount->oxdiscount__oxitmamount = new oxField(1);
        $oDiscount->oxdiscount__oxitmmultiple = new oxField(1);
        $oDiscount->save();

        $oO2D = oxNew('oxbase');
        $oO2D->init("oxobject2discount");
        $oO2D->setId('_testo2d1');
        $oO2D->oxobject2discount__oxdiscountid = new oxField('_testDiscount');
        $oO2D->oxobject2discount__oxobjectid = new oxField('1126');
        $oO2D->oxobject2discount__oxtype = new oxField("oxarticles");
        $oO2D->save();

        $oO2D = oxNew('oxbase');
        $oO2D->init("oxobject2discount");
        $oO2D->setId('_testo2d2');
        $oO2D->oxobject2discount__oxdiscountid = new oxField('_testDiscount');
        $oO2D->oxobject2discount__oxobjectid = new oxField('1131');
        $oO2D->oxobject2discount__oxtype = new oxField("oxarticles");
        $oO2D->save();

        $oBasket = $this->getMock("oxBasket", array("load"));
        $oBasket->addToBasket('1126', 6);
        $oBasket->addToBasket('1131', 3);
        $oBasket->calculateBasket();

        $aContents = $oBasket->getContents();
        $aInfo = array('1142' => 3, '1126' => 6, '1131' => 3);

        $this->assertEquals(3, count($aContents));
        foreach ($aContents as $oContent) {
            $sId = $oContent->getProductId();
            $this->assertTrue(isset($aInfo[$sId]));
            $this->assertEquals($aInfo[$sId], $oContent->getAmount());
        }
    }

    /**
     * Test for a vat calculation bug described in the following reports:
     *
     * #0005795: Percentual voucher assigned to article leads to wrong VAT and netto sum calculation in mixed baskets
     * #0006204: Vat calculation is wrong when article is assigned to coupon serie
     * #0006283: Voucher vat calculation
     *
     */
    public function testForBugEntries5795_6204_6283()
    {
        $regularArticleId = '1951'; // 14 EUR
        $discountedArticleId = '1126'; //34 EUR

        $this->oVoucherSerie->oxvoucherseries__oxdiscounttype = new oxField('percent', oxField::T_RAW);
        $this->oVoucherSerie->save();

        // assigning voucher serie to Article 2024
        $discount2Article = oxNew('oxBase');
        $discount2Article->init('oxobject2discount');
        $discount2Article->setId('_dsci1');
        $discount2Article->oxobject2discount__oxdiscountid = new oxField($this->oVoucherSerie->getId(), oxField::T_RAW);
        $discount2Article->oxobject2discount__oxobjectid = new oxField($discountedArticleId, oxField::T_RAW);
        $discount2Article->oxobject2discount__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $discount2Article->save();

        $basket = oxNew('oxbasket');
        $basket->addToBasket($regularArticleId, 1);
        $basket->addToBasket($discountedArticleId, 1);
        $basket->calculateBasket(false);

        // add basket to session to make voucher addition possible
        $this->getSession()->setBasket($basket);

        $voucher = reset($this->aVouchers);
        $basket->addVoucher($voucher->oxvouchers__oxvouchernr->value); // 10 %

        $basket->calculateBasket(false);

        $vats = $basket->getProductVats(false);

        $this->assertEquals(7.12, oxRegistry::getUtils()->fRound($vats[19]));
        $this->assertEquals(37.48, oxRegistry::getUtils()->fRound($basket->getNettoSum()));
    }

    /**
     * Test case for oxBasket::_addedNewItem(), oxBasket::isNewItemAdded()
     *
     * @return null
     */
    public function testIsNewItemAdded()
    {
        $oBasket = oxNew('oxBasket');
        $this->assertFalse($oBasket->isNewItemAdded());
        $this->assertNull(oxRegistry::getSession()->getVariable("blAddedNewItem"));

        $oBasket = oxNew('oxBasket');
        $oBasket->UNITaddedNewItem(0, 0, 0, 0, 0, 0, 0);
        $this->assertTrue(oxRegistry::getSession()->getVariable("blAddedNewItem"));
        $this->assertTrue($oBasket->isNewItemAdded());
        $this->assertNull(oxRegistry::getSession()->getVariable("blAddedNewItem"));

    }

    /**
     * Testing oxbasket::hasDownloadableProducts getter
     *
     * @return null
     */
    public function testHasDownloadableProducts()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxisdownloadable = new oxField(true);
        $oOrderArticle = $this->getMock('oxorderarticle', array('getArticle'));
        $oOrderArticle->expects($this->any())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oOrderArticle));
        $this->assertTrue($oBasket->hasDownloadableProducts());
    }


    /**
     * testing #4411 fix
     */
    public function testHasDownloadableProductsException()
    {
        $oOrderArticle = oxNew("oxBasketItem");
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->addtoBasket("1126", 5);
        try {
            $blRes = $oBasket->hasDownloadableProducts();
        } catch (Exception $oE) {
            $this->fail("Exceptions within hasDownloadableProducts() should be catched.");
        }

        $this->assertFalse($blRes);
    }

    /**
     * oxbasket::isProportionalCalculationOn() test case
     */
    public function testIsProportionalCalculationOn()
    {
        $this->getConfig()->setConfigParam("sAdditionalServVATCalcMethod", 'proportional');

        $oBasket = oxNew('oxBasket');
        $this->assertTrue($oBasket->isProportionalCalculationOn());

        $this->getConfig()->setConfigParam("sAdditionalServVATCalcMethod", 'not propotional');
        $this->assertFalse($oBasket->isProportionalCalculationOn());
    }

    /**
     * oxbasket::getAdditionalServicesVatPercent() test case
     */
    public function testGetAdditionalServicesVatPercent()
    {
        $this->getConfig()->setConfigParam("sAdditionalServVATCalcMethod", 'proportional');

        $oArticle = oxNew('oxArticle');

        $oArticle->setId('_testArt1');
        $oArticle->oxarticles__oxprice = new oxField(60);
        $oArticle->oxarticles__oxvat = new oxField(20);
        $oArticle->save();

        $oArticle->setId('_testArt2');
        $oArticle->oxarticles__oxprice = new oxField(110);
        $oArticle->oxarticles__oxvat = new oxField(10);
        $oArticle->save();

        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('_testArt1', 2);
        $oBasket->addToBasket('_testArt2', 1);
        $oBasket->calculateBasket();

        $this->assertEquals(15, $oBasket->getAdditionalServicesVatPercent());
        $this->getConfig()->setConfigParam("sAdditionalServVATCalcMethod", 'not propotional');
        $this->assertEquals(20, $oBasket->getAdditionalServicesVatPercent());
    }


    /**
     * testing the update of basket after adding two products with same selection list
     *
     * @return null
     */
    public function testGetBasketSummary_WithSelectionList()
    {
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', true);
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $sArtId = '1126';
        $oBasket = oxNew('oxBasket');

        // creating selection list
        $oSelList = oxNew('oxSelectlist');
        $oSelList->setId('_testoxsellist');
        $oSelList->oxselectlist__oxtitle = new oxfield('testsel');
        $oSelList->oxselectlist__oxvaldesc = new oxfield('Large!P!10__@@Medium!P!20__@@Small!P!30__@@');
        $oSelList->save();

        // assigning sel list
        $oO2Sel = oxNew('oxBase');
        $oO2Sel->init("oxobject2selectlist");
        $oO2Sel->setId('_testoxobject2selectlist');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxfield($sArtId);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxfield($oSelList->getId());
        $oO2Sel->save();

        // storing products to basket with diff sel list
        $oBasket->addToBasket($sArtId, 1, array(0));
        $oBasket->calculateBasket();
        $oBasket->onUpdate();
        $oSummary = $oBasket->getBasketSummary();

        // checking amounts
        $this->assertEquals(44, $oSummary->dArticleDiscountablePrice);
    }

    public function testGetSaveBasketSetNotSave()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->enableSaveToDataBase(false);
        $this->assertFalse($oBasket->isSaveToDataBaseEnabled());
    }

    public function testGetSaveBasketSetNotSaveWithConfig()
    {
        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);
        $this->assertFalse($oBasket->isSaveToDataBaseEnabled());
    }

    public function testGetSaveBasketSetSaveWithConfig()
    {
        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', false);
        $this->assertTrue($oBasket->isSaveToDataBaseEnabled());
    }

    public function testGetSaveBasketSetSaveWithConfigNotDefined()
    {
        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', null);
        $this->assertTrue($oBasket->isSaveToDataBaseEnabled());
    }

    /**
     * @return array
     */
    public function providerHasArticlesWithIntangibleAgreement()
    {
        $aEmptyBasket = array();

        $aBasketWithOneIntangibleArticle = array(
            $this->createBasketItemForArticleAgreementTests(true, false, true),
        );

        $aBasketWithOneIntangibleAndOtherArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, false, false),
            $this->createBasketItemForArticleAgreementTests(true, false, true),
        );

        $aBasketWithOneIntangibleAndDownloadableArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, true, true),
            $this->createBasketItemForArticleAgreementTests(true, false, true),
        );

        $aBasketWithOtherArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, false, false),
            $this->createBasketItemForArticleAgreementTests(false, true, true),
        );

        $aBasketWithIntangibleWithoutShowingAgreement = array(
            $this->createBasketItemForArticleAgreementTests(true, false, false),
        );

        return array(
            array($aEmptyBasket, false),
            array($aBasketWithOneIntangibleArticle, true),
            array($aBasketWithOneIntangibleAndOtherArticles, true),
            array($aBasketWithOneIntangibleAndDownloadableArticles, true),
            array($aBasketWithOtherArticles, false),
            array($aBasketWithIntangibleWithoutShowingAgreement, false),
        );
    }

    /**
     * @param array $aBasketContents
     * @param bool  $blResult
     *
     * @dataProvider providerHasArticlesWithIntangibleAgreement
     */
    public function testHasArticlesWithIntangibleAgreementWhenArticleExists($aBasketContents, $blResult)
    {
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", $aBasketContents);
        $this->assertSame($blResult, $oBasket->hasArticlesWithIntangibleAgreement());
    }

    /**
     * @return array
     */
    public function providerHasArticlesWithDownloadableAgreement()
    {
        $aEmptyBasket = array();

        $aBasketWithOneDownloadableArticle = array(
            $this->createBasketItemForArticleAgreementTests(false, true, true),
        );

        $aBasketWithOneDownloadableAndOtherArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, false, false),
            $this->createBasketItemForArticleAgreementTests(false, true, true),
        );

        $aBasketWithOneIntangibleAndDownloadableArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, true, true),
            $this->createBasketItemForArticleAgreementTests(true, false, true),
        );

        $aBasketWithOtherArticles = array(
            $this->createBasketItemForArticleAgreementTests(false, false, false),
            $this->createBasketItemForArticleAgreementTests(true, false, true),
        );

        $aBasketWithDownloadableArticleWithoutShowingAgreement = array(
            $this->createBasketItemForArticleAgreementTests(true, false, false),
        );

        return array(
            array($aEmptyBasket, false),
            array($aBasketWithOneDownloadableArticle, true),
            array($aBasketWithOneDownloadableAndOtherArticles, true),
            array($aBasketWithOneIntangibleAndDownloadableArticles, true),
            array($aBasketWithOtherArticles, false),
            array($aBasketWithDownloadableArticleWithoutShowingAgreement, false),
        );
    }

    /**
     * @param array $aBasketContents
     * @param bool  $blResult
     *
     * @dataProvider providerHasArticlesWithDownloadableAgreement
     */
    public function testHasArticlesWithDownloadableAgreement($aBasketContents, $blResult)
    {
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", $aBasketContents);
        $this->assertSame($blResult, $oBasket->hasArticlesWithDownloadableAgreement());
    }

    /**
     * Creates and returns basket item object based on given options
     *
     * @param bool $blIntangible
     * @param bool $blDownloadable
     * @param bool $blShowCustomAgreement
     *
     * @return oxBasketItem
     */
    private function createBasketItemForArticleAgreementTests($blIntangible, $blDownloadable, $blShowCustomAgreement)
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxnonmaterial = new oxField($blIntangible);
        $oArticle->oxarticles__oxisdownloadable = new oxField($blDownloadable);
        $oArticle->oxarticles__oxshowcustomagreement = new oxField($blShowCustomAgreement);

        $oOrderArticle = $this->getMock('oxorderarticle', array('getArticle'));
        $oOrderArticle->expects($this->any())->method('getArticle')->will($this->returnValue($oArticle));

        return $oOrderArticle;
    }

}
