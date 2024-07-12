<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxField;
use oxRegistry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;


class DiscountlistTest extends \OxidTestCase
{
    public $aDiscountIds = [];

    public $aDiscountArtIds = [];

    public $aTransparentDiscountArtIds = [];

    protected function tearDown(): void
    {
        $this->cleanUpTable('oxobject2discount');
        $this->cleanUpTable('oxcategories');
        parent::tearDown();
    }

    // just SQL cleaner ..
    protected function cleanSQL($sQ)
    {
        return preg_replace(['/[^\w\'\:\-\.\*]/'], '', $sQ);
    }

    /**
     * Testing if discount list will be build even some data is wrong
     */
    public function testGetDiscountListWithSomeWrongData()
    {
        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oArticle = oxNew('oxArticle');
        $oArticle->load("1431");

        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket("1431", 1);
        $oBasket->calculateBasket();

        $oDiscountList = oxNew('oxDiscountList');
        $oDiscountList = $oDiscountList->getBasketItemDiscounts($oArticle, $oBasket, $oUser);

        $iListCOunt = count($oDiscountList);

        // list must contain at least one item
        $this->assertTrue($iListCOunt > 0);

        $oDiscount = current($oDiscountList);

        // adding garbage
        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2discount");
        $oGarbage->setId("_testoxobject2discount1");

        $oGarbage->oxobject2discount__oxdiscountid = new oxField($oDiscount->getId());
        $oGarbage->oxobject2discount__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2discount__oxtype = new oxField("oxcountry");
        $oGarbage->save();

        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2discount");
        $oGarbage->setId("_testoxobject2discount2");

        $oGarbage->oxobject2discount__oxdiscountid = new oxField($oDiscount->getId());
        $oGarbage->oxobject2discount__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2discount__oxtype = new oxField("oxuser");
        $oGarbage->save();

        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2discount");
        $oGarbage->setId("_testoxobject2discount3");

        $oGarbage->oxobject2discount__oxdiscountid = new oxField($oDiscount->getId());
        $oGarbage->oxobject2discount__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2discount__oxtype = new oxField("oxgroups");
        $oGarbage->save();

        $oDiscountList = oxNew('oxDiscountList');
        $oDiscountList = $oDiscountList->getBasketItemDiscounts($oArticle, $oBasket, $oUser);

        $iNewListCount = count($oDiscountList);

        // list must contain at least one item
        $this->assertTrue($iNewListCount > 0);
        $this->assertTrue($iNewListCount === $iListCOunt);

        $blFound = false;
        foreach ($oDiscountList as $oDisc) {
            if ($oDiscount->getId() == $oDisc->getId()) {
                $blFound = true;
                break;
            }
        }

        $this->assertTrue($blFound, "Error, delivery set not found");
    }

    /**
     * Checking dicount list initializer
     */
    // with user
    public function testGetList()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $oUser->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['selectString', 'getFilterSelect']);
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->once())->method('getFilterSelect');
        $oList->getDiscountList($oUser);
    }

    // testing returned data
    public function testGetListDataCheckNoUser()
    {
        $oList = oxNew('oxdiscountlist');
        $oList->getDiscountList();

        // checking using demo data
        $this->assertEquals(1, $oList->count());
        $this->assertEquals(
            ['4e542e4e8dd127836.00288451'],
            array_keys($oList->aList)
        );
    }

    public function testGetListDataCheckAdminUser()
    {
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oList = oxNew('oxdiscountlist');
        $oList->getDiscountList($oUser);

        // checking using demo data
        $this->assertEquals(1, $oList->count());
        $this->assertEquals(
            ['4e542e4e8dd127836.00288451'],
            array_keys($oList->aList)
        );
    }

    /**
     * Testing country ID getter
     */
    // no user, will be taken from config
    public function testGetCountryIdNoUserExpectsConfigCountry()
    {
        $oList = oxNew('oxdiscountlist');
        $this->assertNull($oList->getCountryId(null));
    }

    // taking user country id
    public function testGetCountryIdAdminUser()
    {
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oList = oxNew('oxdiscountlist');
        $this->assertEquals('a7c40f631fc920687.20179984', $oList->getCountryId($oUser));
    }

    /**
     * Testing discount filter SQL getter
     */
    // no user
    public function testGetFilterSelectNoUser()
    {
        $iCurrTime = 0;

        $oUtilsDate = $this->getMock(\OxidEsales\Eshop\Core\UtilsDate::class, ['getRequestTime']);
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsDate::class, $oUtilsDate);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sUserTable = $tableViewNameGenerator->getViewName('oxuser');
        $sGroupTable = $tableViewNameGenerator->getViewName('oxgroups');
        $sCountryTable = $tableViewNameGenerator->getViewName('oxcountry');

        $oList = oxNew('oxdiscountlist');

        // default oxConfig country check.
        $sTable = $tableViewNameGenerator->getViewName('oxdiscount');
        $sQ = "select " . $oList->getBaseObject()->getSelectFields() . sprintf(' from %s where ( ( %s.oxactive = 1 or ( %s.oxactivefrom < \'', $sTable, $sTable, $sTable) . date('Y-m-d H:i:s', $iCurrTime) . sprintf('\' and %s.oxactiveto > \'', $sTable) . date('Y-m-d H:i:s', $iCurrTime) . "')) ) and (
                if(EXISTS(select 1 from oxobject2discount, {$sCountryTable} where {$sCountryTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        0,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, {$sUserTable} where {$sUserTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        0,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        0,
                        1)
            )";
        $sQ .= sprintf(' order by %s.oxsort ', $sTable);

        $this->assertEquals($this->cleanSQL($sQ), $this->cleanSQL($oList->getFilterSelect(null)));
    }

    // admin user
    public function testGetFilterSelectAdminUser()
    {
        $this->setTime(0);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sUserTable = $tableViewNameGenerator->getViewName('oxuser');
        $sGroupTable = $tableViewNameGenerator->getViewName('oxgroups');
        $sCountryTable = $tableViewNameGenerator->getViewName('oxcountry');

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $sGroupIds = '';
        foreach ($oUser->getUserGroups() as $oGroup) {
            if ($sGroupIds !== '' && $sGroupIds !== '0') {
                $sGroupIds .= ', ';
            }

            $sGroupIds .= "'" . $oGroup->getId() . "'";
        }

        $oList = oxNew('oxdiscountlist');

        // default oxConfig country check.
        $sTable = $tableViewNameGenerator->getViewName('oxdiscount');
        $sQ = "select " . $oList->getBaseObject()->getSelectFields() . sprintf(' from %s where ', $sTable) . $oList->getBaseObject()->getSqlActiveSnippet() . " and (
                if(EXISTS(select 1 from oxobject2discount, {$sCountryTable} where {$sCountryTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID='a7c40f631fc920687.20179984'),
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, {$sUserTable} where {$sUserTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID='oxdefaultadmin'),
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID={$sTable}.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ({$sGroupIds}) ),
                        1)
            )";
        $sQ .= sprintf(' order by %s.oxsort ', $sTable);

        $this->assertEquals($this->cleanSQL($sQ), $this->cleanSQL($oList->getFilterSelect($oUser)));
    }

    /**
     * Testing discount loader by type
     */
    // article discounts
    public function testGetArticleDiscounts()
    {
        // just simulating article
        $oArticle = oxNew('oxArticle');
        $oArticle->xxx = 'yyy';

        $aDiscounts[0] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForArticle', 'getId']);
        $aDiscounts[0]->expects($this->once())->method('isForArticle')->will($this->returnValue(true));
        $aDiscounts[0]->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $aDiscounts[1] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForArticle', 'getId']);
        $aDiscounts[1]->expects($this->once())->method('isForArticle')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('getId')->will($this->returnValue('yyy'));

        $aDiscounts[2] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForArticle', 'getId']);
        $aDiscounts[2]->expects($this->once())->method('isForArticle')->will($this->returnValue(false));
        $aDiscounts[2]->expects($this->never())->method('getId')->will($this->returnValue('zzz'));

        $aDiscounts[3] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForArticle', 'getId']);
        $aDiscounts[3]->expects($this->once())->method('isForArticle')->will($this->returnValue(false));
        $aDiscounts[3]->expects($this->never())->method('getId')->will($this->returnValue('www'));

        $oDList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['getArray']);
        $oDList->expects($this->once())->method('getArray')->will($this->returnValue($aDiscounts));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['getDiscountList']);
        $oList->expects($this->once())->method('getDiscountList')->will($this->returnValue($oDList));

        // now proceeding to disocunt id check
        $this->assertEquals(['xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1]], $oList->getArticleDiscounts($oArticle));
    }

    // basket item
    public function testGetBasketItemDiscounts()
    {
        // just simulating article
        $oArticle = oxNew('oxArticle');
        $oArticle->xxx = 'yyy';

        // simulating basket
        $oBasket = oxNew('oxBasket');
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasketItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[0]->expects($this->once())->method('isForBasketItem')->will($this->returnValue(true));
        $aDiscounts[0]->expects($this->once())->method('getId')->will($this->returnValue('xxx'));
        $aDiscounts[0]->expects($this->once())->method('isForBasketAmount')->will($this->returnValue(true));

        $aDiscounts[1] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasketItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[1]->expects($this->once())->method('isForBasketItem')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('getId')->will($this->returnValue('yyy'));
        $aDiscounts[1]->expects($this->once())->method('isForBasketAmount')->will($this->returnValue(true));

        $aDiscounts[2] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasketItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[2]->expects($this->once())->method('isForBasketItem')->will($this->returnValue(false));
        $aDiscounts[2]->expects($this->never())->method('getId')->will($this->returnValue('zzz'));
        $aDiscounts[2]->expects($this->never())->method('isForBasketAmount');

        $aDiscounts[3] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasketItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[3]->expects($this->once())->method('isForBasketItem')->will($this->returnValue(false));
        $aDiscounts[3]->expects($this->never())->method('getId')->will($this->returnValue('www'));
        $aDiscounts[3]->expects($this->never())->method('isForBasketAmount');

        $aDiscounts[4] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasketItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[4]->expects($this->once())->method('isForBasketItem')->will($this->returnValue(false));
        $aDiscounts[4]->expects($this->never())->method('getId')->will($this->returnValue('www'));
        $aDiscounts[4]->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $aDiscounts[4]->expects($this->never())->method('isForBasketAmount');

        $oDList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['getArray']);
        $oDList->expects($this->once())->method('getArray')->will($this->returnValue($aDiscounts));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['getDiscountList']);
        $oList->expects($this->once())->method('getDiscountList')->will($this->returnValue($oDList));

        // now proceeding to disocunt id check
        $this->assertEquals(['xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1]], $oList->getBasketItemDiscounts($oArticle, $oBasket));
    }

    // basket discounts
    public function testGetBasketDiscounts()
    {
        // simulating basket
        $oBasket = oxNew('oxBasket');
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasket', 'getId']);
        $aDiscounts[0]->expects($this->once())->method('isForBasket')->will($this->returnValue(true));
        $aDiscounts[0]->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $aDiscounts[1] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasket', 'getId']);
        $aDiscounts[1]->expects($this->once())->method('isForBasket')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('getId')->will($this->returnValue('yyy'));

        $aDiscounts[2] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasket', 'getId']);
        $aDiscounts[2]->expects($this->once())->method('isForBasket')->will($this->returnValue(false));
        $aDiscounts[2]->expects($this->never())->method('getId')->will($this->returnValue('zzz'));

        $aDiscounts[3] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBasket', 'getId']);
        $aDiscounts[3]->expects($this->once())->method('isForBasket')->will($this->returnValue(false));
        $aDiscounts[3]->expects($this->never())->method('getId')->will($this->returnValue('www'));

        $oDList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['getArray']);
        $oDList->expects($this->once())->method('getArray')->will($this->returnValue($aDiscounts));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['getDiscountList']);
        $oList->expects($this->once())->method('getDiscountList')->will($this->returnValue($oDList));

        // now proceeding to disocunt id check
        $this->assertEquals(['xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1]], $oList->getBasketDiscounts($oBasket));
    }

    // basket item
    public function testGetBasketItemBundleDiscounts()
    {
        // just simulating article
        $oArticle = oxNew('oxArticle');
        $oArticle->xxx = 'yyy';

        // simulating basket
        $oBasket = oxNew('oxBasket');
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[0]->expects($this->once())->method('isForBundleItem')->will($this->returnValue(true));
        $aDiscounts[0]->expects($this->never())->method('getId');
        $aDiscounts[0]->expects($this->once())->method('isForBasketAmount')->will($this->returnValue(false));

        $aDiscounts[1] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[1]->expects($this->once())->method('isForBundleItem')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('isForBasketAmount')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('getId')->will($this->returnValue('yyy'));

        $aDiscounts[2] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[2]->expects($this->once())->method('isForBundleItem')->will($this->returnValue(false));
        $aDiscounts[2]->expects($this->never())->method('getId')->will($this->returnValue('zzz'));
        $aDiscounts[2]->expects($this->never())->method('isForBasketAmount');

        $aDiscounts[3] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleItem', 'isForBasketAmount', 'getId']);
        $aDiscounts[3]->expects($this->once())->method('isForBundleItem')->will($this->returnValue(false));
        $aDiscounts[3]->expects($this->never())->method('getId')->will($this->returnValue('www'));
        $aDiscounts[3]->expects($this->never())->method('isForBasketAmount');

        $oDList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['getArray']);
        $oDList->expects($this->once())->method('getArray')->will($this->returnValue($aDiscounts));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['getDiscountList']);
        $oList->expects($this->once())->method('getDiscountList')->will($this->returnValue($oDList));

        // now proceeding to disocunt id check
        $this->assertEquals(['yyy' => $aDiscounts[1]], $oList->getBasketItemBundleDiscounts($oArticle, $oBasket));
    }

    // basket bundle
    public function testGetBasketBundleDiscounts()
    {
        // simulating basket
        $oBasket = oxNew('oxBasket');
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleBasket', 'getId']);
        $aDiscounts[0]->expects($this->once())->method('isForBundleBasket')->will($this->returnValue(true));
        $aDiscounts[0]->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $aDiscounts[1] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleBasket', 'getId']);
        $aDiscounts[1]->expects($this->once())->method('isForBundleBasket')->will($this->returnValue(true));
        $aDiscounts[1]->expects($this->once())->method('getId')->will($this->returnValue('yyy'));

        $aDiscounts[2] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleBasket', 'getId']);
        $aDiscounts[2]->expects($this->once())->method('isForBundleBasket')->will($this->returnValue(false));
        $aDiscounts[2]->expects($this->never())->method('getId')->will($this->returnValue('zzz'));

        $aDiscounts[3] = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, ['isForBundleBasket', 'getId']);
        $aDiscounts[3]->expects($this->once())->method('isForBundleBasket')->will($this->returnValue(false));
        $aDiscounts[3]->expects($this->never())->method('getId')->will($this->returnValue('www'));

        $oDList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['getArray']);
        $oDList->expects($this->once())->method('getArray')->will($this->returnValue($aDiscounts));

        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\DiscountList::class, ['getDiscountList']);
        $oList->expects($this->once())->method('getDiscountList')->will($this->returnValue($oDList));

        // now proceeding to disocunt id check
        $this->assertEquals(['xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1]], $oList->getBasketBundleDiscounts($oBasket));
    }

    //tests forceReload setter
    public function testForceReload()
    {
        $oList = $this->getProxyClass('oxDiscountList');
        $oList->forceReload(true);
        $this->assertTrue($oList->getNonPublicVar('_blReload'));
    }

    public function testHasSkipDiscountCategories()
    {
        // making category
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCat');

        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('Test category 1', oxField::T_RAW);
        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue(oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories());

        $oCategory->oxcategories__oxskipdiscounts = new oxField('0', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertFalse(oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories());


        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue(oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories());
    }
}
