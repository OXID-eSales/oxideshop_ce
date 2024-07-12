<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxDb;
use oxField;
use oxRegistry;
use oxTestModules;
use oxuser;

class VatSelectorTest extends \PHPUnit\Framework\TestCase
{
    /** @var oxArticle */
    private $oArticle;

    /** @var oxCategory */
    private $oCategory;

    protected function setUp(): void
    {
        parent::setUp();
        // demo article
        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2275' : '2077';
        $sNewId = oxRegistry::getUtilsObject()->generateUId();

        $this->oArticle = oxNew('oxArticle');
        $this->oArticle->disableLazyLoading();
        $this->oArticle->Load($sId);

        // making copy
        $this->oArticle->setId($sNewId);

        $this->oArticle->oxarticles__oxweight = new oxField(10, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstock = new oxField(100, oxField::T_RAW);
        $this->oArticle->oxarticles__oxprice = new oxField(19, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();

        // demo category
        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e4143562a5.46426637';

        $sNewId = oxRegistry::getUtilsObject()->generateUId();

        $this->oCategory = oxNew('oxBase');
        $this->oCategory->Init('oxcategories');
        $this->oCategory->Load($sId);

        // making copy
        $this->oCategory->setId($sNewId);
        $this->oCategory->save();

        // assigning article to category
        $oO2Group = oxNew('oxobject2category');
        $oO2Group->oxobject2category__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oO2Group->oxobject2category__oxobjectid = new oxField($this->oArticle->getId(), oxField::T_RAW);
        $oO2Group->oxobject2category__oxcatnid = new oxField($this->oCategory->getId(), oxField::T_RAW);
        $oO2Group->save();
        $this->getConfig()->setConfigParam('dDefaultVAT', '99');
    }

    protected function tearDown(): void
    {
        // deleting demo items
        if ($this->oArticle) {
            $this->oArticle->delete();
        }

        if ($this->oCategory) {
            $this->oCategory->delete();
        }

        oxTestModules::addFunction('oxVatSelector', 'clear', '{ oxVatSelector::$_aUserVatCache = array();}');
        oxNew('oxVatSelector')->clear();
        parent::tearDown();
    }

    /**
     * testing user VAT getter
     */
    public function testGetUserVat()
    {
        $oVatSelector = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ['getForeignCountryUserVat']);
        $oVatSelector->expects($this->once())->method('getForeignCountryUserVat')->willReturn(66);

        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxcountryid = new oxField(null, oxField::T_RAW);
        $this->assertFalse($oVatSelector->getUserVat($oUser, true));
        // check cache
        $this->assertFalse($oVatSelector->getUserVat($oUser));

        $oUser->oxuser__oxcountryid = new oxField('NoneExisting', oxField::T_RAW);
        try {
            $this->assertFalse($oVatSelector->getUserVat($oUser, true));
            $this->fail("This country shouldn't be loaded");
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ObjectException) {
            // expected here
        }

        $aHome = $this->getConfig()->getConfigParam('aHomeCountry');
        $oUser->oxuser__oxcountryid = new oxField($aHome[0], oxField::T_RAW);
        $this->assertFalse($oVatSelector->getUserVat($oUser, true));

        $oDb = oxDb::getDb();
        // foreigner
        $oUser->oxuser__oxcountryid = new oxField($oDb->getOne('select oxid from oxcountry where oxid not in ("' . implode('","', $aHome) . '")'), oxField::T_RAW);
        $this->assertSame(66, $oVatSelector->getUserVat($oUser, true));
    }

    public function testGetForeignCountryUserVat()
    {
        $oCountry1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, ['isInEU']);
        $oCountry1->expects($this->once())->method('isInEU')->willReturn(false);

        $oUser = oxNew('oxuser');
        $oVatSelector = $this->getProxyClass("oxVatSelector");

        $this->assertSame(0, $oVatSelector->getForeignCountryUserVat($oUser, $oCountry1));

        $oCountry2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Country::class, ['isInEU']);
        $oCountry2->expects($this->exactly(2))->method('isInEU')->willReturn(true);
        $oUser->oxuser__oxustid = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oVatSelector->getForeignCountryUserVat($oUser, $oCountry2));
        $oUser->oxuser__oxustid = new oxField("LTsff", oxField::T_RAW);
        $oCountry2->oxcountry__oxisoalpha2 = new oxField('LT', oxField::T_RAW);
        $this->assertSame(0, $oVatSelector->getForeignCountryUserVat($oUser, $oCountry2));
    }

    /**
     * testing article VAT getter
     */
    // article has custom VAT stored in oxarticle
    public function testFindArticleVatArticleHasCustomVat()
    {
        $oVatSelector1 = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ['getVatForArticleCategory']);
        $oVatSelector1->expects($this->once())->method('getVatForArticleCategory')->willReturn(69);

        $oArticle1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getCustomVAT']);
        $oArticle1->expects($this->once())->method('getCustomVAT')->willReturn('66');

        $this->assertSame(66, $oVatSelector1->getArticleVat($oArticle1));

        $oArticle2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getCustomVAT']);
        $oArticle2->expects($this->exactly(2))->method('getCustomVAT')->willReturn(null);

        $this->assertSame(69, $oVatSelector1->getArticleVat($oArticle2));

        $oVatSelector1 = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ['getVatForArticleCategory']);
        $oVatSelector1->expects($this->once())->method('getVatForArticleCategory')->willReturn(false);

        $this->assertSame(99, $oVatSelector1->getArticleVat($oArticle2));
    }

    public function testGetVatForArticleCategory()
    {
        //make sure getCategories are never called
        $oArticle1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getCategoryIds']);
        $oArticle1->expects($this->never())->method('getCategoryIds');

        $oVatSelector = oxNew('oxVatSelector');
        $this->assertFalse($oVatSelector->getVatForArticleCategory($oArticle1));

        $this->oCategory->oxcategories__oxvat = new oxField(69, oxField::T_RAW);
        $this->oCategory->save();

        $oVatSelector = oxNew('oxVatSelector');
        $this->assertSame(69, $oVatSelector->getVatForArticleCategory($this->oArticle));

        $this->oCategory->oxcategories__oxvat = new oxField(null, oxField::T_RAW);
        $this->oCategory->save();

        $oVatSelector = oxNew('oxVatSelector');
        $this->assertFalse($oVatSelector->getVatForArticleCategory($this->oArticle));
    }

    public function testGetVatForArticleCategoryArtWithoutCat()
    {
        $oArticle1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId']);
        //make sure getCategories are never called
        $oArticle1->expects($this->once())->method('getId')->willReturn('666');
        $oVatSelector = $this->getProxyClass("oxVatSelector");

        $this->oCategory->oxcategories__oxvat = new oxField(69, oxField::T_RAW);
        $this->oCategory->save();

        $this->assertFalse($oVatSelector->getVatForArticleCategory($oArticle1));
    }

    /**
     * Testing basket item VAT getter, which does same things as getArticleVat
     * FYI: method "getBasketItemVat" is good if someone needs some special
     * behaviour while calculatin basket price :)
     */
    public function testGetBasketItemVat()
    {
        $oVatSelector = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ['getArticleVat']);
        $oVatSelector->expects($this->once())->method('getArticleVat')->willReturn(66);

        $this->assertSame(66, $oVatSelector->getBasketItemVat($this->oArticle, null));
    }

    /**
     * Testing article user VAT getter
     */
    public function testGetArticleUserVat()
    {
        $oVatSelector = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ['getUserVat']);
        $oVatSelector->expects($this->once())->method('getUserVat')->willReturn(66);
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getArticleUser']);
        $oArticle->expects($this->once())->method('getArticleUser')->willReturn(new oxuser());

        $this->assertSame(66, $oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Testing article user VAT getter
     */
    public function testGetArticleUserVatNoUser()
    {
        $oVatSelector = $this->getProxyClass("oxVatSelector");
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getArticleUser']);
        $oArticle->expects($this->once())->method('getArticleUser')->willReturn(false);

        $this->assertFalse($oVatSelector->getArticleUserVat($oArticle));
    }

    /**
     * Tests oxVatSelector::_getVatCountry() method functionality.
     * Tests the case when for VAT consideration SHIPPING country is taken
     *
     */
    public function testGetVatCountryAsShippingCountry()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $sSwitzerlandId = 'a7c40f6321c6f6109.43859248';

        //swiss address
        $oAddress = oxNew('oxAddress');
        $oAddress->setId('_testAddress');

        $oAddress->oxaddress__oxcountryid = new oxField($sSwitzerlandId);

        $oAddressList = oxNew('oxList');
        $oAddressList['_testAddress'] = $oAddress;

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getUserAddresses", "getSelectedAddressId"]);
        $oUser->oxuser__oxcountryid = new oxField($sGermanyId);
        $oUser->expects($this->exactly(1))->method("getUserAddresses")->willReturn($oAddressList);
        $oUser->expects($this->exactly(1))->method("getSelectedAddressId")->willReturn('_testAddress');

        //the option is ON
        $this->getConfig()->setConfigParam("blShippingCountryVat", true);

        $oVatSelector = $this->getProxyClass("oxVatSelector");
        $this->assertSame($sSwitzerlandId, $oVatSelector->getVatCountry($oUser));
    }

    /**
     * Tests oxVatSelector::_getVatCountry() method functionality.
     * Tests the case when for VAT consideration SHIPPING country is taken
     *
     */
    public function testGetVatCountryAsShippingCountryShippingNOTSelected()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $sSwitzerlandId = 'a7c40f6321c6f6109.43859248';

        //swiss address
        $oAddress = oxNew('oxAddress');
        $oAddress->setId('_testAddress');

        $oAddress->oxaddress__oxcountryid = new oxField($sSwitzerlandId);

        $oAddressList = oxNew('oxList');
        $oAddressList['_testAddress'] = $oAddress;

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getUserAddresses", "getSelectedAddressId"]);
        $oUser->oxuser__oxcountryid = new oxField($sGermanyId);
        $oUser->expects($this->exactly(1))->method("getUserAddresses")->willReturn($oAddressList);
        $oUser->expects($this->exactly(1))->method("getSelectedAddressId")->willReturn(null);

        //the option is ON
        $this->getConfig()->setConfigParam("blShippingCountryVat", true);

        $oVatSelector = $this->getProxyClass("oxVatSelector");
        $this->assertSame($sGermanyId, $oVatSelector->getVatCountry($oUser));
    }

    /**
     * Tests oxVatSelector::_getVatCountry() method functionality.
     * Tests the case when for VAT consideration BILLING country is taken
     *
     */
    public function testGetVatCountryAsBillingCountry()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $sSwitzerlandId = 'a7c40f6321c6f6109.43859248';

        //swiss address
        $oAddress = oxNew('oxAddress');
        $oAddress->setId('_testAddress');

        $oAddress->oxaddress__oxcountryid = new oxField($sSwitzerlandId);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getUserAddresses", "getSelectedAddressId"]);
        $oUser->oxuser__oxcountryid = new oxField($sGermanyId);
        $oUser->expects($this->never())->method("getUserAddresses");
        $oUser->expects($this->never())->method("getSelectedAddressId");

        //the option is ON
        $this->getConfig()->setConfigParam("blShippingCountryVat", false);

        $oVatSelector = $this->getProxyClass("oxVatSelector");
        $this->assertSame($sGermanyId, $oVatSelector->getVatCountry($oUser));
    }

    /**
     * Tests whether oxVatSelector::_getVatCountry() is correctly envoked
     *
     */
    public function testGetVatCountryIsCalled()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $oUser = oxNew('oxUser');

        $oVatSelector = $this->getMock(\OxidEsales\Eshop\Application\Model\VatSelector::class, ["getVatCountry"]);
        $oVatSelector->expects($this->once())->method("getVatCountry")->with($oUser)->willReturn($sGermanyId);
        $oVatSelector->getUserVat($oUser, true);
    }
}
