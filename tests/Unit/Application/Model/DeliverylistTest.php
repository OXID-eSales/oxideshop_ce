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

use \OxidEsales\EshopCommunity\Application\Model\Delivery;
use \oxArticleHelper;
use \oxdeliverylist;
use \oxDb;
use \oxField;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxRegistry;
use \oxTestModules;
use \oxUser;
use \PHPUnit_Framework_MockObject_MockObject as MockObject;

class oxDeliveryListTestClass extends oxdeliverylist
{

    public function getList($oUser = null, $sCountryId = null, $sDelSet = null)
    {
        return parent::_getList($oUser, $sCountryId, $sDelSet);
    }

    public function _getFilterSelect($oUser, $sCountryId, $sDelSet)
    {
        return parent::_getFilterSelect($oUser, $sCountryId, $sDelSet);
    }

    public function getObjectsInListName()
    {
        return $this->_sObjectsInListName;
    }

    public function getPerfLoadDelivery()
    {
        return $this->_blPerfLoadDelivery;
    }
}

class oxdeliverylistTest_forGetList extends oxdeliverylist
{

    public $sFilterUser;
    public $sFilterCountryId;
    public $sFilterDeliverySet;
    public $sUserId;

    public function getList($oUser = null, $sCountryId = null, $sDelSet = null)
    {
        return parent::_getList($oUser, $sCountryId, $sDelSet);
    }

    public function _getFilterSelect($oUser, $sCountryId, $sDelSet)
    {
        $this->sFilterUser = $oUser ? $oUser : null;
        $this->sFilterCountryId = $sCountryId;
        $this->sFilterDeliverySet = $sDelSet;

        return 'select * from oxdelivery where oxid like "\_test%" ';
    }

    public function getUserId()
    {
        return $this->_sUserId;
    }
}

class oxDb_noActiveSnippetInDeliveryList extends oxDb
{

    public function getActiveSnippet($param1, $param3 = null)
    {
        return '1';
    }
}


class DeliverylistTest extends \OxidTestCase
{
    /** @var oxUser */
    protected $_oUser;

    /** @var array */
    protected $_aTestProducts = array();

    /** @var array  */
    protected $_aCategories = array();

    /** @var array  */
    protected $_aDeliverySets = array();

    /** @var array  */
    protected $_aDeliveries = array();

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        // set to load full deliveries list
        $this->getConfig()->setConfigParam('bl_perfLoadDelivery', true);

        oxAddClassModule('Unit\Application\Model\oxDeliveryListTestClass', 'oxDeliveryList');

        // inserting some demo data

        //set default user
        $this->_oUser = oxNew("oxUser");
        $this->_oUser->setId('_testUserId');
        $this->_oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oUser->save();

        //add user address
        $oAdress = oxNew('oxBase');
        $oAdress->init('oxaddress');
        $oAdress->setId('_testAddressId');
        $oAdress->oxaddress__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);
        $oAdress->oxaddress__oxaddressuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);
        $oAdress->oxaddress__oxcountryid = new oxField('a7c40f6323c4bfb36.59919433', oxField::T_RAW); //italien
        $oAdress->save();
        $this->getSession()->setVariable('deladrid', '_testAddressId');

        //add user to group
        $oO2Group = oxNew('oxBase');
        $oO2Group->init('oxobject2group');
        $oO2Group->setId('_testO2GId');
        $oO2Group->oxobject2group__oxobjectid = new oxField('_testUserId', oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oO2Group->save();

        // delivery set
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_testDeliverySetId');
        $oDelSet->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet->save();
        $this->_aDeliverySets[] = $oDelSet;

        // 1. creating category for test
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCategoryId');
        $oCategory->oxcategories__oxtitle = new oxField('_testCategoryTitle', oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxhidden = new oxField(0, oxField::T_RAW);
        $oCategory->oxcategories__oxdefsortmode = new oxField(0, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->save();
        $this->_aCategories[] = $oCategory;

        //3. insert test articles
        for ($i = 1; $i <= 3; $i++) {
            $oArticle = oxNew("oxArticle");
            $oArticle->setId('_testArticleId' . $i);
            $oArticle->oxarticles__oxtitle = new oxField('testArticle' . $i, oxField::T_RAW);
            $oArticle->oxarticles__oxartnum = new oxField(1000 + $i, oxField::T_RAW);
            $oArticle->oxarticles__oxshortdesc = new oxField('testArticle' . $i . 'Description', oxField::T_RAW);
            $oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
            $oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
            $oArticle->oxarticles__oxshopid = new oxField(1, oxField::T_RAW);

            $oArticle->save();
            $this->_aTestProducts[] = $oArticle;

            // 2.1 assigning products to category
            $oO2Cat = oxNew('oxobject2category');
            $oO2Cat->setId('_testO2CatId' . $i);
            $oO2Cat->oxobject2category__oxobjectid = new oxField($oArticle->getId(), oxField::T_RAW);
            $oO2Cat->oxobject2category__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
            $oO2Cat->oxobject2category__oxcatnid = new oxField($oCategory->getId(), oxField::T_RAW);

            $oO2Cat->save();
        }

        // some deliveries
        for ($i = 1; $i <= 3; $i++) {
            $oDelivery = oxNew('oxDelivery');
            $oDelivery->setId('_testDeliveryId' . $i);
            $oDelivery->oxdelivery__oxtitle = new oxField('_testDelivertTitle' . $i, oxField::T_RAW);
            $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
            $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
            $oDelivery->oxdelivery__oxparam = new oxField(0, oxField::T_RAW);
            $oDelivery->oxdelivery__oxparamend = new oxField(999999, oxField::T_RAW);
            $oDelivery->oxdelivery__oxaddsum = new oxField(100, oxField::T_RAW);
            $oDelivery->oxdelivery__oxfixed = new oxField(0, oxField::T_RAW);
            $oDelivery->oxdelivery__oxsort = new oxField(3 - $i, oxField::T_RAW);
            $oDelivery->oxdelivery__oxfinalize = new oxField(0, oxField::T_RAW);
            //$oDelivery->blForCat = true;
            $oDelivery->save();
            $this->_aDeliveries[] = $oDelivery;

            $oDel2Delset = oxNew('oxBase');
            $oDel2Delset->init('oxdel2delset');
            $oDel2Delset->setId('_testDel2DelSetId' . $i);
            $oDel2Delset->oxdel2delset__oxdelid = new oxField($oDelivery->getId(), oxField::T_RAW);
            $oDel2Delset->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId(), oxField::T_RAW);
            $oDel2Delset->save();
        }

        oxArticleHelper::cleanup();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule('Unit\Application\Model\oxDeliveryListTestClass');
        oxRemClassModule('Unit\Application\Model\oxDb_noActiveSnippetInDeliveryList');

        $this->cleanUpTable('oxdel2delset');
        $this->cleanUpTable('oxobject2category');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxaddress');
        $this->cleanUpTable('oxobject2group');

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->delete('b763e957be61108f8.80080127');
        $oDelivery->delete('3033e968fb5b30930.92732498');
        $oDelivery->delete('a713e96c15c7bf3c7.45279281');
        $oDelivery->delete('a713e96c1aeaefa75.74010807');
        $oDelivery->delete('bdd46f9f2455153b9.22318118');

        $oDelList = oxNew('oxDeliverySet');
        $oDelList->delete('b3b46b74d3894f9f5.62965460');

        oxRegistry::getSession()->deleteVariable('deladrid');

        parent::tearDown();
    }

    /**
     * Testing if delivery list will be build even some data is wrong
     */
    public function testGetDeliveryListWithSomeWrongData()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket("1126", 1);
        $oBasket->addToBasket("1672", 1);
        $oBasket->calculateBasket();

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oDelList = oxNew('oxDeliveryList');
        $oDelList = $oDelList->getDeliveryList($oBasket, $oUser);
        $iListCOunt = count($oDelList);

        // list must contain at least one item
        $this->assertTrue($iListCOunt > 0);

        $oDelivery = current($oDelList);

        // adding garbage
        $oGarbage = oxNew('oxBase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery1");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oGarbage->save();

        $oGarbage = oxNew('oxBase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery2");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxuser");
        $oGarbage->save();

        $oGarbage = oxNew('oxBase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery3");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxgroups");
        $oGarbage->save();

        $oDelList = oxNew('oxDeliveryList');
        $oDelList = $oDelList->getDeliveryList($oBasket, $oUser);
        $iNewListCount = count($oDelList);

        // list must contain at least one item
        $this->assertTrue($iNewListCount > 0);
        $this->assertTrue($iNewListCount === $iListCOunt);

        $blFound = false;
        foreach ($oDelList as $oDel) {
            /** @var oxDelivery $oDel */
            if ($oDelivery->getId() == $oDel->getId()) {
                $blFound = true;
                break;
            }
        }
        $this->assertTrue($blFound, "Error, delivery not found");
    }

    public function testHasDeliveries()
    {
        // test delivery
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->oxdelivery__oxfinalize = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxsort = new oxField(1, oxField::T_RAW);
        $oDelivery->save();

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket('1354', 5);
        $oBasket->calculateBasket();

        $oDelList = oxNew('oxDeliveryList');
        $this->assertFalse($oDelList->hasDeliveries($oBasket, $oUser, 'xxx', 'yyy'));
        $this->assertTrue($oDelList->hasDeliveries($oBasket, $oUser, $oUser->oxuser__oxcountryid->value, 'oxidstandard'));
    }

    /**
     * Testing constructor and if it calls two setters - setLoadFullList and setHomeCountry
     */
    public function testOxDeliveryList()
    {
        /** @var oxDeliveryList|MockObject $oList */
        $oList = $this->getMock('oxDeliveryList', array('setHomeCountry'));
        $oList->expects($this->once())->method('setHomeCountry');
        $oList->__construct();

        // checking object type
        $this->assertTrue($oList->getBaseObject() instanceof delivery);
    }

    /**
     * Testing home country setter
     */
    public function testSetHomeCountry()
    {
        $oList = $this->getProxyClass('oxDeliveryList');
        $oList->setHomeCountry(array('something'));
        $this->assertEquals('something', $oList->getNonPublicVar('_sHomeCountry'));
    }

    public function testSetHomeCountryIfNotArray()
    {
        $oList = $this->getProxyClass('oxDeliveryList');
        $oList->setHomeCountry('something');
        $this->assertEquals('something', $oList->getNonPublicVar('_sHomeCountry'));
    }

    /**
     * Testing getList - test getting list when user and country id is not setted.
     * Should use default country id
     */
    public function testGetListWithoutUserAndCountry()
    {
        $oList = $this->getProxyClass('oxDeliveryList');
        $oList->setHomeCountry(array('_testHomeCountryId'));
        $oList->UNITgetList(null, null, '_testDeliverySetId');

        $this->assertEquals('_testHomeCountryId_testDeliverySetId', $oList->getNonPublicVar('_sUserId'));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2', '_testDeliveryId1'),
            array_keys($oList->aList)
        );
    }

    /**
     * Testing getList - test getting list when user specified and country id not.
     * Should use user country id
     */
    public function testGetListWithExistingUser()
    {
        $oAddress = oxNew('oxBase');
        $oAddress->init('oxaddress');
        $oAddress->load('_testAddressId');
        $oAddress->oxaddress__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW); //germany
        $oAddress->save();
        $oList = $this->getProxyClass('oxDeliveryList');
        $oList->UNITgetList($this->_oUser, null, 'oxidstandard');
        // testing with demo deliveries
        $this->assertEquals($this->_oUser->getId() . 'a7c40f631fc920687.20179984oxidstandard', $oList->getNonPublicVar('_sUserId'));
        $this->assertEquals(
            array('1b842e734b62a4775.45738618', '1b842e73470578914.54719298'),
            array_keys($oList->aList)
        );
    }

    /**
     * Now simply testing if all expected functions are executed
     * Testing with simulated user.
     */
    public function testGetListExecTestWithUser()
    {
        /** @var oxUser|MockObject $oUser */
        $oUser = $this->getMock('oxUser', array('getId', 'getActiveCountry'));
        $oUser->expects($this->once())->method('getId')->will($this->returnValue('xxx'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('yyy'));

        /** @var oxDeliveryList|MockObject $oList */
        $oList = $this->getMock('oxDeliveryList', array('getUser', '_getFilterSelect', 'selectString', 'rewind'));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oList->expects($this->once())->method('_getFilterSelect');
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->once())->method('rewind');

        $oList->UNITgetList(null, null, null);
    }

    /**
     * Now simply testing if all expected functions are executed
     * testing without user
     */
    public function testGetListExecTestNoUser()
    {
        /** @var oxDeliveryList|MockObject $oList */
        $oList = $this->getMock('oxDeliveryList', array('getUser', 'selectString', 'rewind'));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue(null));
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->once())->method('rewind');

        // executing test
        $oList->UNITgetList(null, null, null);
    }

    /**
     * Testing getList - test getting list when user and country id specified.
     * Should use specified country id.
     */
    public function testGetListWithExistingUserAndCountryId()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('_testUserId');

        $oDList = new oxdeliverylistTest_forGetList();
        $oDList->getList($this->_oUser, 'a7c40f63264309e05.58576680', '_testDeliverySetId');

        //testing if getList calls _getFilterSelect() with correct params
        $this->assertEquals('_testUserId', $oDList->sFilterUser->getId());
        $this->assertEquals('a7c40f63264309e05.58576680', $oDList->sFilterCountryId); // luxemburg
        $this->assertEquals('_testDeliverySetId', $oDList->sFilterDeliverySet);

        $this->assertEquals(3, $oDList->count());
        $this->assertEquals(
            array('_testDeliveryId1', '_testDeliveryId2', '_testDeliveryId3'),
            array_keys($oDList->aList)
        );
    }

    /**
     * Testing getList - test caching same user
     */
    public function testGetListCaching()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('_testUserId');

        $oDList = new oxdeliverylistTest_forGetList();
        $oDList->getList($this->_oUser, null, '_testDeliverySetId');

        $this->assertEquals(3, $oDList->count());
        $this->assertEquals(
            array('_testDeliveryId1', '_testDeliveryId2', '_testDeliveryId3'),
            array_keys($oDList->aList)
        );

        //removing testing deliveries
        $this->cleanUpTable('oxdelivery');

        // testing if cache works
        $oDList->getList($this->_oUser, null, '_testDeliverySetId');
        $this->assertEquals(3, $oDList->count());
        $this->assertEquals(
            array('_testDeliveryId1', '_testDeliveryId2', '_testDeliveryId3'),
            array_keys($oDList->aList)
        );
    }

    /**
     * Testing getting delivery filter - default oxConfig country check
     */
    public function testGetFilterSelectWithoutUserAndCountryId()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oDList = new oxDeliveryListTestClass();

        $sTable = getViewName('oxdelivery');
        $sQ = "select $sTable.* from ( select distinct $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelid=$sTable.oxid where " . $oDList->getBaseObject()->getSqlActiveSnippet() . " and oxdel2delset.oxdelsetid = ''  order by $sTable.oxsort asc ) as $sTable where (
            select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' LIMIT 1),
                    0,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' LIMIT 1),
                    0,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxgroups' LIMIT 1),
                    0,
                    1)
            ) order by $sTable.oxsort asc ";

        $sTestSQ = $oDList->_getFilterSelect(null, null, null);

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = strtolower(preg_replace($aSearch, " ", $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, " ", $sTestSQ));

        $this->assertEquals($sQ, $sTestSQ);
    }

    /**
     * Testing getting delivery filter - no user, any country
     */
    public function testGetFilterSelectWitoutUserAndWithCountryId()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oDList = new oxDeliveryListTestClass();

        $sTable = getViewName('oxdelivery');
        $sQ = "select $sTable.* from ( select distinct $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelid=$sTable.oxid where " . $oDList->getBaseObject()->getSqlActiveSnippet() . " and oxdel2delset.oxdelsetid = ''  order by $sTable.oxsort asc ) as $sTable where (
            select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' and oxobject2delivery.OXOBJECTID='_testCountryId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' LIMIT 1),
                    0,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxgroups' LIMIT 1),
                    0,
                    1)
            ) order by $sTable.oxsort asc ";

        $sTestSQ = $oDList->_getFilterSelect(null, '_testCountryId', null);

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sQ, $sTestSQ);
    }

    /**
     * Testing getting delivery filter - with user and country id.
     * Also checks if correct user groups used
     */
    public function testGetFilterSelectWithUserAndCountryId()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oDList = new oxDeliveryListTestClass();
        // default oxConfig country check.
        $sTable = getViewName('oxdelivery');
        $sQ = "select $sTable.* from ( select distinct $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelid=$sTable.oxid where " . $oDList->getBaseObject()->getSqlActiveSnippet() . " and oxdel2delset.oxdelsetid = '_testDeliverySetId'  order by $sTable.oxsort asc ) as $sTable where (
            select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' and oxobject2delivery.OXOBJECTID='_testCountryId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' and oxobject2delivery.OXOBJECTID='_testUserId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxgroups' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxgroups' and oxobject2delivery.OXOBJECTID in ('oxidadmin') ),
                    1)
            ) order by $sTable.oxsort asc ";

        $sTestSQ = $oDList->_getFilterSelect($this->_oUser, '_testCountryId', '_testDeliverySetId');

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sQ, $sTestSQ);
    }

    /**
     * Testing getting delivery filter - with user (without groups) and country id.
     */
    public function testGetFilterSelectWithUserAndCountryIdAndWithoutGroups()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        //remove user from groups
        $this->cleanUpTable('oxobject2group');

        $oDList = new oxDeliveryListTestClass();
        // default oxConfig country check.
        $sTable = getViewName('oxdelivery');
        $sQ = "select $sTable.* from ( select distinct $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelid=$sTable.oxid where " . $oDList->getBaseObject()->getSqlActiveSnippet() . " and oxdel2delset.oxdelsetid = '_testDeliverySetId'  order by $sTable.oxsort asc ) as $sTable where (
            select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxcountry' and oxobject2delivery.OXOBJECTID='_testCountryId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxuser' and oxobject2delivery.OXOBJECTID='_testUserId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxgroups' LIMIT 1),
                    0,
                    1)
            ) order by $sTable.oxsort asc ";

        $sTestSQ = $oDList->_getFilterSelect($this->_oUser, '_testCountryId', '_testDeliverySetId');

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sQ, $sTestSQ);
    }

    /**
     * Testing delivery list loader functionality
     */
    public function testGetDeliveryList()
    {
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(3, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2', '_testDeliveryId1'),
            array_keys($aList)
        );
    }

    public function testGetDeliveryListIfFinalixedDeliviery()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load('_testDeliveryId2');
        $oDelivery->oxdelivery__oxfinalize = new oxField(1, oxField::T_RAW);
        $oDelivery->save();

        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(2, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2'),
            array_keys($aList)
        );
    }

    public function testGetDeliveryListFittingDeliveriesSets()
    {
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $oDList->setCollectFittingDeliveriesSets(true);
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(4, count($aList));
        $this->assertTrue(in_array('_testDeliverySetId', array_keys($aList)));
    }

    public function testGetDeliveryListNoDelFound()
    {
        $this->cleanUpTable('oxdelivery');
        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList(oxNew('oxBasket'), oxNew('oxuser'), 'somecountry', null);

        $this->assertEquals(0, count($aList));
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has articles assiged to delivery and basket has same article
     */
    public function testGetDeliveryListWithDeliveryArticlesThatAreInBasket()
    {
        // add article to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testArticleId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oObject2Delivery->save();

        // add same article to basket
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(3, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2', '_testDeliveryId1'),
            array_keys($aList)
        );
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has variants assiged to delivery and basket has same article
     * FS#1954
     */
    public function testGetDeliveryListWithDeliveryVariantsThatAreInBasket()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId1');
        $oArticle->oxarticles__oxparentid = new oxField('_testArticleId2', oxField::T_RAW);
        $oArticle->save();

        // add article to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testArticleId2', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oObject2Delivery->save();

        // add same article to basket
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(3, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2', '_testDeliveryId1'),
            array_keys($aList)
        );
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has articles assigned to delivery and same article is not in basket
     */
    public function testGetDeliveryListWithDeliveryArticlesThatAreNotInBasket()
    {
        // add article to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testArticleId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oObject2Delivery->save();

        // add different article to basket
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId2', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(2, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2'),
            array_keys($aList)
        );
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has categories assigned to delivery and basket has articles in that category
     */
    public function testGetDeliveryListWithDeliveryCategories()
    {
        // add category to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testCategoryId', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oObject2Delivery->save();

        // add same article to basket (belongs to category)
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $oDList->setCollectFittingDeliveriesSets(false);
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(3, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2', '_testDeliveryId1'),
            array_keys($aList)
        );
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has categories assiged to delivery and basket has articles in that category
     * plus also load other delivery for diff product
     */
    public function testGetDeliveryListWithDeliveryCategoryAndLoadOtherDelivForDiffArt()
    {
        // add category to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testCategoryId', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oObject2Delivery->save();
        $oObject2Delivery->setId('_testO2DelId2');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId2', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oObject2Delivery->save();
        $oObject2Delivery->setId('_testO2DelId3');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId3', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('112asd6', oxField::T_RAW);
        $oObject2Delivery->save();

        // add same article to basket (belongs to category)
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $_oBasketItem2 = oxNew("oxBasketItem");
        $_oBasketItem2->init('1126', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);
        $_oBasketItem2->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $_oBasketItem2;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $oDList->setCollectFittingDeliveriesSets(false);
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(2, count($aList));
        $this->assertEquals(
            array('_testDeliveryId2', '_testDeliveryId1'),
            array_keys($aList)
        );
    }

    /**
     * Testing delivery list loader functionality - if deliveries is loaded
     * when it has categories assiged to delivery and basket has not articles in that category
     */
    public function testGetDeliveryListWithDeliveryCategoriesAndBasketItemsNotInSameCategory()
    {
        //remove objects from categories
        $this->cleanUpTable('oxobject2category');

        // add category to delivery
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId('_testO2DelId1');
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField('_testDeliveryId1', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField('_testCategoryId', oxField::T_RAW);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oObject2Delivery->save();

        // add same article to basket (belongs to category)
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(2, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2'),
            array_keys($aList)
        );
    }

    /**
     * Testing deliveries loading according deliveries amount values
     */
    public function testGetDeliveryListAccordingAmount()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load('_testDeliveryId1');
        $oDelivery->oxdelivery__oxparamend = new oxField(1024, oxField::T_RAW);
        $oDelivery->save();

        // amount = 2 amounts of each item x price of 256 x 3 items in basket = 1536
        // so first delivery should be skipped

        // add same article to basket (belongs to category)
        $basketItem = oxNew("oxBasketItem");
        $basketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);
        $basketItem->setPrice($oPrice);

        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;
        $aBasketContents[] = $basketItem;

        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($aBasketContents));

        $oDList = oxNew("oxDeliveryList");
        $aList = $oDList->getDeliveryList($oBasket, null, null, '_testDeliverySetId');

        $this->assertEquals(2, count($aList));
        $this->assertEquals(
            array('_testDeliveryId3', '_testDeliveryId2'),
            array_keys($aList)
        );
    }

    /**
     * Testing oUser setter/getter
     */
    public function testSetGetUser()
    {
        $oUser = oxNew('oxUser');
        $oUser->setId('testUserId');

        $oDList = oxNew("oxDeliveryList");
        $oDList->setUser($oUser);
        $this->assertEquals('testUserId', $oDList->getUser()->getId());
    }

    /**
     * With special data and mysql 5.0.77
     */
    public function testGetListSpecialCase()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $sQ = "INSERT INTO `oxdelivery`
               (OXID, OXSHOPID, OXACTIVE, OXACTIVEFROM, OXACTIVETO, OXTITLE, OXTITLE_1, OXTITLE_2, OXTITLE_3, OXADDSUMTYPE, OXADDSUM, OXDELTYPE, OXPARAM, OXPARAMEND, OXFIXED, OXSORT, OXFINALIZE, OXTIMESTAMP)
               VALUES
               ('b763e957be61108f8.80080127', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test Bestellwert Inland unter � 40,00 = � 2,60', '', '', '', 'abs', 2.6, 'p', 10, 39.99, 0, 9999, 1, NOW()),
               ('3033e968fb5b30930.92732498', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test Bestellwert Inland �ber � 40,00 = portofrei', '', '', '', 'abs', 0, 'p', 40, 1000000, 0, 9999, 1, NOW()),
               ('a713e96c15c7bf3c7.45279281', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test Mindermengenzuschlag bis � 10,00 = � 3,50', '', '', '', 'abs', 3.5, 'p', 0, 9.99, 0, 9999, 1, NOW()),
               ('a713e96c1aeaefa75.74010807', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test Bestellwert europ. Ausland pauschal EURO 6,00', '', '', '', 'abs', 6, 'p', 0, 5000, 0, 9999, 1, NOW()),
               ('bdd46f9f2455153b9.22318118', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test Bestellwert au�ereurop. Ausland EURO 9,50', '', '', '', 'abs', 9.5, 'a', 0, 5000, 0, 9999, 1, NOW())";
        oxDb::getDb()->execute($sQ);
        $sQ = "INSERT INTO `oxdel2delset`
               (OXID, OXDELID, OXDELSETID, OXTIMESTAMP)
               VALUES
               ('b3b46b74d44224772.61045591', 'b763e957be61108f8.80080127', 'b3b46b74d3894f9f5.62965460', NOW()),
               ('87a46ff51e18cffb7.32142202', '3033e968fb5b30930.92732498', 'db046b85bd9ecca78.15075258', NOW()),
               ('87a46ff51e18cdc07.84474619', 'b763e957be61108f8.80080127', 'db046b85bd9ecca78.15075258', NOW()),
               ('87046fd251e929865.64580766', 'a713e96c15c7bf3c7.45279281', 'b3b46b74d3894f9f5.62965460', NOW()),
               ('b3b46b74d44226de8.09907681', '3033e968fb5b30930.92732498', 'b3b46b74d3894f9f5.62965460', NOW()),
               ('87a46ff51e18d1b15.20021730', 'a713e96c15c7bf3c7.45279281', 'db046b85bd9ecca78.15075258', NOW()),
               ('84747302b831b36c9.47406525', 'bdd46f9f2455153b9.22318118', '00c47010695b17720.89704467', NOW()),
               ('00c470107507b6fd5.42311521', 'a713e96c1aeaefa75.74010807', '00c4701074960ca97.47102377', NOW())";
        oxDb::getDb()->execute($sQ);
        $sQ = "INSERT INTO `oxdeliveryset`
               (OXID, OXSHOPID, OXACTIVE, OXACTIVEFROM, OXACTIVETO, OXTITLE, OXTITLE_1, OXTITLE_2, OXTITLE_3, OXPOS, OXTIMESTAMP)
               VALUES
               ('b3b46b74d3894f9f5.62965460', ".ShopIdCalculator::BASE_SHOP_ID.", 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Test DHL/DPD Inland', 'DHL/DPD Inland', '', '', 0, NOW())";
        oxDb::getDb()->execute($sQ);
        $sQ = "INSERT INTO `oxobject2delivery`
               (OXID, OXDELIVERYID, OXOBJECTID, OXTYPE, OXTIMESTAMP)
               VALUES
               ('b3b46b74d10909465.50250935', '3033e968fb5b30930.92732498', 'a7c40f631fc920687.20179984', 'oxcountry', NOW()),
               ('bdd46f9f27a6759a1.51238581', 'bdd46f9f2455153b9.22318118', '8f241f110962e40e6.75062153', 'oxcountry', NOW()),
               ('87046fd23d581ed04.18664580', 'b763e957be61108f8.80080127', 'a7c40f631fc920687.20179984', 'oxcountry', NOW()),
               ('bb346bb5318166468.44951132', 'a713e96c1aeaefa75.74010807', 'a7c40f632e04633c9.47194042', 'oxcountry', NOW())";
        oxDb::getDb()->execute($sQ);
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $oDList = new oxDeliveryListTest_forGetList();
        $oDList->getList($oUser, 'a7c40f631fc920687.20179984', 'b3b46b74d3894f9f5.62965460');

        //testing if getList calls _getFilterSelect() with correct params
        $this->assertEquals('oxdefaultadmin', $oDList->sFilterUser->getId());
        $this->assertEquals('a7c40f631fc920687.20179984', $oDList->sFilterCountryId); // luxemburg
        $this->assertEquals('b3b46b74d3894f9f5.62965460', $oDList->sFilterDeliverySet);

        $this->assertEquals(3, $oDList->count());
        $this->assertEquals(
            array('_testDeliveryId1', '_testDeliveryId2', '_testDeliveryId3'),
            array_keys($oDList->aList)
        );
    }

    /**
     * Testing loadDeliveryListForProduct
     */
    public function testLoadDeliveryListForProduct()
    {
        $oDList = oxNew("oxDeliveryList");
        $oDList->loadDeliveryListForProduct($this->_aTestProducts[0]);
        $this->assertEquals(7, $oDList->count());
    }
}
