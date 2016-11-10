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

use OxidEsales\EshopCommunity\Application\Model\DeliverySet;

use \oxDeliverySetList;
use \oxDb;
use \oxPaymentList;
use \oxField;
use \oxRegistry;
use \oxTestModules;

class modOxDeliverySetList extends oxDeliverySetList
{

    public function getObjectsInListName()
    {
        return $this->_sObjectsInListName;
    }
}

class oxDb_noActiveSnippetInDeliverySetList extends oxDb
{

    public function getActiveSnippet($param1, $param3 = null)
    {
        return '1';
    }
}

class modOxDeliverySetList_paymentList extends oxPaymentList
{

    public static $dBasketPrice = null;

    public function getPaymentList($sShipSetId, $dBasketPrice, $oUser = null)
    {
        self::$dBasketPrice = $dBasketPrice;

        return parent::getPaymentList($sShipSetId, $dBasketPrice, $oUser);
    }
}

class DeliverysetListTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxAddClassModule('modOxDeliverySetList', 'oxDeliverySetList');

        //set default user
        $this->_oUser = oxNew('oxuser');
        $this->_oUser->setId('_testUserId');
        $this->_oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oUser->save();

        //add user addres
        $oAdress = oxNew('oxbase');
        $oAdress->init('oxaddress');
        $oAdress->setId('_testAddressId');
        $oAdress->oxaddress__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);
        $oAdress->oxaddress__oxaddressuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);
        $oAdress->oxaddress__oxcountryid = new oxField('a7c40f6323c4bfb36.59919433', oxField::T_RAW); //italien
        $oAdress->save();
        $this->getSession()->setVariable('deladrid', '_testAddressId');
        modOxDeliverySetList_paymentList::$dBasketPrice = null;
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule('oxDb_noActiveSnippetInDeliveryList');
        oxRemClassModule('modOxDeliverySetList');

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxaddress');
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxdel2delset');

        oxRegistry::getSession()->deleteVariable('deladrid');

        parent::tearDown();
    }

    /**
     * Test for bug entry #0001615
     *
     * @return null
     */
    public function testForBugEntry0001615()
    {
        $this->getConfig()->setActShopCurrency(2);
        $this->cleanUpTable('oxaddress');

        $sShipSet = "oxidstandard";
        $sProductId = "1126";
        $dAmount = 29410;

        $oUser = $this->getMock("oxUser", array("getActiveCountry"));
        $oUser->expects($this->any())->method('getActiveCountry')->will($this->returnValue("a7c40f631fc920687.20179984"));
        $oUser->load("oxdefaultadmin");

        $oBasket = oxNew('oxbasket');
        $oBasket->addToBasket($sProductId, $dAmount);
        $oBasket->calculateBasket();

        $oDelSetList = oxNew('oxDeliverySetList');
        list(, , $aPaymentList) = $oDelSetList->getDeliverySetData($sShipSet, $oUser, $oBasket);
        $this->assertTrue(count($aPaymentList) > 0);
    }

    /**
     * Testing if delivery set list will be build even some data is wrong
     */
    public function testGetDeliverySetListWithSomeWrongData()
    {
        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oDelSetList = oxNew('oxDeliverySetList');
        $oDelSetList = $oDelSetList->getDeliverySetList($oUser, $oUser->oxuser__oxcountryid->value);
        $iListCOunt = count($oDelSetList);

        // list must contain at least one item
        $this->assertTrue($iListCOunt > 0);

        $oDeliverySet = current($oDelSetList);

        // adding garbage
        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery1");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDeliverySet->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxdelset");
        $oGarbage->save();

        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery2");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDeliverySet->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxdelsetu");
        $oGarbage->save();

        $oGarbage = oxNew('oxbase');
        $oGarbage->init("oxobject2delivery");
        $oGarbage->setId("_testoxobject2delivery3");
        $oGarbage->oxobject2delivery__oxdeliveryid = new oxField($oDeliverySet->getId());
        $oGarbage->oxobject2delivery__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2delivery__oxtype = new oxField("oxdelsetg");
        $oGarbage->save();

        $oDelSetList = oxNew('oxDeliverySetList');
        $oDelSetList = $oDelSetList->getDeliverySetList($oUser, $oUser->oxuser__oxcountryid->value);
        $iNewListCount = count($oDelSetList);

        // list must contain at least one item
        $this->assertTrue($iNewListCount > 0);
        $this->assertTrue($iNewListCount === $iListCOunt);

        $blFound = false;
        foreach ($oDelSetList as $oDelSet) {
            if ($oDeliverySet->getId() == $oDelSet->getId()) {
                $blFound = true;
                break;
            }
        }
        $this->assertTrue($blFound, "Error, delivery set not found");
    }

    /**
     * Test case
     * Article 1 => Deliverycost 1 => Deliveryset 1
     * Article 1 & Article 2 => Deliverycost 2 => Deliveryset 2
     *
     * Article 1 in basket => order process "step 3" => only Deliveryset 1 available => right
     * Article 2 in basket => order process "step 3" => only Deliveryset 2 available => right
     *
     * Article 1 & Article 2 in basket => order process "step 3" => Deliveryset 1 and Deliveryset 2 available => wrong, there should only be deliveryset 2
     */
    public function testGetDeliverySetListForTestCase()
    {
        $iActShop = $this->getConfig()->getBaseShopId();
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);

        /**
         * Preparing data
         */

        // Deliverycost 1
        $oDel1 = oxNew('oxDelivery');
        $oDel1->setId('_testdelivery1');
        $oDel1->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDel1->oxdelivery__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDel1->oxdelivery__oxtitle = new oxField('Test delivery1', oxField::T_RAW);
        $oDel1->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDel1->oxdelivery__oxaddsum = new oxField('100', oxField::T_RAW);
        $oDel1->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDel1->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDel1->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDel1->oxdelivery__oxsort = new oxField(2, oxField::T_RAW);
        $oDel1->save();

        // Deliverycost 2
        $oDel2 = oxNew('oxDelivery');
        $oDel2->setId('_testdelivery2');
        $oDel2->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDel2->oxdelivery__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDel2->oxdelivery__oxtitle = new oxField('Test delivery2', oxField::T_RAW);
        $oDel2->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDel2->oxdelivery__oxaddsum = new oxField('100', oxField::T_RAW);
        $oDel2->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDel2->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDel2->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDel2->oxdelivery__oxsort = new oxField(1, oxField::T_RAW);
        $oDel2->save();

        // Deliveryset 1
        $oDelSet1 = oxNew('oxDeliverySet');
        $oDelSet1->setId('_testdeliveryset1');
        $oDelSet1->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet1->oxdeliveryset__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDelSet1->oxdeliveryset__oxtitle = new oxField('Test deliveryset1', oxField::T_RAW);
        $oDelSet1->oxdeliveryset__oxpos = new oxField(2, oxField::T_RAW);
        $oDelSet1->save();

        // Deliveryset 2
        $oDelSet2 = oxNew('oxDeliverySet');
        $oDelSet2->setId('_testdeliveryset2');
        $oDelSet2->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet2->oxdeliveryset__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDelSet2->oxdeliveryset__oxtitle = new oxField('Test deliveryset2', oxField::T_RAW);
        $oDelSet2->oxdeliveryset__oxpos = new oxField(1, oxField::T_RAW);
        $oDelSet2->save();

        // Article 1 => Deliverycost 1
        $oO2D1 = oxNew('oxbase');
        $oO2D1->init('oxobject2delivery');
        $oO2D1->setId('_testoxobject2delivery1');
        $oO2D1->oxobject2delivery__oxdeliveryid = new oxField($oDel1->getId(), oxField::T_RAW);
        $oO2D1->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oO2D1->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D1->save();

        // Article 1 & Article 2 => Deliverycost 2
        $oO2D2 = oxNew('oxbase');
        $oO2D2->init('oxobject2delivery');
        $oO2D2->setId('_testoxobject2delivery2');
        $oO2D2->oxobject2delivery__oxdeliveryid = new oxField($oDel2->getId(), oxField::T_RAW);
        $oO2D2->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oO2D2->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D2->save();

        $oO2D3 = oxNew('oxbase');
        $oO2D3->init('oxobject2delivery');
        $oO2D3->setId('_testoxobject2delivery2');
        $oO2D3->oxobject2delivery__oxdeliveryid = new oxField($oDel2->getId(), oxField::T_RAW);
        $oO2D3->oxobject2delivery__oxobjectid = new oxField('1127', oxField::T_RAW);
        $oO2D3->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D3->save();

        // Deliverycost 1 => Deliveryset 1
        $oD2DelSet1 = oxNew('oxbase');
        $oD2DelSet1->init('oxdel2delset');
        $oD2DelSet1->setId('_testoxdel2delset1');
        $oD2DelSet1->oxdel2delset__oxdelid = new oxField($oDel1->getId(), oxField::T_RAW);
        $oD2DelSet1->oxdel2delset__oxdelsetid = new oxField($oDelSet1->getId(), oxField::T_RAW);
        $oD2DelSet1->save();

        // Deliverycost 2 => Deliveryset 2
        $oD2DelSet2 = oxNew('oxbase');
        $oD2DelSet2->init('oxdel2delset');
        $oD2DelSet2->setId('_testoxdel2delset2');
        $oD2DelSet2->oxdel2delset__oxdelid = new oxField($oDel2->getId(), oxField::T_RAW);
        $oD2DelSet2->oxdel2delset__oxdelsetid = new oxField($oDelSet2->getId(), oxField::T_RAW);
        $oD2DelSet2->save();

        // payment => Deliveryset 1
        $oP2DelSet1 = oxNew('oxbase');
        $oP2DelSet1->init('oxobject2payment');
        $oP2DelSet1->oxobject2payment__oxpaymentid = new oxField('oxidcashondel', oxField::T_RAW);
        $oP2DelSet1->oxobject2payment__oxobjectid = new oxField($oDelSet1->getId(), oxField::T_RAW);
        $oP2DelSet1->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oP2DelSet1->save();

        // payment => Deliveryset 2
        $oP2DelSet2 = oxNew('oxbase');
        $oP2DelSet2->init('oxobject2payment');
        $oP2DelSet2->oxobject2payment__oxpaymentid = new oxField('oxidpayadvance', oxField::T_RAW);
        $oP2DelSet2->oxobject2payment__oxobjectid = new oxField($oDelSet2->getId(), oxField::T_RAW);
        $oP2DelSet2->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oP2DelSet2->save();

        /**
         * Preparing input
         */

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        oxRegistry::getSession()->deleteVariable('deladrid');

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $oBasket->setBasketUser($oUser);
        $oBasket->addToBasket('1127', 1);
        $oBasket->calculateBasket();

        /**
         * Testing
         */
        /**
         * act. delivery set must be "Standard Germany" ('oxidstandard')
         */
        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals('oxidstandard', $sActShipSet);

        /**
         * changing user country to Swiss
         * act. delivery set mustl be "Standard international" ('1b842e737735469e0.79687388')
         */
        $oUser->oxuser__oxcountryid = new oxField('a7c40f6321c6f6109.43859248', oxField::T_RAW);
        $oBasket->addToBasket('1126', 1);
        $oBasket->calculateBasket();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals('oxidstandard', $sActShipSet);

        /**
         * changing amounts
         * act. delivery set mustl be "Test deliveryset1" ('_testdeliveryset1')
         */
        $oBasket->addToBasket('1126', 9);
        $oBasket->addToBasket('1127', 0, array(), null, true);
        $oBasket->calculateBasket();

        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);
        $oBasket->setBasketUser($oUser);

        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals('_testdeliveryset1', $sActShipSet);

        /**
         * changing amounts
         * act. delivery set must be "Test deliveryset2" ('_testdeliveryset2')
         */
        $oBasket->addToBasket('1127', 10);
        $oBasket->calculateBasket();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals('_testdeliveryset2', $sActShipSet);

        /**
         * changing sorting
         * changing amounts
         * act. delivery set must be "Test deliveryset2" ('_testdeliveryset2')
         */
        $oDel1->oxdelivery__oxsort = new oxField(1, oxField::T_RAW);
        $oDel1->save();
        $oDel2->oxdelivery__oxsort = new oxField(2, oxField::T_RAW);
        $oDel2->save();

        $oBasket->addToBasket('1126', 0, array(), null, true);
        $oBasket->calculateBasket();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals('_testdeliveryset2', $sActShipSet);

        // if user is not set
        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData(null, null, $oBasket);
        $this->assertNull($sActShipSet);

        // if shipset selected
        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData('_testdeliveryset2', $oUser, $oBasket);
        $this->assertEquals('_testdeliveryset2', $sActShipSet);

        // if wrong shipset selected
        $oDeliverySetList = oxNew('oxDeliverySetList');
        list(, $sActShipSet,) = $oDeliverySetList->getDeliverySetData('someshipset', $oUser, $oBasket);
        $this->assertEquals('_testdeliveryset2', $sActShipSet);
    }

    /**
     * Testing if constructor created correct object structure
     */
    public function testOxDeliverySetList()
    {
        $oList = $this->getMock('oxDeliverySetList', array('setHomeCountry'));
        $oList->expects($this->once())->method('setHomeCountry');
        $oList->__construct();

        // checking object type
        $this->assertTrue($oList->getBaseObject() instanceof deliveryset);
    }

    /**
     * Testing _getList - test getting list when country id is not setted.
     * Checking if _getFilterSelect() is called with correct params.
     */
    public function testGetListWithoutCountryId()
    {
        $oDelSetList = $this->getMock('oxDeliverySetList', array('_getFilterSelect'));
        $oDelSetList->setHomeCountry(array('_testHomeCountryId'));

        $oDelSetList->expects($this->any())
            ->method('_getFilterSelect')
            ->will($this->returnValue('SELECT 1'))
            ->with(null, '_testHomeCountryId');

        $oDelSetList->UNITgetList(null, null);
    }

    /**
     * Testing _getList - test getting list when user not setted.
     * Checking if _getFilterSelect() is called with correct
     * params - with setted user and user country id
     */
    public function testGetListWithoutUser()
    {
        $oDelSetList = $this->getMock('oxDeliverySetList', array('_getFilterSelect'));
        $oDelSetList->setHomeCountry(array('_testHomeCountryId'));

        $oDelSetList->expects($this->any())
            ->method('_getFilterSelect')
            ->will($this->returnValue('SELECT 1'))
            ->with($this->_oUser, 'a7c40f6323c4bfb36.59919433');

        $oDelSetList->setUser($this->_oUser);
        $oDelSetList->UNITgetList(null, null);
    }

    /**
     * Testing _getList - test getting list when user and country id are setted.
     * Checking if _getFilterSelect() is called with correct params
     */
    public function testGetListWithUserAndCountryId()
    {
        $oDelSetList = $this->getMock('oxDeliverySetList', array('_getFilterSelect'));
        $oDelSetList->setHomeCountry(array('_testHomeCountryId'));

        $oDelSetList->expects($this->any())
            ->method('_getFilterSelect')
            ->will($this->returnValue('SELECT 1'))
            ->with($this->_oUser, '_testHomeCountryId');

        $oDelSetList->UNITgetList($this->_oUser, '_testHomeCountryId');
    }

    /**
     * Testing code execution
     */
    // when user is not passed and does not exist in session
    public function testgetListCodeExecNoUser()
    {
        $oList = $this->getMock('oxDeliverySetList', array('getUser', 'setUser', '_getFilterSelect', 'selectString', 'rewind'));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue(null));
        $oList->expects($this->never())->method('setUser');
        $oList->expects($this->once())->method('_getFilterSelect');
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->once())->method('rewind');

        // testing
        $oList->UNITgetList(null, null);

    }

    // when user is passed by param
    public function testgetListCodeExecUserIsTakenFromSession()
    {
        $oUser = $this->getMock('oxuser', array('getId', 'getActiveCountry'));
        $oUser->expects($this->once())->method('getId')->will($this->returnValue('xxx'));;
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('yyy'));

        $oList = $this->getMock('oxDeliverySetList', array('getUser', 'setUser', '_getFilterSelect', 'selectString', 'rewind'));
        $oList->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oList->expects($this->never())->method('setUser');
        $oList->expects($this->once())->method('_getFilterSelect')->with($oUser, 'yyy');
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->once())->method('rewind');

        // testing
        $oList->UNITgetList(null, null);
    }

    // when user and country ar set
    public function testgetListCountryAndUserAreSet()
    {
        $oList = $this->getMock('oxDeliverySetList', array('getUser', 'setUser', '_getFilterSelect', 'selectString', 'rewind'));
        $oList->expects($this->never())->method('getUser');
        $oList->expects($this->exactly(2))->method('setUser');
        $oList->expects($this->once())->method('_getFilterSelect')->will($this->returnValue('SELECT 1'))->with($this->_oUser, '_testHomeCountryId');
        $oList->expects($this->once())->method('selectString');
        $oList->expects($this->exactly(2))->method('rewind');

        // testing
        $oList->setHomeCountry(array('_testHomeCountryId'));
        $oList->UNITgetList($this->_oUser, '_testHomeCountryId');
        $oList->UNITgetList($this->_oUser, '_testHomeCountryId');
    }

    // when user and country ar set
    public function testgetListCountryIsChanged()
    {
        $oList = $this->getMock('oxDeliverySetList', array('getUser', 'setUser', '_getFilterSelect', 'selectString', 'rewind'));
        $oList->expects($this->never())->method('getUser');
        $oList->expects($this->exactly(2))->method('setUser');
        $oList->expects($this->exactly(2))->method('_getFilterSelect')->will($this->returnValue('SELECT 1'));
        $oList->expects($this->exactly(2))->method('selectString');
        $oList->expects($this->exactly(2))->method('rewind');

        // testing
        $oList->UNITgetList($this->_oUser, '_testHomeCountryId');
        $oList->UNITgetList($this->_oUser, 'a7c40f6323c4bfb36.59919433');
    }

    /**
     * Testing results.
     * No input, everything should be loaded according to config defaults.
     */
    public function testGetListTestingResultsNoInput()
    {
        $oList = oxNew('oxDeliverySetList');
        $oList->UNITgetList(null, null);

        $this->assertEquals(3, count($oList->aList));
        $this->assertArrayHasKey('oxidstandard', $oList->aList);
        $this->assertArrayHasKey('1b842e732a23255b1.91207750', $oList->aList);
        $this->assertArrayHasKey('1b842e732a23255b1.91207751', $oList->aList);
    }

    // only country
    public function testGetListTestingResultsPassingOnlyCountryId()
    {
        $oList = oxNew('oxDeliverySetList');
        $oList->UNITgetList(null, 'a7c40f6320aeb2ec2.72885259'); // austria

        $this->assertEquals(3, count($oList->aList));
        $this->assertArrayHasKey('oxidstandard', $oList->aList);
        $this->assertArrayHasKey('1b842e732a23255b1.91207750', $oList->aList);
        $this->assertArrayHasKey('1b842e732a23255b1.91207751', $oList->aList);
    }

    /**
     * Testing if SQLs returned by this method are fine - testing sql query string when no user is set
     */
    public function testGetFilterSelectWithoutUser()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oList = oxNew('oxDeliverySetList');
        $sQ = $oList->UNITgetFilterSelect(null, null);

        $sTable = getViewName('oxdeliveryset');
        $sTestSQ = "select $sTable.* from $sTable where " . $oList->getBaseObject()->getSqlActiveSnippet() . " and (
                select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' LIMIT 1),
                    0,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' LIMIT 1),
                    0,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' LIMIT 1),
                    0,
                    1)
            ) order by $sTable.oxpos";

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sTestSQ, $sQ);
    }

    /**
     * Testing if SQLs returned by this method are fine - testing sql query string when user is set
     */
    public function testGetFilterSelectWithUser()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oList = oxNew('oxDeliverySetList');
        $sQ = $oList->UNITgetFilterSelect($this->_oUser, '_testCoutntryId');

        $sTable = getViewName('oxdeliveryset');
        $sTestSQ = "select $sTable.* from $sTable where " . $oList->getBaseObject()->getSqlActiveSnippet() . " and (
                select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' and oxobject2delivery.OXOBJECTID='_testCoutntryId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' and oxobject2delivery.OXOBJECTID='_testUserId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' LIMIT 1),
                    0,
                    1)
            ) order by $sTable.oxpos";

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sTestSQ, $sQ);
    }

    /**
     * Testing if SQLs returned by this method are fine - testing sql query string when user is set and is in group
     */
    public function testGetFilterSelectWithUserInGroups()
    {
        $this->setTime(0);
        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        //create some groups array
        $oGroup = oxNew('oxGroups');
        $oGroup->setId('10');
        $aGroups[] = $oGroup;

        $oGroup = oxNew('oxGroups');
        $oGroup->setId('25');
        $aGroups[] = $oGroup;

        $oUser = $this->getMock('oxUser', array('getUserGroups', 'getId'));

        $oUser->expects($this->any())
            ->method('getUserGroups')
            ->will(
                $this->returnValue($aGroups)
            );

        $oUser->expects($this->any())
            ->method('getId')
            ->will(
                $this->returnValue('_testUserId')
            );

        $sQ = oxRegistry::get("oxDeliverySetList")->UNITgetFilterSelect($oUser, '_testCoutntryId');

        $sTable = getViewName('oxdeliveryset');
        $sTestSQ = "select $sTable.* from $sTable where " . oxRegistry::get("oxDeliverySetList")->getBaseObject()->getSqlActiveSnippet() . " and (
                select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' and oxobject2delivery.OXOBJECTID='_testCoutntryId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' and oxobject2delivery.OXOBJECTID='_testUserId'),
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' LIMIT 1),
                    EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' and oxobject2delivery.OXOBJECTID in ('10', '25') ),
                    1)
            ) order by $sTable.oxpos";

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $aReplace = array(" ", " ", " ", " ");
        $sQ = strtolower(preg_replace($aSearch, $aReplace, $sQ));
        $sTestSQ = strtolower(preg_replace($aSearch, $aReplace, $sTestSQ));

        $this->assertEquals($sTestSQ, $sQ);
    }

    /**
     * Testing if delivery set list getter correctly exchanges primary delivery set
     */
    public function test_getDeliverySetList()
    {
        // inserting delivery set
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_testDeliverySetId1');
        $oDelSet->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet->save();

        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_testDeliverySetId2');
        $oDelSet->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet->save();

        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_testDeliverySetId3');
        $oDelSet->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet->save();


        $oDelSetList = oxNew('oxDeliverySetList');
        $aList = $oDelSetList->getDeliverySetList(null, null, '_testDeliverySetId2');
        reset($aList);
        $oItem = current($aList);

        $this->assertTrue(count($aList) > 1);
        $this->assertEquals('_testDeliverySetId2', $oItem->getId());

    }

    /**
     * Testing oUser setter/getter
     */
    public function testSetGetUser()
    {
        $oUser = oxNew('oxuser');
        $oUser->setId('oLiaLiaMergaite');

        $oDelSetList = oxNew("oxDeliverySetList");
        $oDelSetList->setUser($oUser);
        $this->assertEquals('oLiaLiaMergaite', $oDelSetList->getUser()->getId());
    }

    /**
     * Testing home country setter
     */
    public function testSetHomeCountry()
    {
        $oList = $this->getProxyClass('oxDeliverySetList');
        $oList->setHomeCountry(array('something'));
        $this->assertEquals('something', $oList->getNonPublicVar('_sHomeCountry'));
    }

    public function testSetHomeCountryIfNotArray()
    {
        $oList = $this->getProxyClass('oxDeliverySetList');
        $oList->setHomeCountry('something');
        $this->assertEquals('something', $oList->getNonPublicVar('_sHomeCountry'));
    }


    /**
     * Test if method for getting payments list uses basket price without payment costs
     */
    public function testGetDeliverySetData_usesBasketPriceWithoutPayment()
    {
        $iActShop = $this->getConfig()->getBaseShopId();

        // Deliverycost 1
        $oDel1 = oxNew('oxDelivery');
        $oDel1->setId('_testdelivery1');
        $oDel1->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDel1->oxdelivery__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDel1->save();

        // Deliveryset 1
        $oDelSet1 = oxNew('oxDeliverySet');
        $oDelSet1->setId('_testdeliveryset1');
        $oDelSet1->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $oDelSet1->oxdeliveryset__oxshopid = new oxField($iActShop, oxField::T_RAW);
        $oDelSet1->save();

        // Article 1 => Deliverycost 1
        $oO2D1 = oxNew('oxbase');
        $oO2D1->init('oxobject2delivery');
        $oO2D1->setId('_testoxobject2delivery1');
        $oO2D1->oxobject2delivery__oxdeliveryid = new oxField($oDel1->getId(), oxField::T_RAW);
        $oO2D1->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oO2D1->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D1->save();

        // Deliverycost 1 => Deliveryset 1
        $oD2DelSet1 = oxNew('oxbase');
        $oD2DelSet1->init('oxdel2delset');
        $oD2DelSet1->setId('_testoxdel2delset1');
        $oD2DelSet1->oxdel2delset__oxdelid = new oxField($oDel1->getId(), oxField::T_RAW);
        $oD2DelSet1->oxdel2delset__oxdelsetid = new oxField($oDelSet1->getId(), oxField::T_RAW);
        $oD2DelSet1->save();

        // payment => Deliveryset 1
        $oP2DelSet1 = oxNew('oxbase');
        $oP2DelSet1->init('oxobject2payment');
        $oP2DelSet1->oxobject2payment__oxpaymentid = new oxField('oxidcashondel', oxField::T_RAW);
        $oP2DelSet1->oxobject2payment__oxobjectid = new oxField($oDelSet1->getId(), oxField::T_RAW);
        $oP2DelSet1->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oP2DelSet1->save();

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');
        $this->setRequestParameter('deladrid', null);

        $oBasket = $this->getMock('oxBasket', array('getPriceForPayment'));
        $oBasket->expects($this->once())->method('getPriceForPayment')->will($this->returnValue(100));

        oxAddClassModule('Unit\Application\Model\modOxDeliverySetList_paymentList', 'oxPaymentList');

        $oDeliverySetList = oxNew('oxDeliverySetList');

        $oDeliverySetList->getDeliverySetData(null, $oUser, $oBasket);
        $this->assertEquals(100, modOxDeliverySetList_paymentList::$dBasketPrice);
    }

    /**
     * Testing oxdeliverysetlist::loadNonRDFaDeliverySetList()
     */
    public function testLoadNonRDFaDeliverySetList()
    {
        $oP2DelSet1 = oxNew('oxbase');
        $oP2DelSet1->init('oxobject2delivery');
        $oP2DelSet1->setId('_testoxobject2delivery1');
        $oP2DelSet1->oxobject2delivery__oxdeliveryid = new oxField('oxidstandard', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxobjectid = new oxField('DHL', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxtype = new oxField("rdfadeliveryset", oxField::T_RAW);
        $oP2DelSet1->save();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        $oDeliverySetList->loadNonRDFaDeliverySetList();
        $this->assertEquals(2, $oDeliverySetList->count());
    }

    /**
     * Testing oxdeliverysetlist::loadRDFaDeliverySetList()
     */
    public function testLoadRDFaDeliverySetList()
    {
        $oP2DelSet1 = oxNew('oxbase');
        $oP2DelSet1->init('oxobject2delivery');
        $oP2DelSet1->setId('_testoxobject2delivery1');
        $oP2DelSet1->oxobject2delivery__oxdeliveryid = new oxField('oxidstandard', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxobjectid = new oxField('DHL', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxtype = new oxField("rdfadeliveryset", oxField::T_RAW);
        $oP2DelSet1->save();

        $oDeliverySet = oxNew('oxDeliverySet');
        $oDeliverySet->load('_oxidstandard');
        $oDeliverySet->oxdeliveryset__oxactive = new oxField(0, oxField::T_RAW);
        $oDeliverySet->save();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        $oDeliverySetList->loadRDFaDeliverySetList();
        $this->assertEquals(3, $oDeliverySetList->count());
        foreach ($oDeliverySetList as $oDel) {
            if ($oDel->getId() == 'oxidstandard') {
                $this->assertEquals('DHL', $oDel->oxdeliveryset__oxobjectid->value);
            } else {
                $this->assertNull($oDel->oxdeliveryset__oxobjectid->value);
            }
        }
    }

    /**
     * Testing oxdeliverysetlist::loadRDFaDeliverySetList()
     */
    public function testLoadRDFaDeliverySetListForDeliveryId()
    {
        $oP2DelSet1 = oxNew('oxbase');
        $oP2DelSet1->init('oxobject2delivery');
        $oP2DelSet1->setId('_testoxobject2delivery1');
        $oP2DelSet1->oxobject2delivery__oxdeliveryid = new oxField('oxidstandard', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxobjectid = new oxField('DHL', oxField::T_RAW);
        $oP2DelSet1->oxobject2delivery__oxtype = new oxField("rdfadeliveryset", oxField::T_RAW);
        $oP2DelSet1->save();

        // Deliverycost 1 => Deliveryset 1
        $oD2DelSet1 = oxNew('oxbase');
        $oD2DelSet1->init('oxdel2delset');
        $oD2DelSet1->setId('_testoxdel2delset1');
        $oD2DelSet1->oxdel2delset__oxdelid = new oxField('1b842e73470578914.54719298', oxField::T_RAW);
        $oD2DelSet1->oxdel2delset__oxdelsetid = new oxField('1b842e732a23255b1.91207750', oxField::T_RAW);
        $oD2DelSet1->save();

        $oDeliverySetList = oxNew('oxDeliverySetList');
        // standart delivery costs for DE
        $oDeliverySetList->loadRDFaDeliverySetList('1b842e73470578914.54719298');
        $this->assertEquals(2, $oDeliverySetList->count());
        foreach ($oDeliverySetList as $oDel) {
            if ($oDel->getId() == 'oxidstandard') {
                $this->assertEquals('DHL', $oDel->oxdeliveryset__oxobjectid->value);
            } else {
                $this->assertNull($oDel->oxdeliveryset__oxobjectid->value);
            }
        }
    }

}
