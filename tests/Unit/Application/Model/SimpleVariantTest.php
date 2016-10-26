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

use \OxidEsales\EshopCommunity\Core\Price;

use \oxField;
use \StdClass;
use \oxRegistry;
use \oxTestModules;

class SimpleVariantTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxdiscount');
        oxTestModules::cleanAllModules();
        oxRegistry::get("oxDiscountList")->forceReload();
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    /**
     * Resting if magic getter returns "aSelectlist" value
     *
     * @return null
     */
    public function testGetASelectlist()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadSelectLists", true);

        $oVariant = $this->getMock('oxSimpleVariant', array('getSelectLists'));
        $oVariant->expects($this->once())->method('getSelectLists')->will($this->returnValue("testSelLists"));

        $this->assertEquals("testSelLists", $oVariant->getSelectLists());
    }

    /**
     * oxSimpleVariant::getStdLink() test case
     *
     * @return
     */
    public function testGetBaseStdLink()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("testArticle");

        $oVariant = oxNew('oxSimpleVariant');
        $oVariant->setId("testArticle");

        $this->assertEquals($oArticle->getBaseStdLink(0), $oVariant->getBaseStdLink(0));
        $this->assertEquals($oArticle->getBaseStdLink(1), $oVariant->getBaseStdLink(1));
        $this->assertEquals($oArticle->getBaseStdLink(2, false, false), $oVariant->getBaseStdLink(2, false, false));
    }

    /**
     * oxSimpleVariant::getStdLink() test case
     *
     * @return
     */
    public function testGetStdLink()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("testArticle");

        $oVariant = oxNew('oxSimpleVariant');
        $oVariant->setId("testArticle");

        $this->assertEquals($oArticle->getStdLink(), $oVariant->getStdLink());
        $this->assertEquals($oArticle->getStdLink(1), $oVariant->getStdLink(1));
        $this->assertEquals($oArticle->getStdLink(2, array("param" => "value")), $oVariant->getStdLink(2, array("param" => "value")));
    }

    /**
     * Test get A group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceA()
    {
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->returnValue(true));

        $oVariant = $this->getMock('oxSimpleVariant', array('getUser'));
        $oVariant->oxarticles__oxpricea = new oxField(12, oxField::T_RAW);
        $oVariant->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals(12, $oVariant->UNITgetGroupPrice());
    }

    /**
     * Test get B group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceB()
    {
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(true), $this->returnValue(false)));

        $oVariant = $this->getMock('oxSimpleVariant', array('getUser'));
        $oVariant->oxarticles__oxpriceb = new oxField(12, oxField::T_RAW);
        $oVariant->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals(12, $oVariant->UNITgetGroupPrice());
    }

    /**
     * Test get C group price.
     *
     * @return null
     */
    public function testGetGroupPricePriceC()
    {

        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(false), $this->returnValue(true)));

        $oVariant = $this->getMock('oxSimpleVariant', array('getUser'));
        $oVariant->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oVariant->oxarticles__oxpriceb = new oxField(12, oxField::T_RAW);
        $oVariant->oxarticles__oxpricec = new oxField(12, oxField::T_RAW);
        $oVariant->oxarticles__oxprice = new oxField(15, oxField::T_RAW);

        $this->assertEquals(12, $oVariant->UNITgetGroupPrice());
    }

    /**
     * Test if zero group prices are set generic price depending on config option.
     *
     * @return null
     */
    public function testModifyGroupPricePriceAZero()
    {
        $oUser = $this->getMock('oxuser', array('inGroup'));
        $oUser->expects($this->any())->method('inGroup')->will($this->returnValue(true));

        $oVariant = $this->getMock('oxSimpleVariant', array('getUser'));
        $oVariant->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oVariant->oxarticles__oxprice = new oxField(15, oxField::T_RAW);
        $oVariant->oxarticles__oxpricea = new oxField(0, oxField::T_RAW);
        $oVariant->oxarticles__oxprice->value = 15;

        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', false);
        $this->assertEquals(0, $oVariant->UNITgetGroupPrice());

        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', true);
        $oVariant->oxarticles__oxprice->value = 15;
        $this->assertEquals(15, $oVariant->UNITgetGroupPrice());
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderArticle", "getArticleUrl", "{return 'sArticleUrl';}");

        $oVariant = oxNew('oxSimpleVariant');
        $this->assertEquals("sArticleUrl", $oVariant->getBaseSeoLink(0));
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{return true;}");

        $oVariant = $this->getMock("oxSimpleVariant", array("getBaseSeoLink"));
        $oVariant->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue('sArticleUrl'));
        $this->assertEquals("sArticleUrl", $oVariant->getLink());
    }

    public function testGetLinkSeoOff()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{return false;}");

        $oVariant = $this->getMock("oxSimpleVariant", array("getStdLink"));
        $oVariant->expects($this->once())->method('getStdLink')->will($this->returnValue('sArticleUrl'));
        $this->assertEquals("sArticleUrl", $oVariant->getLink());
    }

    public function testSelectListGetter()
    {
        $oSimpleVar = oxNew('oxSimpleVariant');
        $this->assertNull($oSimpleVar->getSelectLists());
    }

    public function testGetSelectLists()
    {
        $oSubj = oxNew('oxSimpleVariant');
        $this->assertNull($oSubj->getSelectLists());
    }

    public function testSetPrice()
    {
        $sPrice = "someString";
        $oSubj = oxNew('oxSimpleVariant');
        $oSubj->setPrice($sPrice);
        $this->assertEquals($sPrice, $oSubj->getPrice());
    }

    public function testGetPrice()
    {
        $oSubj = $this->getMock('oxSimpleVariant', array('_getGroupPrice', '_applyParentVat', '_applyCurrency'));
        $oSubj->expects($this->once())->method('_getGroupPrice')->will($this->returnValue(1));
        $oSubj->expects($this->once())->method('_applyParentVat')->will($this->returnValue(null));
        $oSubj->expects($this->once())->method('_applyCurrency')->will($this->returnValue(null));
        $oPrice = $oSubj->getPrice();
        $this->assertTrue($oPrice instanceof Price);
    }

    public function testApplyParentVatNoParent()
    {
        $oSubj = $this->getMock("oxSimpleVariant", array('getParent'));
        $oSubj->expects($this->once())->method('getParent')->will($this->returnValue(null));

        $oPrice = oxNew('oxPrice');
        $oSubj->UNITapplyParentVat($oPrice);
    }

    public function testApplyParentVat()
    {
        $oPrice = oxNew('oxPrice');

        $oParent = $this->getMock('oxArticle', array('applyVats'));
        $oParent->expects($this->once())->method('applyVats')->will($this->returnValue(null))->with($oPrice);

        $oSubj = $this->getMock("oxSimpleVariant", array('getParent'));
        $oSubj->expects($this->once())->method('getParent')->will($this->returnValue($oParent));

        $oSubj->UNITapplyParentVat($oPrice);
    }

    // #2231: Admin settings for "apply VAT in cart" and "net product pricing" don't work for Variants
    public function testApplyParentVatCalcVatOnlyForBasketOrder()
    {
        $oPrice = oxNew('oxPrice');
        $this->getConfig()->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', true);

        $oParent = $this->getMock('oxArticle', array('applyVats'));
        $oParent->expects($this->never())->method('applyVats');

        $oSubj = $this->getMock("oxSimpleVariant", array('getParent'));
        $oSubj->expects($this->once())->method('getParent')->will($this->returnValue($oParent));

        $oSubj->UNITapplyParentVat($oPrice);
    }

    public function testGetPriceExisting()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oSubj->setNonPublicVar("_oPrice", 10);
        $this->assertEquals(10, $oSubj->getPrice());
        $this->assertFalse($oSubj->getPrice() instanceof Price);
    }

    public function testGetFPrice()
    {
        $oSubj = oxNew('oxSimpleVariant');
        $oSubj->getPrice()->setPrice(10, 10);
        $this->assertEquals("10,00", $oSubj->getFPrice());
    }

    public function testGetPriceWithDiscount()
    {
        oxRegistry::get("oxDiscountList")->forceReload();

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId("_testDiscount");
        $oDiscount->oxdiscount__oxactive = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(10, oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxpriceto = new oxField(999, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamountto = new oxField(999, oxField::T_RAW);
        $oDiscount->save();

        $oSubj = oxNew('oxSimpleVariant');
        $oSubj->oxarticles__oxprice = new oxField(10);

        $oParent = oxNew('oxArticle');
        $oParent->oxarticles__oxprice = new oxField(10);
        $oSubj->setParent($oParent);

        $this->assertEquals(9, $oSubj->getPrice()->getBruttoPrice());
        $this->cleanUpTable('oxdiscount');
    }

    public function testGetPriceFromParent()
    {
        oxTestModules::addFunction("oxarticle", "skipDiscounts", "{return true;}");
        $oSubj = oxNew('oxSimpleVariant');
        $oParent = oxNew('oxArticle');
        $oParent->oxarticles__oxprice = new oxField(10);
        $oSubj->setParent($oParent);
        $this->assertEquals(10, $oSubj->getPrice()->getBruttoPrice());
    }

    public function testIsLazyLoaded()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $this->assertTrue($oSubj->getNonPublicVar("_blUseLazyLoading"));
    }

    public function testSetParent()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oSubj->setParent("testString");
        $this->assertEquals("testString", $oSubj->getNonPublicVar("_oParent"));
    }

    public function testGetParent()
    {
        $oSubj = oxNew('oxSimpleVariant');
        $oSubj->setParent(5);
        $this->assertEquals(5, $oSubj->getParent());
    }

    public function testApplyCurrency()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oCur = new StdClass;
        $oCur->rate = 2;
        $this->getConfig()->setActShopCurrency(2);
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(100);
        $oSubj->UNITapplyCurrency($oPrice);
        $this->assertEquals(143.26, $oPrice->getBruttoPrice());
        $this->getConfig()->setActShopCurrency(0);
    }

    public function testApplyCurrencyIfObjSet()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oCur = new StdClass;
        $oCur->rate = 0.68;
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(100);
        $oSubj->UNITapplyCurrency($oPrice, $oCur);
        $this->assertEquals(68, $oPrice->getBruttoPrice());
    }

    public function testGetLinkType()
    {
        $oParent = $this->getMock('oxArticle', array('getLinkType'));
        $oParent->expects($this->once())->method('getLinkType')->will($this->returnValue(1));

        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oSubj->setParent($oParent);

        $this->assertEquals(1, $oSubj->getLinkType());
    }

    public function testInCategory()
    {
        $sCatId = "123";
        $oParent = $this->getMock('oxArticle', array('inCategory'));
        $oParent->expects($this->once())->method('inCategory')->with($this->equalTo($sCatId))->will($this->returnValue(true));

        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oSubj->setParent($oParent);

        $this->assertTrue($oSubj->inCategory($sCatId));
    }

    public function testInPriceCategory()
    {
        $sCatId = "123";
        $oParent = $this->getMock('oxArticle', array('inPriceCategory'));
        $oParent->expects($this->once())->method('inPriceCategory')->with($this->equalTo($sCatId))->will($this->returnValue(true));

        $oSubj = $this->getProxyClass("oxSimpleVariant");
        $oSubj->setParent($oParent);

        $this->assertTrue($oSubj->inPriceCategory($sCatId));
    }

    /**
     * 0002030: Option "Calculate Product Price" does not work with variants.
     * Check if no price returned when unset Calculate Product Price.
     */
    function testGetPriceNoPriceCalculate()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadPrice', false);

        $oSubj = oxNew('oxSimpleVariant');
        $oSubj->setPrice(10);
        $iPrice = $oSubj->getPrice();
        $this->assertTrue(empty($iPrice));
    }
}
