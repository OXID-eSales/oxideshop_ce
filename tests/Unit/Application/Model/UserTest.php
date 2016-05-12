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

use oxEmailHelper;
use \oxnewssubscribed;
use oxUser;
use oxUtilsObject;
use \oxUtilsServer;
use \oxField;
use \oxInputException;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxEmailHelper.php';

/**
 * Mocks loadFromUserID and loadFromEMail in oxNewsSubscribed class.
 */
class UserTest_oxNewsSubscribed extends oxnewssubscribed
{
    public $loadFromUserID;
    public $loadFromEMail;

    public function loadFromUserID($sOXID)
    {
        if ($sOXID == 'oxid') {
            $this->loadFromUserID = true;
        }
    }

    public function loadFromEMail($sEmail)
    {
        if ($sEmail == 'email') {
            $this->loadFromEMail = true;
        }
    }
}

/**
 * Mocks getOxCookie in oxUtilsServer class.
 */
class UserTest_oxUtilsServerHelper extends oxUtilsServer
{

    public function getOxCookie($sName = null)
    {
        return true;
    }
}

/**
 * Mocks setOxCookie, getOxCookie and delOxCookie in oxUtilsServer class.
 */
class UserTest_oxUtilsServerHelper2 extends oxUtilsServer
{

    /**
     * $_COOKIE alternative for testing
     *
     * @var array
     */
    protected $_aCookieVars = array();

    public function setOxCookie($sVar, $sVal = "", $iExpire = 0, $sPath = '/', $sDomain = null, $blToSession = true, $blSecure = false, $blHttpOnly = true)
    {
        //unsetting cookie
        if (!isset($sVar) && !isset($sVal)) {
            $this->_aCookieVars = null;

            return;
        }

        $this->_aCookieVars[$sVar] = $sVal;
    }

    public function getOxCookie($sVar = null)
    {
        if (!$sVar) {
            return $this->_aCookieVars;
        }

        if ($this->_aCookieVars[$sVar]) {
            return $this->_aCookieVars[$sVar];
        }

        return null;
    }

    public function delOxCookie()
    {
        $this->_aCookieVars = null;
    }
}

/**
 * Testing oxuser class
 */
class UserTest extends \OxidTestCase
{

    protected $_aShops = array(1);
    protected $_aUsers = array();

    protected $_aDynPaymentFields = array('kktype'   => 'Test Bank',
                                          'kknumber' => '123456',
                                          'kkmonth'  => '123456',
                                          'kkyear'   => 'Test User',
                                          'kkname'   => 'Test Name',
                                          'kkpruef'  => '123456');

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oUser = oxNew('oxUser');
        if ($oUser->loadActiveUser()) {
            $oUser->logout();
        }
        $oUser->setAdminMode(null);
        oxRegistry::getSession()->deleteVariable('deladrid');

        $this->getSession()->setVariable('usr', null);
        $this->getSession()->setVariable('auth', null);

        // resetting globally admin mode

        // removing email wrapper module
        oxRemClassModule('Unit\Application\Model\UserTest_oxNewsSubscribed');
        oxRemClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper');
        oxRemClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper2');
        oxRemClassModule('Unit\Application\Model\oxEmailHelper');

        // removing users
        foreach ($this->_aUsers as $sUserId => $oUser) {
            /** @var oxUser $oUser */
            $oUser->delete($sUserId);
            unset($this->_aUsers[$sUserId]);
        }

        // restore database
        $oDbRestore = self::_getDbRestore();
        $oDbRestore->restoreDB();

        parent::tearDown();
    }

    /**
     * Creates user.
     *
     * @param string $sUserName
     * @param int    $iActive
     * @param string $sRights either user or malladmin
     * @param int    $sShopId User shop ID
     *
     * @return oxUser
     */
    protected function createUser($sUserName = null, $iActive = 1, $sRights = 'user', $sShopId = null)
    {
        $oUtils = oxRegistry::getUtils();
        $oDb = $this->getDb();

        $iLastNr = count($this->_aUsers) + 1;

        if (is_null($sShopId)) {
            $sShopId = $this->getConfig()->getShopId();
        }

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxshopid = new oxField($sShopId, oxField::T_RAW);
        $oUser->oxuser__oxactive = new oxField($iActive, oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField($sRights, oxField::T_RAW);

        // setting name
        $sUserName = $sUserName ? $sUserName : 'test' . $iLastNr . '@oxid-esales.com';
        $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField(crc32($sUserName), oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
        $oUser->save();

        $sUserId = $oUser->getId();
        $sId = oxUtilsObject::getInstance()->generateUID();

        // loading user groups
        $sGroupId = $oDb->getOne('select oxid from oxgroups order by rand() ');
        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . $sUserId . '", "' . $sShopId . '", "' . $sUserId . '", "' . $sGroupId . '" )';
        $oDb->Execute($sQ);

        $sQ = 'insert into oxorder ( oxid, oxshopid, oxuserid, oxorderdate ) values ( "' . $sId . '", "' . $sShopId . '", "' . $sUserId . '", "' . date('Y-m-d  H:i:s', time() + 3600) . '" ) ';
        $oDb->Execute($sQ);

        // adding article to order
        $sArticleID = $oDb->getOne('select oxid from oxarticles order by rand() ');
        $sQ = 'insert into oxorderarticles ( oxid, oxorderid, oxamount, oxartid, oxartnum ) values ( "' . $sId . '", "' . $sId . '", 1, "' . $sArticleID . '", "' . $sArticleID . '" ) ';
        $oDb->Execute($sQ);

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle ) values ( "' . $sUserId . '", "' . $sUserId . '", "oxtest" ) ';
        $oDb->Execute($sQ);

        $sArticleID = $oDb->getOne('select oxid from oxarticles order by rand() ');
        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "' . $sUserId . '", "' . $sUserId . '", "' . $sArticleID . '", "1" ) ';
        $oDb->Execute($sQ);

        // creating test address
        $sCountryId = $oDb->getOne('select oxid from oxcountry where oxactive = "1"');
        $sQ = 'insert into oxaddress ( oxid, oxuserid, oxaddressuserid, oxcountryid ) values ( "test_user' . $iLastNr . '", "' . $sUserId . '", "' . $sUserId . '", "' . $sCountryId . '" ) ';
        $oDb->Execute($sQ);

        // creating test executed user payment
        $aDynValue = $this->_aDynPaymentFields;
        $oPayment = oxNew('oxPayment');
        $oPayment->load('oxidcreditcard');
        $oPayment->setDynValues($oUtils->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value, true, true, true));

        $aDynValues = $oPayment->getDynValues();
        while (list($key, $oVal) = each($aDynValues)) {
            $oVal = new oxField($aDynValue[$oVal->name], oxField::T_RAW);
            $oPayment->setDynValue($key, $oVal);
            $aDynVal[$oVal->name] = $oVal->value;
        }

        $sDynValues = '';
        if (isset($aDynVal)) {
            $sDynValues = $oUtils->assignValuesToText($aDynVal);
        }

        $sQ = 'insert into oxuserpayments ( oxid, oxuserid, oxpaymentsid, oxvalue ) values ( "' . $sId . '", "' . $sUserId . '", "oxidcreditcard", "' . $sDynValues . '" ) ';
        $oDb->Execute($sQ);

        $this->_aUsers[$sUserId] = $oUser;

        return $oUser;
    }

    /**
     * oxUser::getOrders() test case when paging is on
     *
     * @return null
     */
    public function testGetOrdersWhenPagingIsOn()
    {
        $oUtils = oxUtilsObject::getInstance();
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopID = $oUser->getShopId();

        // adding some more orders..
        for ($i = 0; $i < 21; $i++) {
            $sId = $oUtils->generateUID();

            $sQ = 'insert into oxorder ( oxid, oxshopid, oxuserid, oxorderdate ) values ( "' . $sId . '", "' . $sShopID . '", "' . $oUser->getId() . '", "' . date('Y-m-d  H:i:s', time() + 3600) . '" ) ';
            $oDb->Execute($sQ);

            // adding article to order
            $sArticleID = $oDb->getOne('select oxid from oxarticles order by rand() ');
            $sQ = 'insert into oxorderarticles ( oxid, oxorderid, oxamount, oxartid, oxartnum ) values ( "' . $sId . '", "' . $sId . '", 1, "' . $sArticleID . '", "' . $sArticleID . '" ) ';
            $oDb->Execute($sQ);
        }

        $iTotal = $oDb->getOne("select count(*) from oxorder where oxshopid = '{$sShopID}' and oxuserid = '{$sUserId}'");

        $oOrders = $oUser->getOrders(10, 0);
        $this->assertEquals(10, $oOrders->count());
        $iTotal -= 10;

        $oOrders = $oUser->getOrders(10, 1);
        $this->assertEquals(10, $oOrders->count());
        $iTotal -= 10;

        $oOrders = $oUser->getOrders(10, 2);
        $this->assertEquals($iTotal, $oOrders->count());

        $oOrders = $oUser->getOrders(10, 3);
        $this->assertEquals(0, $oOrders->count());
    }

    /**
     * oxUser::setCreditPointsForRegistrant() test case
     *
     * @return null
     */
    public function testSetCreditPointsForRegistrant()
    {
        $sDate = oxRegistry::get("oxUtilsDate")->formatDBDate(date("Y-m-d"), true);
        $oDb = $this->getDb();
        $sSql = "INSERT INTO oxinvitations SET oxuserid = 'oxdefaultadmin', oxemail = 'oxemail',  oxdate='$sDate', oxpending = '1', oxaccepted = '0', oxtype = '1' ";
        $oDb->execute($sSql);
        $this->getConfig()->setConfigParam("dPointsForRegistration", 10);
        $this->getConfig()->setConfigParam("dPointsForInvitation", false);
        $this->getSession()->setVariable('su', 'oxdefaultadmin');
        $this->getSession()->setVariable('re', md5('oxemail'));

        $oUser = $this->getMock("oxuser", array("save"));
        $oUser->expects($this->once())->method('save')->will($this->returnValue(true));
        $this->assertFalse($oUser->setCreditPointsForRegistrant("oxdefaultadmin", md5('oxemail')));
        $this->assertNull(oxRegistry::getSession()->getVariable('su'));
        $this->assertNull(oxRegistry::getSession()->getVariable('re'));
    }

    /**
     * oxUser::setCreditPointsForInviter() test case
     *
     * @return null
     */
    public function testSetCreditPointsForInviter()
    {
        $this->getConfig()->setConfigParam("dPointsForInvitation", 10);

        $oUser = $this->getMock("oxuser", array("save"));
        $oUser->expects($this->once())->method('save')->will($this->returnValue(true));
        $this->assertTrue($oUser->setCreditPointsForInviter());
    }

    /**
     * oxUser::isTermsAccepted() test case
     *
     * @return null
     */
    public function testIsTermsAccepted()
    {
        $sShopId = $this->getConfig()->getShopId();
        $this->getDb()->execute("insert into oxacceptedterms (`OXUSERID`, `OXSHOPID`, `OXTERMVERSION`) values ( 'testUserId', '{$sShopId}', '0' )");

        $oUser = $this->getMock("oxuser", array("getId"));
        $oUser->expects($this->once())->method('getId')->will($this->returnValue('testUserId'));
        $this->assertTrue($oUser->isTermsAccepted());
    }

    /**
     * oxUser::acceptTerms() test case
     *
     * @return null
     */
    public function testAcceptTerms()
    {
        $oDb = $this->getDb();

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxacceptedterms where oxuserid='oxdefaultadmin'"));

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");
        $oUser->acceptTerms();

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'"));

        $oDb->execute("update oxacceptedterms set oxtermversion='0'");
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='0'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'"));

        $oUser->acceptTerms();
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'"));
    }

    /**
     * Test case for bug entry #1714
     *
     * @return null
     */
    public function testCaseForBugEntry1714()
    {
        $this->createUser();

        $iCustNr = $this->getDb()->getOne("select max(oxcustnr) from oxuser");

        $oUser = oxNew('oxUser');
        $oUser->setId("testID");
        $oUser->oxuser__oxusername = new oxField("aaa@bbb.lt", oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load("testID");
        $this->assertEquals($iCustNr + 1, $oUser->oxuser__oxcustnr->value);

        $oUser->delete();

        $oUser = oxNew('oxUser');
        $oUser->setId("testID");
        $oUser->oxuser__oxusername = new oxField("aaa@bbb.lt", oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->load("testID");
        $this->assertEquals($iCustNr + 2, $oUser->oxuser__oxcustnr->value);

        $oUser->delete();
    }

    public function testSetSelectedAddressId()
    {
        $sAddressId = 'xxx';
        $oUser = oxNew('oxUser');
        $oUser->setSelectedAddressId($sAddressId);
        $this->assertEquals($sAddressId, $oUser->getSelectedAddressId());
    }

    public function testAllowDerivedUpdate()
    {
        $oUser = oxNew('oxUser');
        $this->assertTrue($oUser->allowDerivedUpdate());
    }

    public function testisExpiredUpdateId()
    {
        $oUser = $this->createUser();
        $oUser->setUpdateKey();

        $this->assertFalse($oUser->isExpiredUpdateId($oUser->getUpdateId()));
        $this->assertTrue($oUser->isExpiredUpdateId('xxx'));

    }

    public function testMagicGetter()
    {
        $oNewsSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'getOptInEmailStatus'));
        $oNewsSubscription->expects($this->once())->method('getOptInStatus')->will($this->returnValue('getOptInStatus'));
        $oNewsSubscription->expects($this->once())->method('getOptInEmailStatus')->will($this->returnValue('getOptInEmailStatus'));

        $oUser = $this->getMock(
            'oxuser', array('getUserGroups',
                            'getNoticeListArtCnt',
                            'getWishListArtCnt',
                            'getRecommListsCount',
                            'getUserAddresses',
                            'getUserPayments',
                            'getUserCountry',
                            'getNewsSubscription')
        );

        $oUser->expects($this->once())->method('getUserGroups')->will($this->returnValue('getUserGroups'));
        $oUser->expects($this->once())->method('getNoticeListArtCnt')->will($this->returnValue('getNoticeListArtCnt'));
        $oUser->expects($this->once())->method('getWishListArtCnt')->will($this->returnValue('getWishListArtCnt'));
        $oUser->expects($this->once())->method('getRecommListsCount')->will($this->returnValue('getRecommListsCount'));
        $oUser->expects($this->once())->method('getUserAddresses')->will($this->returnValue('getUserAddresses'));
        $oUser->expects($this->once())->method('getUserPayments')->will($this->returnValue('getUserPayments'));
        $oUser->expects($this->once())->method('getUserCountry')->will($this->returnValue('getUserCountry'));
        $oUser->expects($this->exactly(2))->method('getNewsSubscription')->will($this->returnValue($oNewsSubscription));

        $this->assertEquals('getUserGroups', $oUser->oGroups);
        $this->assertEquals('getNoticeListArtCnt', $oUser->iCntNoticeListArticles);
        $this->assertEquals('getWishListArtCnt', $oUser->iCntWishListArticles);
        $this->assertEquals('getRecommListsCount', $oUser->iCntRecommLists);
        $this->assertEquals('getUserAddresses', $oUser->oAddresses);
        $this->assertEquals('getUserPayments', $oUser->oPayments);
        $this->assertEquals('getUserCountry', $oUser->oxuser__oxcountry);

        $this->assertEquals('getOptInStatus', $oUser->sDBOptin);
        $this->assertEquals('getOptInEmailStatus', $oUser->sEmailFailed);
    }

    public function testIsSamePassword()
    {
        $oUser = oxNew('oxUser');

        // plain password in db
        $oUser->oxuser__oxpassword = new oxfield('aaa');
        $this->assertFalse($oUser->isSamePassword('aaa'));
        $this->assertFalse($oUser->isSamePassword('bbb'));

        // hashed
        $oUser->setPassword('xxx');
        $this->assertTrue($oUser->isSamePassword('xxx'));
        $this->assertFalse($oUser->isSamePassword('yyy'));
    }

    public function testSetPassword()
    {
        $oUser = oxNew('oxUser');
        $oUser->setPassword('xxx');
        $this->assertFalse('' == $oUser->oxuser__oxpassword->value);
        $this->assertTrue($oUser->isSamePassword('xxx'));

        $oUser->setPassword();
        $this->assertTrue('' == $oUser->oxuser__oxpassword->value);
    }

    public function testEncodePassword()
    {
        $sPassword = 'xxx';
        $sSalt = 'yyy';
        $sEncPass = hash('sha512', $sPassword . $sSalt);

        $oUser = oxNew('oxUser');
        $this->assertEquals($sEncPass, $oUser->encodePassword($sPassword, $sSalt));
    }

    public function testGetUpdateId()
    {
        $oUser = $this->getMock('oxuser', array('setUpdateKey'));
        $oUser->expects($this->once())->method('setUpdateKey');

        $oUser->setId('xxx');
        $oUser->oxuser__oxshopid = new oxfield('yyy');
        $oUser->oxuser__oxupdatekey = new oxfield('zzz');

        $this->assertEquals(md5('xxx' . 'yyy' . 'zzz'), $oUser->getUpdateId());
    }

    public function testSetUpdateKey()
    {
        $iCurrTime = time();

        // overriding utility functions
        oxTestModules::addFunction("oxUtilsObject", "generateUId", "{ return 'xxx'; }");
        $this->setTime($iCurrTime);

        $oUser = $this->getMock('oxuser', array('save'));
        $oUser->expects($this->once())->method('save');
        $oUser->setUpdateKey();

        $this->assertEquals('xxx', $oUser->oxuser__oxupdatekey->value);
        $this->assertEquals(($iCurrTime + 3600 * 6), $oUser->oxuser__oxupdateexp->value);
    }

    public function testReSetUpdateKey()
    {
        $oUser = $this->getMock('oxuser', array('save'));
        $oUser->expects($this->once())->method('save');
        $oUser->setUpdateKey(true);

        $this->assertEquals('', $oUser->oxuser__oxupdatekey->value);
        $this->assertEquals(0, $oUser->oxuser__oxupdateexp->value);
    }

    public function testLoadUserByUpdateId()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $oUser->oxuser__oxupdatekey = new oxfield('xxx');
        $oUser->oxuser__oxupdateexp = new oxfield(time() + 3600);
        $oUser->oxuser__oxshopid = new oxfield($this->getConfig()->getShopId());
        $oUser->save();

        $sUid = md5($oUser->getId() . $oUser->oxuser__oxshopid->value . $oUser->oxuser__oxupdatekey->value);

        $oUser = oxNew('oxUser');
        $this->assertTrue($oUser->loadUserByUpdateId($sUid));
        $this->assertEquals($sUserId, $oUser->getId());

        $this->assertNull($oUser->loadUserByUpdateId('xxx'));
    }

    /**
     * Boni index for newly created users must be 1000 instead of 1000
     */
    public function testBoniAfterUserInsert()
    {
        $sId = 'testuserx';
        $oUser = oxNew('oxUser');
        $oUser->setId($sId);
        $oUser->oxuser__oxusername = new oxField("aaa@bbb.lt", oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->UNITinsert();

        $this->assertEquals('1000', $this->getDb()->getOne("select oxboni from oxuser where oxid = '$sId' "));
    }


    public function testCheckIfEmailExistsMallUsersNonAdminNoPass()
    {
        $oUser = $this->createUser();
        $oUser->oxuser__oxusername = new oxField('admin@oxid.lt', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user');
        $oUser->save();

        $oUser = oxNew('oxUser');
        if ($this->getConfig()->getEdition() === 'EE') {
            $oUser->setMallUsersStatus(true);
        }
        $this->assertFalse($oUser->checkIfEmailExists('admin@oxid.lt'));
    }

    public function testCheckIfEmailExistsMallUsersTryingToCreateUserWithNameAdmin()
    {
        $oUser = oxNew('oxUser');
        if ($this->getConfig()->getEdition() === 'EE') {
            $oUser->setMallUsersStatus(true);
        }

        $this->assertTrue($oUser->checkIfEmailExists(oxADMIN_LOGIN));
    }

    public function testCheckIfEmailExistsMallUsersOldEntryWithoutPass()
    {
        $oUser = oxNew('oxUser');
        if ($this->getConfig()->getEdition() === 'EE') {
            $oUser->setMallUsersStatus(true);
        }

        $this->assertFalse($oUser->checkIfEmailExists('aaa@bbb.lt'));
    }

    public function testCheckIfEmailExistsMallUsersOldEntryWithPass()
    {
        $oUser = oxNew('oxUser');
        if ($this->getConfig()->getEdition() === 'EE') {
            $oUser->setMallUsersStatus(true);
        }

        $this->assertTrue($oUser->checkIfEmailExists(oxADMIN_LOGIN));
    }

    /**
     * Test case:
     * creating new user in subshop which data is:
     * name: admin, pass: adminas, rights: user, shop id: 2
     */
    public function testNewUserInSubShop()
    {
        $oConfig = $this->getMock('oxconfig', array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(2));

        $oUser = $this->getMock('oxuser', array('isAdmin', 'getConfig', 'getViewName'), array(), '', false);
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oUser->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oUser->expects($this->any())->method('getViewName')->will($this->returnValue('oxuser'));

        $oUser->init('oxuser');
        $this->assertTrue($oUser->checkIfEmailExists(oxADMIN_LOGIN));
    }

    // QA reported that newly created user has not rights set in db
    public function testCreatingUserRightsMustBeSet()
    {
        $oDb = $this->getDb();
        $oDb->execute('delete from oxuser where oxusername="aaa@bbb.lt" ');

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->createUser();

        $oNewUser = oxNew('oxUser');
        $oNewUser->load($oUser->getId());
        $this->assertEquals('user', $oNewUser->oxuser__oxrights->value);
    }

    /**
     * Test case:
     * newsletter registration is made using email, trying to register user using same email and without password
     */
    public function testCreateUserWhileRegistrationNoPass()
    {
        // simulating newsletter subscription
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->save();
        $this->assertEquals('1000', $oUser->oxuser__oxboni->value);

        $aInvAdress = array('oxuser__oxfname'     => 'xxx',
                            'oxuser__oxlname'     => 'yyy',
                            'oxuser__oxstreetnr'  => '11',
                            'oxuser__oxstreet'    => 'zzz',
                            'oxuser__oxzip'       => '22',
                            'oxuser__oxcity'      => 'ooo',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        $oUser = oxNew('oxUser');
        $oUser->setId('_testUser');
        $oUser->checkValues('aaa@bbb.lt', '', '', $aInvAdress, array());
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->createUser();

        $oUser->load($oUser->oxuser__oxid->value);
        $oUser->changeUserData($oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array());
        $this->assertEquals('1000', $oUser->oxuser__oxboni->value);
    }

    /**
     * Test case:
     * newsletter registration is made using email, trying to register user using same email and with password
     */
    public function testCreateUserWhileRegistrationWithPass()
    {
        // simulating newsletter subscription
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->save();

        $aInvAdress = array('oxuser__oxfname'     => 'xxx',
                            'oxuser__oxlname'     => 'yyy',
                            'oxuser__oxstreetnr'  => '11',
                            'oxuser__oxstreet'    => 'zzz',
                            'oxuser__oxzip'       => '22',
                            'oxuser__oxcity'      => 'ooo',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        try {
            $oUser = oxNew('oxUser');
            $oUser->setId('_testUser');
            $oUser->checkValues('aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array());
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load($oUser->oxuser__oxid->value);
            $oUser->changeUserData($oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array());
        } catch (Exception $oEx) {
            $this->fail('failed while runing testCheckValues test: ' . "\n msg:\n" . $oEx->getMessage() . "\n trace:\n" . $oEx->getTraceAsString());
        }
    }

    /**
     * Test case:
     * user is registered (without pass), updating its data
     */
    public function testUpdateUserCheckValuesWithPass()
    {
        $oUser = $this->createUser();
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->save();

        $aInvAdress = array('oxuser__oxfname'     => 'xxx',
                            'oxuser__oxlname'     => 'yyy',
                            'oxuser__oxstreetnr'  => '11',
                            'oxuser__oxstreet'    => 'zzz',
                            'oxuser__oxzip'       => '22',
                            'oxuser__oxcity'      => 'ooo',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        try {
            $oUser = oxNew('oxUser');
            $oUser->setId('_testUser');
            $oUser->checkValues('aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array());
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load($oUser->oxuser__oxid->value);
            $oUser->changeUserData($oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array());
        } catch (Exception $oEx) {
            $this->fail('failed while runing testCheckValues test: ' . "\n msg:\n" . $oEx->getMessage() . "\n trace:\n" . $oEx->getTraceAsString());
        }
    }

    /**
     * Test case:
     * user is registered (with pass), updating its data
     */
    public function testUpdateUserCheckValuesOldWithNewWithPass()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('xxx', oxField::T_RAW);
        $oUser->save();
        $aInvAdress = array('oxuser__oxusername'  => 'aaa@bbb.lt',
                            'oxuser__oxfname'     => 'xxx',
                            'oxuser__oxlname'     => 'yyy',
                            'oxuser__oxstreetnr'  => '11',
                            'oxuser__oxstreet'    => 'zzz',
                            'oxuser__oxzip'       => '22',
                            'oxuser__oxcity'      => 'ooo',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
                            'oxuser__oxpassword'  => 'xxx');

        try {
            $oUser = oxNew('oxUser');
            $oUser->load($sUserId);
            $oUser->changeUserData('xxx@yyy.zzz', 'xxx', 'xxx', $aInvAdress, array());
            $this->assertEquals($sUserId, $oUser->getId());
        } catch (Exception $oEx) {
            $this->fail('failed while runing testCheckValues test');
        }
    }

    /**
     * Test case:
     * creating user with pass
     */
    public function testCreateUserWithPass()
    {
        $aInvAdress = array('oxuser__oxfname'     => 'xxx',
                            'oxuser__oxlname'     => 'yyy',
                            'oxuser__oxstreetnr'  => '11',
                            'oxuser__oxstreet'    => 'zzz',
                            'oxuser__oxzip'       => '22',
                            'oxuser__oxcity'      => 'ooo',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        try {
            $oUser = oxNew('oxUser');
            $oUser->setId('_testUser');
            $oUser->checkValues('aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array());
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('bbbaaabbb', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load($oUser->oxuser__oxid->value);
            $oUser->changeUserData($oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array());
        } catch (Exception $oEx) {
            $this->fail('failed while runing testCheckValues test');
        }
    }

    /**
     * Testing getter which is used for backwards compatibility
     */
    public function testGet()
    {
        $oUser1 = $this->createUser();
        $oUser2 = oxNew('oxUser');
        $oUser2->load($oUser1->getId());

        $this->assertEquals($oUser1->oGroups->arrayKeys(), $oUser2->getUserGroups()->arrayKeys());
        $this->assertEquals($oUser1->oAddresses->arrayKeys(), $oUser2->getUserAddresses()->arrayKeys());
        $this->assertEquals($oUser1->oPayments, $oUser2->getUserPayments());
        $this->assertEquals($oUser1->oxuser__oxcountry->value, $oUser2->getUserCountry()->value);
        $this->assertEquals($oUser1->sDBOptin, $oUser2->getNewsSubscription()->getOptInStatus());
        $this->assertEquals($oUser1->sEmailFailed, $oUser2->getNewsSubscription()->getOptInEmailStatus());
    }

    /**
     * Testing user's news subscribtion object getter
     */
    // 1. for empty user or user which does not have any subscription info oxuser::getNewsSubscription
    //    must return empty oxnewssubscribed object
    public function testGetNewsSubscriptionNoUserEmptySubscription()
    {
        $oUser = oxNew('oxUser');
        $this->assertNull($oUser->getNewsSubscription()->oxnewssubscribed__oxid->value);
    }

    // 2. loading subscription by user id
    public function testGetNewsSubscriptionNoUserReturnsByOxid()
    {
        oxAddClassModule('Unit\Application\Model\UserTest_oxNewsSubscribed', 'oxnewssubscribed');
        $oUser = oxNew('oxUser');
        $oUser->setId('oxid');
        $this->assertTrue($oUser->getNewsSubscription()->loadFromUserID);
    }

    // 3. loading subscription by user email
    public function testGetNewsSubscriptionNoUserReturnsByEmail()
    {
        oxAddClassModule('Unit\Application\Model\UserTest_oxNewsSubscribed', 'oxnewssubscribed');
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField('email', oxField::T_RAW);
        $this->assertTrue($oUser->getNewsSubscription()->loadFromEMail);
    }

    /**
     * Testing how group/address/exec. payments list loading works
     */
    // 1. fetching group info for existing user - must return 1 group
    public function testGetUserGroups_correctInput()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $oGroups = $oUser->getUserGroups();

        // each new created user is assigned to 1 new created user group
        $sGroupId = $oDb->getOne('select oxgroupsid from oxobject2group where oxobjectid="' . $sUserId . '"');
        $this->assertTrue(isset($oGroups[$sGroupId]));
    }

    // 2. fetching group info for not existing user - must return 0 group
    public function testGetUserGroupsWrongInput()
    {
        $oUser = oxNew('oxUser');
        $oUser->setId('xxx');
        $oGroups = $oUser->getUserGroups();

        // no user for not existing user
        $this->assertEquals(0, count($oGroups));
    }


    /**
     * Testing user address getter
     */
    // 1. fetching address info for existing user - must return 1 address
    public function testGetUserAddressesCorrenctInput()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $aAddress = $oUser->getUserAddresses();

        // each new created user is assigned to 1 new created address
        $sAddressId = $oDb->getOne('select oxid from oxaddress where oxuserid="' . $oUser->getId() . '"');
        $this->assertEquals(true, isset($aAddress[$sAddressId]));

        $aAddress = $oUser->getUserAddresses('xxx');
        $this->assertEquals(0, count($aAddress));
    }

    // 2. fetching address info for not existing user - must return 0 address
    public function testGetUserAddressesWrongInput()
    {
        $oUser = oxNew('oxUser');
        $aAddress = $oUser->getUserAddresses('xxx');

        // each new created user is assigned to 1 new created address
        $this->assertEquals(0, count($aAddress));
    }

    /**
     * Testing user payments getter
     */
    // 1. fetching payment info for existing user - must return 1 payment
    public function testGetUserPaymentsCorrectInput()
    {
        $oUser = $this->createUser();
        $oUserPayments = $oUser->getUserPayments();

        // each new created user is assigned to 1 new created exec. payment
        $this->assertEquals(1, count($oUserPayments));

        $oUserPayments->rewind();
        $oUserPayment = $oUserPayments->current();

        $this->assertEquals($oUserPayment->oxuserpayments__oxuserid->value, $oUser->getId());
        $this->assertEquals($oUserPayment->oxpayments__oxdesc->value, 'Kreditkarte'); //important for compatibility to templates
        $this->assertEquals($oUserPayment->oxuserpayments__oxpaymentsid->value, 'oxidcreditcard'); //important for compatibility to templates

    }

    // 2. fetching payment info for not existing user - must return 0 payment
    public function testGetUserPaymentsWrongInput()
    {
        $oUser = oxNew('oxUser');
        $oPayments = $oUser->getUserPayments('xxx');

        // each new created user is assigned to 1 new created exec. payment
        $this->assertEquals(0, count($oPayments));
    }

    /**
     * Testing user recommendation lists getter
     */
    public function testGetUserRecommLists()
    {
        $oDb = $this->getDb();
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopId = $this->getConfig()->getShopId();

        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "test", "' . $sUserId . '", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $oDb->Execute($sQ);

        $sArticleID = $oDb->getOne('select oxid from oxarticles order by rand() ');
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid ) values ( "test", "' . $sArticleID . '", "test" ) ';
        $oDb->Execute($sQ);

        $oUser = oxNew('oxUser');
        $oRecommlists = $oUser->getUserRecommLists($sUserId);

        $this->assertEquals(1, count($oRecommlists));
        $oRecommlist = $oRecommlists->current();
        $this->assertEquals($oRecommlist->oxrecommlists__oxuserid->value, $sUserId);
        $this->assertEquals($oRecommlist->oxrecommlists__oxtitle->value, "oxtest");
    }

    /**
     * Testing user recommendation lists getter
     */
    public function testRecommListsCount()
    {
        $oDb = $this->getDb();
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopId = $this->getConfig()->getShopId();

        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "test", "' . $sUserId . '", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $oDb->Execute($sQ);

        $sArticleID = $oDb->getOne('select oxid from oxarticles order by rand() ');
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid ) values ( "test", "' . $sArticleID . '", "test" ) ';
        $oDb->Execute($sQ);

        $oUser = oxNew('oxUser');
        $oUser->load($sUserId);
        $iRecommlists = $oUser->getRecommListsCount();

        $this->assertEquals(1, $iRecommlists);
    }

    /**
     * Testing user object saving
     */
    public function testSave()
    {
        $oDb = $this->getDb();

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxpassword = new oxField('somePassword', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField(null, oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);
        $oUser->save();

        // looking for other info
        $this->assertEquals('user', $oUser->oxuser__oxrights->value);
        $this->assertEquals(false, empty($oUser->oxuser__oxregister->value));

        // looking for record in oxremark table
        $sQ = 'select count(oxid) from oxremark where oxparentid = "' . $oUser->getId() . '" and oxtype !="o"';
        $this->assertEquals(1, (int) $oDb->getOne($sQ));

        $oUser = oxNew('oxUser');
        $oUser->setId($oUser->getId());
        $oUser->save();
    }

    /**
     * Testing user object saving
     */
    public function testSaveWithSpecChar()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $aInvAddress ['oxuser__oxcompany'] = 'test&';
        $aInvAddress ['oxuser__oxaddinfo'] = 'test&';
        $oUser->assign($aInvAddress);
        $oUser->save();
        $this->assertEquals('test&amp;', $oUser->oxuser__oxcompany->value);
        $this->assertEquals('test&amp;', $oUser->oxuser__oxaddinfo->value);
        $sQ = 'select oxcompany from oxuser where oxid = "' . $oUser->oxuser__oxid->value . '" ';
        $this->assertEquals('test&', $oDb->getOne($sQ));
    }

    /**
     * Testing user object saving if birthday is added
     */
    public function testSaveWithBirthDay()
    {
        $oDb = $this->getDb();

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxpassword = new oxField('somePassword', oxField::T_RAW);
        $oUser->oxuser__oxbirthdate = new oxField(array('day' => '12', 'month' => '12', 'year' => '1212'), oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField(null, oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);
        $oUser->save();

        // looking for other info
        $this->assertEquals('user', $oUser->oxuser__oxrights->value);
        $this->assertEquals(false, empty($oUser->oxuser__oxregister->value));
        $this->assertEquals('1212-12-12', $oUser->oxuser__oxbirthdate->value);

        // looking for record in oxremark tabl
        $sQ = 'select count(oxid) from oxremark where oxparentid = "' . $oUser->getId() . '"  and oxtype !="o"';
        $this->assertEquals(1, (int) $oDb->getOne($sQ));
    }

    /**
     * Testing user rights getter
     */
    // 1. for user with no initial rights
    public function testGetUserRightsNoInitialRights()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('user', $oUser->UNITgetUserRights());
    }

    // 2. user initial rights are malladmin
    public function testGetUserRightsInitialAdminRightsSessionUserIsAdmin()
    {
        $this->getSession()->setVariable("usr", "oxdefaultadmin");

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals('malladmin', $oUser->UNITgetUserRights());
    }

    // 3. user initial rights are "user"
    public function testGetUserRightsInitialAdminRightsSessionUserIsSimpleUser()
    {
        $this->getSession()->setVariable("usr", null);
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals('user', $oUser->UNITgetUserRights());
    }

    // 4. user initial rights are sub shop admin
    public function testGetUserRightsInitialAdminRightsSessionUserIsSubShopUser()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = oxNew('oxBase');
        $oUser->init('oxuser');
        $oUser->load($sUserId);
        $oUser->oxuser__oxrights = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oUser->save();

        $this->getSession()->setVariable("usr", $oUser->oxuser__oxid->value);

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxrights = new oxField($this->getConfig()->GetShopId(), oxField::T_RAW);
        $this->assertEquals($this->getConfig()->GetShopId(), $oUser->UNITgetUserRights());

        // check for denial
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxrights = new oxField(2, oxField::T_RAW);
        $this->assertEquals("user", $oUser->UNITgetUserRights());

        // check for denial
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals("user", $oUser->UNITgetUserRights());
    }

    /**
     * Testing if inGroup method works OK
     */
    public function testInGroupWrongGroup()
    {
        $oUser = $this->createUser();

        // assigned to some group ?
        $this->assertEquals(false, $oUser->inGroup('oxtestgroup'));
    }

    public function testInGroupCorrectGroup()
    {
        $oDb = $this->getDb();
        $oUser = $this->createUser();

        // assigned to some group ?
        $sGroupId = $oDb->getOne('select oxgroupsid from oxobject2group where oxobjectid="' . $oUser->getId() . '"');
        $this->assertEquals(true, $oUser->inGroup($sGroupId));
    }

    /**
     * Testing if deletion does not leave any related records
     */
    public function testDeleteEmptyUser()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals(false, $oUser->delete());
    }

    public function testDelete()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        // user address
        $oAddress = oxNew('oxAddress');
        $oAddress->setId("_testAddress");
        $oAddress->oxaddress__oxuserid = new oxField($sUserId);
        $oAddress->save();

        // user groups
        $o2g = oxNew('oxBase');
        $o2g->init("oxobject2group");
        $o2g->setId("_testO2G");
        $o2g->oxobject2group__oxobjectid = new oxField($sUserId);
        $o2g->oxobject2group__oxgroupsid = new oxField($sUserId);
        $o2g->save();

        // notice/wish lists
        $oU2B = oxNew('oxBase');
        $oU2B->init("oxuserbaskets");
        $oU2B->setId("_testU2B");
        $oU2B->oxuserbaskets__oxuserid = new oxField($sUserId);
        $oU2B->save();

        // newsletter subscription
        $oNewsSubs = oxNew('oxBase');
        $oNewsSubs->init("oxnewssubscribed");
        $oNewsSubs->setId("_testNewsSubs");
        $oNewsSubs->oxnewssubscribed__oxemail = new oxField($sUserId);
        $oNewsSubs->oxnewssubscribed__oxuserid = new oxField($sUserId);
        $oNewsSubs->save();

        // delivery and delivery sets
        $o2d = oxNew('oxBase');
        $o2d->init("oxobject2delivery");
        $o2d->setId("_testo2d");
        $o2d->oxobject2delivery__oxobjectid = new oxField($sUserId);
        $o2d->oxobject2delivery__oxdeliveryid = new oxField($sUserId);
        $o2d->save();

        // discounts
        $o2d = oxNew('oxBase');
        $o2d->init("oxobject2discount");
        $o2d->setId("_testo2d");
        $o2d->oxobject2discount__oxobjectid = new oxField($sUserId);
        $o2d->oxobject2discount__oxdiscountid = new oxField($sUserId);
        $o2d->save();

        // order information
        $oRemark = oxNew('oxBase');
        $oRemark->init("oxremark");
        $oRemark->setId("_testRemark");
        $oRemark->oxremark__oxparentid = new oxField($sUserId);
        $oRemark->oxremark__oxtype = new oxField('r');
        $oRemark->save();

        $oUser = oxNew('oxUser');
        $oUser->load($sUserId);
        $oUser->delete();

        $aWhat = array('oxuser'            => 'oxid',
                       'oxaddress'         => 'oxuserid',
                       'oxuserbaskets'     => 'oxuserid',
                       'oxnewssubscribed'  => 'oxuserid',
                       'oxobject2delivery' => 'oxobjectid',
                       'oxobject2discount' => 'oxobjectid',
                       'oxobject2group'    => 'oxobjectid',
                       'oxobject2payment'  => 'oxobjectid',
            // all order information must be preserved
                       'oxremark'          => 'oxparentid',
        );

        // now checking if all related records were deleted
        foreach ($aWhat as $sTable => $sField) {
            $sQ = 'select count(*) from ' . $sTable . ' where ' . $sField . ' = "' . $sUserId . '" ';

            if ($sTable == 'oxremark') {
                $sQ .= " AND oxtype ='o'";
            }

            $iCnt = $oDb->getOne($sQ);
            if ($iCnt > 0) {
                $this->fail($iCnt . ' records were not deleted from "' . $sTable . '" table');
            }
        }
    }

    //FS#2578
    public function testDeleteSpecialUser()
    {
        $oDb = $this->getDb();
        $iLastCustNr = ( int ) $oDb->getOne('select max( oxcustnr ) from oxuser') + 1;
        $sShopId = $this->getConfig()->getShopId();
        $sQ = 'insert into oxuser (oxid, oxshopid, oxactive, oxrights, oxusername, oxpassword, oxcustnr, oxcountryid) ';
        $sQ .= 'values ( "oxtestuser", "' . $sShopId . '", "1", "user", "testuser", "", "' . $iLastCustNr . '", "testCountry" )';
        $oDb->execute($sQ);

        $oUser = oxNew('oxUser');
        $oUser->delete("oxtestuser");
        $this->assertEquals(false, $oDb->getOne('select oxid from oxuser where oxid = "oxtestuser"'));
    }

    /**
     * Testing object loading.
     * Mostly to check if create date value is formatted.
     */
    public function testLoad()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = oxNew('oxUser');
        $oUser->load($sUserId);

        $sCreate = $oDb->getOne('select oxcreate from oxuser where oxid="' . $oUser->getId() . '" ');
        $this->assertEquals(oxRegistry::get("oxUtilsDate")->formatDBDate($sCreate), $oUser->oxuser__oxcreate->value);
    }


    /**
     * Testing how update/insert works
     */
    public function testInsert()
    {
        $oDb = $this->getDb();

        $oUser = oxNew('oxUser');
        $oUser->UNITinsert();

        // checking
        $sQ = 'select count(*) from oxuser where oxid = "' . $oUser->oxuser__oxid->value . '" ';
        $this->assertEquals(1, $oDb->getOne($sQ));

        // checking boni
        $sQ = 'select oxboni from oxuser where oxid = "' . $oUser->oxuser__oxid->value . '" ';
        $this->assertEquals(1000, $oDb->getOne($sQ));
    }


    /**
     * Testing update functionality
     */
    public function testUpdate()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = $this->getMock("oxuser", array('isAdmin'));
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUser->load($sUserId);

        // copying to test
        $sOxCreate = $oUser->oxuser__oxcreate->value;
        $sOxCustNr = $oUser->oxuser__oxcustnr->value;

        // updating
        $oUser->UNITupdate();

        // reloading
        $oUser = oxNew('oxUser');
        $oUser->load($sUserId);

        // checking
        $this->assertEquals($sOxCreate, $oUser->oxuser__oxcreate->value);
        $this->assertEquals($sOxCustNr, $oUser->oxuser__oxcustnr->value);
    }


    /**
     * Testing oxuser::exists method
     */
    public function testExistsNotExisting()
    {
        $oUser = oxNew('oxUser');
        $this->assertFalse($oUser->exists('zzz'));
    }

    public function testExistsMallUsers()
    {
        $oUser = $this->createUser();

        $oConfig = $this->getConfig();
        $blMall = $oConfig->blMallUsers;
        $oConfig->blMallUsers = true;

        $this->assertEquals(true, $oUser->exists($oUser->getId()));

        // restoring
        $oConfig->blMallUsers = $blMall;
    }

    public function testExistsIfMallAdmin()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');
        $this->assertTrue($oUser->exists());
    }

    public function testExists()
    {
        $oUser = $this->createUser();
        $this->assertEquals(true, $oUser->exists());
    }

    /**
     * Testing #5901 case
     */
    public function testExistsInOtherSubshops()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');
        $oUser->oxuser__oxrights = new oxField("");
        $oUser->oxuser__oxshopid = new oxField("2");
        $oUser->oxuser__oxusername = new oxField("differentName");

        $this->getConfig()->setShopId(2);

        $this->assertTrue($oUser->exists());
    }

    /**
     * Testing existing username
     * (same as subscribing to newsletter logics)
     */
    public function testExistsUsername()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField("admin", oxField::T_RAW);
        $this->assertTrue($oUser->exists());
    }

    /**
     * Testing existing username in different subshop
     * (same as subscribing to newsletter logics)
     */
    public function testExistsUsernameMultishop()
    {
        $this->getConfig()->setShopId(2);
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField("admin", oxField::T_RAW);

        $this->assertFalse($oUser->exists());
    }



    /**
     * Checking amount of created orders
     */
    // 1. checking order count for random user. order count must be 1
    public function testGetOrdersForRandomUSer()
    {
        $oUser = $this->createUser();

        // checking order count
        $this->assertEquals(1, count($oUser->getOrders()));
    }

    // 2. checking order count for random user. order count must be 1
    public function testGetOrdersForNonRegUser()
    {
        $oUser = $this->createUser();
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);

        // checking order count
        $this->assertEquals(0, count($oUser->getOrders()));
    }


    /**
     * Testing executed order count
     */
    // 1. empty user normally have no orders
    public function testGetOrderCountEmptyUser()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals(0, $oUser->getOrderCount());
    }

    // 2. demo user has 1 demo order
    public function testGetOrderCountUserWithOrder()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();

        $iOrderCnt = $oDb->getOne('select count(*) from oxorder where oxuserid = "' . $oUser->oxuser__oxid->value . '" and oxorderdate >= "' . $oUser->oxuser__oxregister->value . '" ');
        $this->assertEquals($iOrderCnt, $oUser->getOrderCount());
    }


    /**
     * Testing active country
     */
    public function testGetActiveCountryEmptyUser()
    {
        $oUser = oxNew('oxUser');
        //to make sure there is no user in the session
        $oUser->logout();
        $this->assertEquals('', $oUser->getActiveCountry());
    }

    public function testGetActiveCountryPassedAddress()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $sQ = 'select oxid from oxaddress where oxuserid = "' . $sUserId . '"';
        $sAddessId = $oDb->getOne($sQ);
        $this->getSession()->setVariable('deladrid', $sAddessId);

        // loading user
        $oUser->load($sUserId);

        // checking country ID
        $sQ = 'select oxcountryid from oxaddress where oxuserid = "' . $sUserId . '" ';
        $this->assertEquals($oDb->getOne($sQ), $oUser->getActiveCountry());
    }

    public function testGetActiveCountryNoPassedAddressCountryIsTakenFromUser()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        // checking user country
        $sQ = 'select oxcountryid from oxuser where oxid = "' . $sUserId . '" ';
        $this->assertEquals($oDb->getOne($sQ), "testCountry");
        $this->assertEquals($oDb->getOne($sQ), $oUser->getActiveCountry());
    }

    public function testGetActiveCountryNoPassedAddressCountryIsTakenFromSessionUser()
    {
        $oUser = $this->createUser();
        $sUsrCountry = $oUser->oxuser__oxcountryid->value;
        $this->getSession()->setVariable('usr', $oUser->getId());

        // checking user country
        $oUser = oxNew('oxUser');
        $this->assertEquals($sUsrCountry, $oUser->getActiveCountry());
    }


    /**
     * Testing user creation
     */
    // 1. creating normalu user with password, after creation new DB record must appear
    public function testCreateUser()
    {
        $oDb = $this->getDb();

        $oUser = oxNew('oxUser');
        $oUser->createUser();

        // checking
        $sQ = 'select count(*) from oxuser where oxid = "' . $oUser->getId() . '" ';
        $this->assertEquals(1, $oDb->getOne($sQ));

    }

    // 2. creating with additional duplicate entries check for mall users
    public function testCreateUserMallUsers()
    {
        $oDb = $this->getDb();

        $this->getConfig()->setConfigParam('blMallUsers', true);

        $oUser = oxNew('oxUser');
        $oUser->createUser();

        // checking
        $sQ = 'select count(*) from oxuser where oxid = "' . $oUser->getId() . '" ';
        $this->assertEquals(1, $oDb->getOne($sQ));
    }

    //3. creating user which overrides some user without password. It should erase previously
    //user stored order info
    public function testCreateUserOverridingUserWithoutPassword()
    {
        $oDb = $this->getDb();

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->save();

        // recreating
        $oUser->createUser();

        // checking
        $sQ = 'select count(*) from oxuser where oxusername = "' . $oUser->oxuser__oxusername->value . '" ';
        $this->assertEquals(1, $oDb->getOne($sQ));
    }

    public function testCreateUserMallUsersTryingToCreateSameUserAgainShouldThrowAnExcp()
    {
        $oUser = $this->createUser();
        $oUser->oxuser__oxusername = new oxField('testuser' . time());
        $oUser->setPassword('xxx');
        $oUser->save();

        try {
            $oUser->setMallUsersStatus(true);
            $oUser->createUser();
        } catch (Exception $oExcp) {
            $oLang = oxRegistry::getLang();
            $this->assertEquals(sprintf($oLang->translateString('ERROR_MESSAGE_USER_USEREXISTS', $oLang->getTplLanguage()), $oUser->oxuser__oxusername->value), $oExcp->getMessage());

            return;
        }

        $this->fail('user creation is not allowed');
    }

    public function testCreateUserSavingFailsExcpThrown()
    {
        $oUser = $this->getMock('oxuser', array('save'));
        $oUser->expects($this->once())->method("save")->will($this->returnValue(false));

        try {
            $oUser->createUser();
        } catch (Exception $oExcp) {
            $this->assertEquals('ERROR_MESSAGE_USER_USERCREATIONFAILED', $oExcp->getMessage());

            return;
        }
        $this->fail('user saving must fail');
    }

    /**
     * Testing how oxid adds/removes user from group
     */
    // 1. trying to add to already assigned group
    public function testAddToGroupToAssigned()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();

        $sGroupId = $oDb->getOne('select oxgroupsid from oxobject2group where oxobjectid="' . $oUser->getId() . '" ');;

        // assigning to some group
        $this->assertEquals(false, $oUser->addToGroup($sGroupId));
    }

    // 2. simply adding to
    public function testAddToGroupToNotAssigned()
    {
        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        // looking for not assigned group
        $sNewGroup = $oDb->getOne('select oxid from oxgroups where oxid not in ( select oxgroupsid from oxobject2group where oxobjectid="' . $sUserId . '" ) ');

        // checking before insert
        $this->assertEquals(1, count($oUser->getUserGroups()));

        // assigning to some group
        $this->assertTrue($oUser->addToGroup($sNewGroup));

        // checking DB
        $sCnt = $oDb->getOne('select count(*) from oxobject2group where oxobjectid="' . $sUserId . '" and oxgroupsid="' . $sNewGroup . '" ');
        $this->assertEquals(1, $sCnt);

        $oGroups = $oUser->getUserGroups();
        // checking group count after adding to new one
        $this->assertEquals(2, count($oGroups));

        // #0003218: validating loaded groups
        $this->assertEquals(true, isset($oGroups[$sNewGroup]));
        $this->assertEquals($sNewGroup, $oGroups[$sNewGroup]->getId());
    }

    public function testRemoveFromGroup()
    {
        $oDb = $this->getDb();
        $myConfig = $this->getConfig();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $sQ = 'select oxid from oxgroups where oxid <> (select oxgroupsid from oxobject2group where oxobjectid = "' . $sUserId . '") ';
        $sQ .= 'order by rand()';
        $sGroupId = $oDb->getOne($sQ);

        // checking
        $sQ = 'insert into oxobject2group ( oxid, oxshopid, oxobjectid, oxgroupsid ) ';
        $sQ .= 'values ( "_testO2G_id", "' . $myConfig->getShopId() . '", "' . $sUserId . '", "' . $sGroupId . '" ) ';
        $oDb->Execute($sQ);

        // loading to initialize group list
        $oUser->load($sUserId);

        // checking before insert
        $this->assertEquals(2, $oUser->getUserGroups()->count());

        // assigning to some group
        $oUser->removeFromGroup($sGroupId);

        // checking
        $sQ = 'select count(*) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "' . $sGroupId . '" ';
        $sCnt = $oDb->getOne($sQ);

        $this->assertEquals(0, $sCnt);

        // checking before insert
        $this->assertEquals(1, count($oUser->getUserGroups()));
    }


    /**
     * Testing onOrderExecute various combinations
     */
    public function testOnOrderExecute0()
    {
        $this->getConfig()->setConfigParam('sMidlleCustPrice', 99);
        $this->getConfig()->setConfigParam('sLargeCustPrice', 999);

        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopId = $this->getConfig()->getShopId();

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . oxUtilsObject::getInstance()->generateUID() . '", "' . $sShopId . '", "' . $sUserId . '", "oxidnotyetordered" )';
        $oDb->Execute($sQ);

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(9);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess = 1;
        $oUser->onOrderExecute($oBasket, $iSuccess);

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidcustomer"';

        $this->assertEquals(1, $oDb->getOne($sQ));
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidsmallcust"';
        $this->assertEquals(1, $oDb->getOne($sQ));

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals(0, $oDb->getOne($sQ));
    }

    public function testOnOrderExecute1()
    {
        $this->getConfig()->setConfigParam('sMidlleCustPrice', 99);
        $this->getConfig()->setConfigParam('sLargeCustPrice', 999);

        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopId = $this->getConfig()->getShopId();

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . oxUtilsObject::getInstance()->generateUID() . '", "' . $sShopId . '", "' . $sUserId . '", "oxidnotyetordered" )';
        $oDb->Execute($sQ);

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(699);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess = 1;
        $oUser->onOrderExecute($oBasket, $iSuccess);

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidcustomer"';
        $this->assertEquals(1, $oDb->getOne($sQ));
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidmiddlecust"';
        $this->assertEquals(1, $oDb->getOne($sQ));

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals(0, $oDb->getOne($sQ));
    }

    public function testOnOrderExecute2()
    {
        $this->getConfig()->setConfigParam('sMidlleCustPrice', 99);
        $this->getConfig()->setConfigParam('sLargeCustPrice', 999);

        $oDb = $this->getDb();

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();
        $sShopId = $this->getConfig()->getShopId();

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "' . oxUtilsObject::getInstance()->generateUID() . '", "' . $sShopId . '", "' . $sUserId . '", "oxidnotyetordered" )';
        $oDb->Execute($sQ);

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(1999);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess = 1;
        $oUser->onOrderExecute($oBasket, $iSuccess);

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidcustomer"';
        $this->assertEquals(1, $oDb->getOne($sQ));
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidgoodcust"';
        $this->assertEquals(1, $oDb->getOne($sQ));

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "' . $sUserId . '" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals(0, $oDb->getOne($sQ));
    }

    /**
     * Testing user basket
     */
    // 1. fetching saved basket for existing user, should return 1
    public function testGetBasketExistingBasket()
    {
        $oUser = $this->createUser();

        $oBasket = $oUser->getBasket('oxtest');
        $this->assertEquals(1, count($oBasket->getItemCount(false)));
    }

    // 2. fetching basket for no user - should return 0
    public function testGetBasketNotExistingBasket()
    {
        $oUser = oxNew('oxUser');

        $oBasket = $oUser->getBasket('oxtest2');
        $this->assertEquals(0, count($oBasket->oArticles));
    }


    /**
     * Testing user birth data converter
     */
    public function testConvertBirthdayGoodInput()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('1981-05-14', $oUser->convertBirthday(array('year' => 1981, 'month' => 05, 'day' => 14)));
    }

    public function testConvertBirthdayAlmostGoodInput()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('1981-02-01', $oUser->convertBirthday(array('year' => 1981, 'month' => 02, 'day' => 31)));
    }

    public function testConvertBirthdayAlmostGoodInput2()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('1981-04-01', $oUser->convertBirthday(array('year' => 1981, 'month' => 04, 'day' => 31)));
    }

    public function testConvertBirthdayBadInput()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('', $oUser->convertBirthday('oxtest'));
    }

    public function testConvertBirthdaySomeGoodInput()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals('1981-01-01', $oUser->convertBirthday(array('year' => 1981)));
    }

    /**
     * Testing login validator
     */
    public function testCheckForAvailableEmailChangingData()
    {
        $oUser = $this->createUser();
        $sUsername = $oUser->oxuser__oxusername->value;

        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $this->assertTrue($oUser->checkIfEmailExists($sUsername));
    }

    /**
     * Testing if method detects duplicate records.
     */
    public function testCheckForAvailableEmailIfNewEmail()
    {
        $oUser = $this->createUser();
        $this->assertFalse($oUser->checkIfEmailExists('aaaaa'));
    }

    /**
     * Testing if method checkValues performs all defined actions
     */
    public function testCheckValues()
    {
        $oInputValidator = $this->getMock('oxInputValidator');
        $oInputValidator->expects($this->once())->method('checkLogin');
        $oInputValidator->expects($this->once())->method('checkEmail');
        $oInputValidator->expects($this->once())->method('checkPassword');
        $oInputValidator->expects($this->once())->method('checkRequiredFields');
        $oInputValidator->expects($this->once())->method('checkCountries');
        $oInputValidator->expects($this->once())->method('checkVatId');
        oxRegistry::set('oxInputValidator', $oInputValidator);

        $oUser = oxNew('oxUser');
        $oUser->checkValues("X", "X", "X", array(), array());
    }

    public function testCheckValuesWithInputException()
    {
        $oInputValidator = $this->getMock('oxInputValidator');
        $oInputValidator->expects($this->once())->method("checkVatId")->will($this->throwException(new oxInputException()));
        oxRegistry::set('oxInputValidator', $oInputValidator);

        $oUser = oxNew('oxUser');
        try {
            $oUser->checkValues("X", "X", "X", array(), array());
        } catch (oxInputException $oException) {
            return;
        }

        $this->fail('oxInputException should have been thrown!');
    }

    /**
     * Testing if auto group assignment works fine
     */
    // 1. testing if foreigner is automatically assigned/removed to/from special user groups
    public function testSetAutoGroupsForeigner()
    {
        $oUser = $this->getMock("oxUser", array("ingroup", "removefromgroup", "addtogroup"));
        $oUser->expects($this->once())->method("removeFromGroup");
        $oUser->expects($this->once())->method("addToGroup");
        $oUser->expects($this->exactly(2))->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(true)));

        $oUser->UNITsetAutoGroups('xxx', array());

    }

    // 2. testing if native country customer is automatically assigned/removed to/from special user groups
    public function testSetAutoGroupsNative()
    {
        $oUser = $this->getMock("oxUser", array("ingroup", "removefromgroup", "addtogroup"));
        $this->getConfig()->setConfigParam('aHomeCountry', 'xxx');
        $oUser->expects($this->once())->method("removeFromGroup");
        $oUser->expects($this->once())->method("addToGroup");
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(true), $this->returnValue(false)));

        $oUser->UNITsetAutoGroups('xxx');

    }

    public function testSetAutoGroupsNativeMultiple()
    {
        $oUser = $this->getMock("oxUser", array("ingroup", "removefromgroup", "addtogroup"));
        $this->getConfig()->setConfigParam('aHomeCountry', array('asd', 'xxx', 'ad'));
        $oUser->expects($this->once())->method("removeFromGroup");
        $oUser->expects($this->once())->method("addToGroup");
        $oUser->expects($this->any())->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(true), $this->returnValue(false)));

        $oUser->UNITsetAutoGroups('xxx');
    }

    /**
     * Testing if newsletter subscription setter is executed properly
     */

    public function testSetNewsSubscriptionSubscribesButOntInStatusEq1()
    {
        $oConfig = $this->getMock('oxconfig');

        $oSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'setOptInStatus'));
        $oSubscription->expects($this->once())->method('getOptInStatus')->will($this->returnValue(1));
        $oSubscription->expects($this->never())->method('setOptInStatus');

        $oUser = $this->getMock('oxuser', array('getNewsSubscription', 'addToGroup', 'removeFromGroup'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));
        $oUser->expects($this->never())->method('addToGroup');
        $oUser->expects($this->never())->method('removeFromGroup');
        $oUser->setConfig($oConfig);

        $this->assertFalse($oUser->setNewsSubscription(true, false));
    }

    public function testSetNewsSubscriptionSubscribesNoOptInEmail()
    {
        $oConfig = $this->getMock('oxconfig');
        $oConfig->setConfigParam('blOrderOptInEmail', false);

        $oSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'setOptInStatus'));
        $oSubscription->expects($this->once())->method('getOptInStatus')->will($this->returnValue(0));
        $oSubscription->expects($this->once())->method('setOptInStatus')->with($this->equalTo(1));

        $oUser = $this->getMock('oxuser', array('getNewsSubscription', 'addToGroup', 'removeFromGroup'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));
        $oUser->expects($this->once())->method('addToGroup')->with($this->equalTo('oxidnewsletter'));
        $oUser->expects($this->never())->method('removeFromGroup');
        $oUser->setConfig($oConfig);

        $this->assertTrue($oUser->setNewsSubscription(true, false));
    }

    public function testSetNewsSubscriptionSubscribesWithOptInEmail()
    {
        oxEmailHelper::$blRetValue = true;
        oxAddClassModule('oxEmailHelper', 'oxemail');

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blOrderOptInEmail', true);

        $oSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'setOptInStatus'));
        $oSubscription->expects($this->once())->method('getOptInStatus')->will($this->returnValue(0));
        $oSubscription->expects($this->once())->method('setOptInStatus')->with($this->equalTo(2));

        $oUser = $this->getMock('oxuser', array('getNewsSubscription', 'addToGroup', 'removeFromGroup'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));
        $oUser->expects($this->never())->method('addToGroup');
        $oUser->expects($this->never())->method('removeFromGroup');
        $oUser->setConfig($oConfig);

        $this->assertTrue($oUser->setNewsSubscription(true, true));
    }

    public function testSetNewsSubscriptionSubscribesWithOptInEmail_sendsOnlyOnce()
    {
        // email should be sent only once
        $oEmail = $this->getMock('oxemail', array('sendNewsletterDBOptInMail'));
        $oEmail->expects($this->once())->method('sendNewsletterDBOptInMail')->will($this->returnValue(true));

        oxTestModules::addModuleObject("oxemail", $oEmail);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blOrderOptInEmail', true);

        $oSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'setOptInStatus'));
        $oSubscription->expects($this->at(0))->method('getOptInStatus')->will($this->returnValue(0));
        $oSubscription->expects($this->at(1))->method('setOptInStatus')->with($this->equalTo(2));
        $oSubscription->expects($this->at(2))->method('getOptInStatus')->will($this->returnValue(2));
        $oSubscription->expects($this->at(3))->method('setOptInStatus')->with($this->equalTo(2));

        $oUser = $this->getMock('oxuser', array('getNewsSubscription', 'addToGroup', 'removeFromGroup'));
        $oUser->expects($this->any())->method('getNewsSubscription')->will($this->returnValue($oSubscription));
        $oUser->setConfig($oConfig);

        // first call, mail should be sent
        $this->assertTrue($oUser->setNewsSubscription(true, true));

        // second call, mail should not be sent
        $this->assertTrue($oUser->setNewsSubscription(true, true));
    }

    public function testSetNewsSubscriptionUnsubscribes()
    {
        oxEmailHelper::$blRetValue = true;
        oxAddClassModule('oxEmailHelper', 'oxemail');

        $oConfig = $this->getMock('oxconfig');

        $oSubscription = $this->getMock('oxnewssubscribed', array('getOptInStatus', 'setOptInStatus'));
        $oSubscription->expects($this->never())->method('getOptInStatus');
        $oSubscription->expects($this->once())->method('setOptInStatus')->with($this->equalTo(0));

        $oUser = $this->getMock('oxuser', array('getNewsSubscription', 'addToGroup', 'removeFromGroup'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));
        $oUser->expects($this->never())->method('addToGroup');
        $oUser->expects($this->once())->method('removeFromGroup')->with($this->equalTo('oxidnewsletter'));
        $oUser->setConfig($oConfig);

        $this->assertTrue($oUser->setNewsSubscription(false, false));
    }

    /**
     * oxuser::loadAdminUser() test
     */
    public function testLoadAdminUser()
    {
        oxAddClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper', 'oxUtilsServer');
        //not logged in
        $oUser = oxNew('oxUser');
        $this->assertFalse($oUser->loadAdminUser());
        //logging in
        $oAdminUser = oxNew('oxUser');
        $oAdminUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oActiveUser = oxNew('oxUser');
        $oActiveUser->loadAdminUser();

        $this->assertNull($oActiveUser->oxuser__oxusername->value);

        $oAdminUser = $this->getMock('oxuser', array('isAdmin'));
        $oAdminUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oAdminUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);

        $oActiveUser->loadAdminUser();

        $this->assertEquals($oActiveUser->oxuser__oxusername->value, oxADMIN_LOGIN);
        $oAdminUser->logout();
        $oUser = oxNew('oxUser');
        $this->assertFalse($oUser->loadAdminUser());
    }

    /**
     * oxuser::getUser() test
     */
    public function testGetUser()
    {
        //not logged in
        $oActUser = oxNew('oxUser');
        $this->assertFalse($oActUser->loadActiveUser());
        $testUser = $this->getMock('oxuser', array('isAdmin'));
        $testUser->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        //trying to login
        $testUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oActUser->loadActiveUser();
        $testUser->logout();
        $this->assertEquals($oActUser->oxuser__oxusername->value, oxADMIN_LOGIN);
    }

    /**
     * oxuser::getUser() test.
     * Will set 'oxdefaultadmin' password to oxADMIN_PASSWD variable value,
     * and will reset it back after the test.
     */
    public function testGetUserNotAdmin()
    {
        oxAddClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper2', 'oxutilsserver');
        $sShopId = $this->getConfig()->getShopId();
        $sTempPassword = oxADMIN_PASSWD;

        //not logged in
        $oActUser = oxNew('oxUser');
        $this->assertFalse($oActUser->loadActiveUser());
        $testUser = $this->getMock('oxuser', array('isAdmin'));
        $testUser->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $aResults = $this->getDb(oxDb::FETCH_MODE_ASSOC)->getAll('select OXPASSSALT, OXPASSWORD from oxuser where OXID="oxdefaultadmin"');
        $sPassSalt = $aResults[0]['OXPASSSALT'];
        $sOriginalPassword = $aResults[0]['OXPASSWORD'];
        $sTemporaryPassword = $oActUser->encodePassword($sOriginalPassword, $sPassSalt);
        $sSql = "update oxuser set OXPASSWORD = '{$sTemporaryPassword}'  where OXID='oxdefaultadmin'";
        $this->addToDatabase($sSql, 'oxuser');
        $sVal = oxADMIN_LOGIN . '@@@' . crypt($sTemporaryPassword, $sPassSalt);
        oxRegistry::get("oxUtilsServer")->setOxCookie('oxid_' . $sShopId, $sVal);

        $oActUser->loadActiveUser();
        $testUser->logout();

        $sSql = "update oxuser set OXPASSWORD = '{$sOriginalPassword}' where OXID='oxdefaultadmin'";
        $this->addToDatabase($sSql, 'oxuser');

        $this->assertEquals($oActUser->oxuser__oxusername->value, oxADMIN_LOGIN);
    }

    /**
     * oxuser::login() test. Checks if login process throws an exception when cookies are not
     * supported for admin.
     */
    public function testLogin_AdminCookieSupport()
    {
        oxAddClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper2', 'oxUtilsServer');
        $oUser = $this->getMock('oxuser', array('isAdmin'));
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        oxRegistry::get("oxUtilsServer")->delOxCookie();
        try {
            //should throw no cookie support exception
            $oUser->login(1, oxADMIN_PASSWD);
        } catch (Exception $e) {
            return;
        }

        $this->fail("Mandatory admin cookies are not checked");
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLogin_Logout()
    {
        $oUser = oxNew('oxUser');
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $this->assertEquals(oxRegistry::getSession()->getVariable('usr'), 'oxdefaultadmin');
        $this->assertNull(oxRegistry::getSession()->getVariable('auth'));

        $oUser = $oUser->getUser();

        $this->assertNotNull($oUser);
        $this->assertEquals('oxdefaultadmin', $oUser->getId());

        $oUser->logout();

        $this->assertNull(oxRegistry::getSession()->getVariable('usr'));
        $this->assertNull(oxRegistry::getSession()->getVariable('auth'));
        $this->assertFalse($oUser->getUser());
    }

    /**
     * oxuser::login() - restets active user on login
     */
    public function testLogin_resetsActiveUser()
    {
        $oUser = $this->getMock("oxuser", array("setUser"));
        $oUser->expects($this->once())->method("setUser")->with($this->equalTo(null));

        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginByPassingCustomerNumberNotAllowed()
    {
        $oUser = oxNew('oxUser');
        try {
            $oUser->login(1, oxADMIN_PASSWD);
        } catch (Exception $oExcp) {
            $this->assertEquals('ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage());

            return;
        }
        $this->fail('exception must be thrown');
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginButUnableToLoadExceptionWillBeThrown()
    {
        $oUser = $this->getMock('oxuser', array('load'));
        $oUser->expects($this->atLeastOnce())->method('load')->will($this->returnValue(false));

        try {
            $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        } catch (Exception $oExcp) {

            $this->assertEquals('ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage());

            return;
        }
        $this->fail('exception must be thrown due to problems loading user object');
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginOxidNotSet()
    {
        $oUser = $this->getMock('oxuser', array('load', '_ldapLogin'));
        $oUser->expects($this->atLeastOnce())->method('load')->will($this->returnValue(true));

        try {
            $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        } catch (Exception $oExcp) {
            $this->assertEquals('ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage());

            return;
        }
        $this->fail('exception must be thrown due to problems loading user object');
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginCookieMustBeSet()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setUserCookie', '{ throw new Exception( "cookie is set" ); }');

        $oUser = oxNew('oxUser');
        try {
            $this->assertTrue($oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD, true));
        } catch (Exception $oExcp) {
            $this->assertEquals("cookie is set", $oExcp->getMessage());

            return;
        }
        $this->fail('forced exception must be thrown');
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginCookie_disabled()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setUserCookie', '{ throw new Exception( "cookie is set" ); }');
        $this->getConfig()->setConfigParam('blShowRememberMe', 0);

        $oUser = oxNew('oxUser');
        try {
            $this->assertTrue($oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD, true));
        } catch (Exception $oExcp) {
            $this->fail('Cookie should not be set, it\'s disabled.');

            return;
        }
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginIsDemoAndAdminButNonAdminUser_Logout()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{ return ""; }');
        $this->getConfig()->setConfigParam('blDemoShop', 1);

        $oUser = $this->getMock('oxuser', array('isAdmin'));
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));

        try {
            $oUser->login('nonadmin', oxADMIN_PASSWD);
        } catch (Exception $oExcp) {
            $this->assertEquals('ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage());

            return;
        }
        $this->fail('exception must be thrown');
    }

    /**
     * oxUser::login() and oxUser::logout() test for demo shop
     */
    public function testLogin_Logout_AdminDemoShop()
    {
        $oConfig = $this->getConfig();

        oxAddClassModule('Unit\Application\Model\UserTest_oxUtilsServerHelper', 'oxutilsserver');
        $oConfig->setConfigParam('blDemoShop', 1);
        $oConfig->setAdminMode(true);

        $oUser = oxNew('oxUser');
        // demo shop login data: admin/admin here
        $oUser->login("admin", "admin");

        $this->assertNotNull($this->getSessionParam('auth'));

        // 'usr' var should not be set here in admin
        $this->assertNull($this->getSessionParam('usr'));

        $oUser = $oUser->getUser();

        $this->assertNotNull($oUser);
        $this->assertNotNull($oUser->getId());

        $oUser->logout();
        $this->assertNull($this->getSessionParam('usr'));
        $this->assertNull($this->getSessionParam('auth'));
        $this->assertFalse($oUser->getUser());

    }

    /**
     * oxuser::logout() test
     */
    public function testLogout()
    {
        $oUser = oxNew('oxUser');
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);

        $this->getSession()->setVariable('dynvalue', 'test');
        $this->getSession()->setVariable('paymentid', 'test');

        $oUser = $oUser->getUser();

        if ($oUser) {
            $this->assertNotNull($oUser);
            $this->assertEquals('oxdefaultadmin', $oUser->getId());

            $oUser->logout();

            $this->assertNull(oxRegistry::getSession()->getVariable('dynvalue'));
            $this->assertNull(oxRegistry::getSession()->getVariable('paymentid'));
            $this->assertFalse($oUser->getUser());
        } else {
            $this->fail('User not loaded');
        }
    }

    /**
     * Address assignment test
     */
    // trying to set empty address
    public function testAssignAddressNoAddressIsSet()
    {
        $this->getSession()->setVariable('deladrid', 'xxx');
        $aDelAddress = array();

        $oUser = oxNew('oxUser');
        $oUser->UNITassignAddress($aDelAddress);

        $this->assertNull(oxRegistry::getSession()->getVariable('deladrid'));
    }

    // trying to set non empty address
    public function testAssignAddress()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';
        $aDelAddress['oxaddress__oxcountryid'] = 'a7c40f631fc920687.20179984';

        $this->setRequestParameter('oxaddressid', 'xxx');

        $oUser->UNITassignAddress($aDelAddress);
        $oDb = $this->getDb();
        $sSelect = 'select oxaddress.oxcountry from oxaddress where oxaddress.oxid = "xxx" AND oxaddress.oxuserid = "' . $sUserId . '" ';

        $sCountry = $oDb->getOne($sSelect);
        $this->assertEquals('xxx', oxRegistry::getSession()->getVariable('deladrid'));
        $this->assertEquals('Deutschland', $sCountry);
    }

    // trying to set non empty address
    public function testAssignAddressWithSpecialChar()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';
        $aDelAddress['oxaddress__oxcountryid'] = 'a7c40f631fc920687.20179984';
        $aDelAddress['oxaddress__oxcompany'] = 'xxx & CO.';

        $this->setRequestParameter('oxaddressid', 'xxx');

        $oUser->UNITassignAddress($aDelAddress);
        $oDb = $this->getDb();
        $this->assertEquals('xxx', oxRegistry::getSession()->getVariable('deladrid'));
        $sSelect = 'select oxaddress.oxcompany from oxaddress where oxaddress.oxuserid = "' . $sUserId . '" AND oxid = "xxx" ';

        $sCompany = $oDb->getOne($sSelect);
        $this->assertEquals('xxx & CO.', $sCompany);
    }

    /**
     * oxuser::getSelectedAddress() test
     */
    public function testGetSelectedAddress()
    {
        $oUser = $this->createUser();

        $this->setRequestParameter('deladrid', null);
        $this->setRequestParameter('oxaddressid', 'test_user1');

        $oAddress = $oUser->getSelectedAddress();
        $this->assertEquals('test_user1', $oAddress->getId());
    }

    /**
     * oxuser::getSelectedAddress() test
     */
    public function testGetSelectedAddressNewAddress()
    {
        $oUser = $this->createUser();

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';

        $this->setRequestParameter('deladrid', null);
        $this->setRequestParameter('oxaddressid', 'xxx');

        $oUser->UNITassignAddress($aDelAddress);

        $this->getSession()->setVariable('oxaddressid', null);
        $oAddress = $oUser->getSelectedAddress();
        $this->assertEquals('xxx', $oAddress->getId());
    }

    /**
     * oxuser::getSelectedAddress() if address is not selected
     */
    public function testGetSelectedAddressNotSelected()
    {
        $oUser = $this->createUser();

        $this->getSession()->setVariable('deladrid', null);
        $this->getSession()->setVariable('oxaddressid', null);
        $oSelAddress = $oUser->getSelectedAddress();
        $oUser->oAddresses->rewind();
        $oAddress = $oUser->oAddresses->current();
        $this->assertEquals($oAddress->getId(), $oSelAddress->getId());
        $this->assertEquals(1, $oAddress->selected);
    }

    /**
     * oxuser::getSelectedAddress() if article
     * from wishlist is added, load wishid address
     */
    public function testGetSelectedAddressWishId()
    {
        $oUser = $this->createUser();

        $this->getSession()->setVariable('deladrid', null);
        $this->getSession()->setVariable('oxaddressid', null);

        $oSelAddress = $oUser->getSelectedAddress($oUser->getId());
        $this->assertEquals('test_user1', $oSelAddress->getId());
    }

    public function testGetNoticeListArtCnt()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = $this->getProxyClass("oxuser");
        $oUser->load($sUserId);

        $oBasket = $this->getMock('oxbasket', array('getItemCount'));
        $oBasket->expects($this->once())->method('getItemCount')->will($this->returnValue(11));
        $aBaskets['noticelist'] = $oBasket;
        $oUser->setNonPublicVar('_aBaskets', $aBaskets);

        $this->assertEquals(11, $oUser->getNoticeListArtCnt());
    }

    public function testGetWishListArtCnt()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = $this->getProxyClass("oxuser");
        $oUser->load($sUserId);

        $oBasket = $this->getMock('oxbasket', array('getItemCount'));
        $oBasket->expects($this->once())->method('getItemCount')->will($this->returnValue(11));
        $aBaskets['wishlist'] = $oBasket;
        $oUser->setNonPublicVar('_aBaskets', $aBaskets);

        $this->assertEquals(11, $oUser->getWishListArtCnt());
    }

    /**
     * Testing encoding of delivery address.
     * Checks whether it generates different hashes for different data and
     * eqal hashes for eqal data.
     *
     * @return null
     */
    public function testGetEncodedDeliveryAddress()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxcompany = new oxField('Company');
        $oUser->oxuser__oxfname = new oxField('First name');
        $oUser->oxuser__oxlname = new oxField('Last name');
        $oUser->oxuser__oxstreet = new oxField('Street');
        $oUser->oxuser__oxstreetnr = new oxField('Street number');
        $sEncoded = $oUser->getEncodedDeliveryAddress();

        $oUser->oxuser__oxstreetnr = new oxField('Street 41');

        $this->assertNotEquals($sEncoded, $oUser->getEncodedDeliveryAddress());

        $oUser->oxuser__oxstreetnr = new oxField('Street number');

        $this->assertEquals($sEncoded, $oUser->getEncodedDeliveryAddress());
    }

    public function testIsLoadedFromCookie()
    {
        $oUser = $this->getProxyClass("oxuser");
        $oUser->setNonPublicVar('_blLoadedFromCookie', true);

        $this->assertTrue($oUser->isLoadedFromCookie());
    }

    /**
     * oxuser::getUserCountryId()
     */
    public function testGetUserCountryId()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals("a7c40f631fc920687.20179984", $oUser->getUserCountryId('DE'));
    }

    /**
     * oxuser::getUserCountry()
     */
    public function testGetUserCountryWithId()
    {
        $oUser = $this->getProxyClass("oxUser");
        $this->assertEquals("Deutschland", $oUser->getUserCountry("a7c40f631fc920687.20179984")->value);
        $this->assertNull($oUser->getNonPublicVar("_oUserCountryTitle"));
    }

    /**
     * oxuser::getUserCountry()
     */
    public function testGetUserCountry()
    {
        $oUser = $this->getProxyClass("oxUser");
        $oUser->load('oxdefaultadmin');
        $this->assertEquals("Deutschland", $oUser->getUserCountry()->value);
        $this->assertEquals("Deutschland", $oUser->getNonPublicVar("_oUserCountryTitle")->value);
        $this->assertEquals($oUser->getUserCountry()->value, $this->getDb()->getOne('select oxtitle' . oxRegistry::getLang()->getLanguageTag(null) . ' from oxcountry where oxid = "' . $oUser->oxuser__oxcountryid->value . '"'));
    }

    public function testGetReviewUserHash()
    {
        $sReviewUser = $this->getDb()->getOne('select md5(concat("oxid", oxpassword, oxusername )) from oxuser where oxid = "oxdefaultadmin"');
        $oUser = $this->getProxyClass("oxuser");

        $this->assertEquals($sReviewUser, $oUser->getReviewUserHash('oxdefaultadmin'));
    }

    public function testGetReviewUserId()
    {
        $sReviewUser = $this->getDb()->getOne('select md5(concat("oxid", oxpassword, oxusername )) from oxuser where oxid = "oxdefaultadmin"');
        $oUser = $this->getProxyClass("oxuser");

        $this->assertEquals('oxdefaultadmin', $oUser->getReviewUserId($sReviewUser));
    }

    /**
     * oxuser::laodActiveUser() test loading active user via cookie
     * when user exists and cookie info is correct
     */
    public function testLoadActiveUser_CookieLogin()
    {
        $this->getConfig()->setConfigParam("blShowRememberMe", true);

        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = oxNew('oxUser');
        $oUser->load($sUserId);
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->setPassword('testPassword');
        $oUser->save();

        oxRegistry::get("oxUtilsServer")->setUserCookie(
            $oUser->oxuser__oxusername->value,
            $oUser->oxuser__oxpassword->value, null, 31536000, $oUser->oxuser__oxpasssalt->value
        );

        $sCookie = oxRegistry::get("oxUtilsServer")->getUserCookie();

        $testUser = $this->getMock('oxuser', array('isAdmin'));
        $testUser->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertTrue($testUser->loadActiveUser());

        $this->assertEquals($sCookie, oxRegistry::get("oxUtilsServer")->getUserCookie());
    }

    /**
     * oxuser::laodActiveUser() test loading active user via cookie
     * when user defined in cookie is not found
     */
    public function testLoadActiveUser_CookieResetting()
    {
        $this->getConfig()->setConfigParam("blShowRememberMe", true);

        oxRegistry::get("oxUtilsServer")->setUserCookie('RandomUserId', 'RandomPassword');

        $testUser = $this->getMock('oxuser', array('isAdmin'));
        $testUser->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertFalse($testUser->loadActiveUser());

        $this->assertNull(oxRegistry::get("oxUtilsServer")->getUserCookie());
    }

    public function testGetWishListId()
    {
        $oBasketItem = $this->getMock('oxBasketItem', array('getWishId'));
        $oBasketItem->expects($this->once())->method('getWishId')->will($this->returnValue("testwishid"));
        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oUserView = $this->getMock('oxuser', array('getSession'));
        $oUserView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $this->assertEquals("testwishid", $oUserView->UNITgetWishListId());
    }

    /**
     * Testing method updateInvitationStatistics()
     *
     * @return null
     */
    public function testUpdateInvitationStatistics()
    {
        $aRecEmails = array("test1@oxid-esales.com", "test2@oxid-esales.com");

        $oUser = $this->getProxyClass('oxuser');
        $oUser->load("oxdefaultadmin");
        $oUser->updateInvitationStatistics($aRecEmails);

        $aRec = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("select * from oxinvitations order by oxemail");

        $this->assertEquals("oxdefaultadmin", $aRec[0]["OXUSERID"]);
        $this->assertEquals("test1@oxid-esales.com", $aRec[0]["OXEMAIL"]);
        $this->assertEquals("1", $aRec[0]["OXPENDING"]);
        $this->assertEquals("0", $aRec[0]["OXACCEPTED"]);
        $this->assertEquals("1", $aRec[0]["OXTYPE"]);

        $this->assertEquals("oxdefaultadmin", $aRec[1]["OXUSERID"]);
        $this->assertEquals("test2@oxid-esales.com", $aRec[1]["OXEMAIL"]);
        $this->assertEquals("1", $aRec[1]["OXPENDING"]);
        $this->assertEquals("0", $aRec[1]["OXACCEPTED"]);
        $this->assertEquals("1", $aRec[1]["OXTYPE"]);
    }

    /**
     * Test case for #0002616: oxuser: addToGroup and inGroup inconsistent
     *
     * @return null
     */
    public function testAddToGroupFor0002616()
    {
        $oUser = $this->createUser();
        $sUserId = $oUser->getId();

        $oUser = $this->getMock("oxuser", array("inGroup"));
        $oUser->expects($this->any())->method('inGroup')->will($this->returnValue(false));
        $oUser->load($sUserId);

        $oGroup = oxNew('oxGroups');
        $oGroup->setId('_testGroup');
        $oGroup->oxgroups__oxtitle = new oxfield('_testGroup');
        $oGroup->oxgroups__oxactive = new oxfield(1);
        $oGroup->save();

        $this->assertTrue($oUser->addToGroup("_testGroup"));
        $this->assertFalse($oUser->addToGroup("nonsense"));
    }


    public function testGetIdByUserName()
    {
        $oUser = oxNew('oxUser');
        $oUser->setId("_testId_1");
        $oUser->oxuser__oxusername = new oxField("aaa@bbb.lt", oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->save();

        $oUser = oxNew('oxUser');
        $oUser->setId("_testId_2");
        $oUser->oxuser__oxusername = new oxField("bbb@ccc.lt", oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField('xxx');
        $oUser->save();

        $oU = oxNew('oxUser');

        $this->getConfig()->setConfigParam('blMallUsers', false);
        $this->assertEquals('_testId_1', $oU->getIdByUserName('aaa@bbb.lt'));
        $this->assertFalse($oU->getIdByUserName('bbb@ccc.lt'));

        $this->getConfig()->setConfigParam('blMallUsers', true);
        $this->assertEquals('_testId_1', $oU->getIdByUserName('aaa@bbb.lt'));
        $this->assertEquals('_testId_2', $oU->getIdByUserName('bbb@ccc.lt'));
    }


    public function testIsPriceViewModeNetto()
    {
        $oUser = oxNew('oxUser');

        $this->getConfig()->setConfigParam('blShowNetPrice', false);
        $this->assertFalse($oUser->isPriceViewModeNetto());

        $this->getConfig()->setConfigParam('blShowNetPrice', true);
        $this->assertTrue($oUser->isPriceViewModeNetto());
    }

    /**
     * Test configurable user credit rating (getBoni());
     * Config option for this: iCreditRating;
     */
    public function testUserCreditRating()
    {
        $oUser = oxNew('oxUser');
        $this->assertEquals(1000, $oUser->getBoni());

        $this->getConfig()->setConfigParam('iCreditRating', 100);
        $this->assertEquals(100, $oUser->getBoni());
    }

    /**
     * Testing state ID getter
     */
    public function testGetStateId()
    {
        $oSubj = oxNew('oxUser');
        $oSubj->oxuser__oxstateid = new oxField('TTT');
        $this->assertEquals('TTT', $oSubj->getStateId());
    }

    /**
     * Testing state title getter by ID
     */
    public function testGetStateTitleById()
    {
        $iStateId = 'CA';
        $iAlternateStateId = 'AK';

        /** @var oxState|PHPUnit_Framework_MockObject_MockObject $oStateMock */
        $oStateMock = $this->getMock('oxState', array('getTitleById'));

        $oStateMock->expects($this->at(0))
            ->method('getTitleById')
            ->with($iStateId)
            ->will($this->returnValue('Kalifornien'));

        $oStateMock->expects($this->at(1))
            ->method('getTitleById')
            ->with($iAlternateStateId)
            ->will($this->returnValue('Alaska'));

        $oStateMock->expects($this->at(2))
            ->method('getTitleById')
            ->with($iStateId)
            ->will($this->returnValue('California'));

        $oStateMock->expects($this->at(3))
            ->method('getTitleById')
            ->with($iAlternateStateId)
            ->will($this->returnValue('Alaska'));

        /** @var oxUser|PHPUnit_Framework_MockObject_MockObject $oUserMock */
        $oUserMock = $this->getMock('oxUser', array('_getStateObject', 'getStateId'));

        $oUserMock->expects($this->any())
            ->method('_getStateObject')
            ->will($this->returnValue($oStateMock));

        $oUserMock->expects($this->any())
            ->method('getStateId')
            ->will($this->returnValue($iAlternateStateId));

        $sExpected = $this->getDb()->getOne('SELECT oxtitle FROM oxstates WHERE oxid = "' . $iStateId . '"');
        $this->assertSame($sExpected, $oUserMock->getStateTitle($iStateId), "State title is correct");

        $sExpected = $this->getDb()->getOne('SELECT oxtitle FROM oxstates WHERE oxid = "' . $iAlternateStateId . '"');
        $this->assertSame($sExpected, $oUserMock->getStateTitle(), "State title is correct when ID is not passed");

        $this->setLanguage(1);

        $sExpected = $this->getDb()->getOne('SELECT oxtitle_1 FROM oxstates WHERE oxid = "' . $iStateId . '"');
        $this->assertSame($sExpected, $oUserMock->getStateTitle($iStateId), "State title is correct");

        $sExpected = $this->getDb()->getOne(
            'SELECT oxtitle_1 FROM oxstates WHERE oxid = "' . $iAlternateStateId . '"'
        );
        $this->assertSame($sExpected, $oUserMock->getStateTitle(), "State title is correct when ID is not passed");
    }
}
