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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Extending mailer
 */
class oxuserTestEmail extends oxemail
{
    static $blSend = true;
    public function sendNewsletterDBOptInMail( $oUser, $sSubject = null )
    {
       return self::$blSend;
    }
}


class oxuserTestonlinevatidcheck
{
    public static $exception = null;
    public function checkUID( $sParam1 )
    {
        if (self::$exception)
            throw self::$exception;
        else
            throw new Exception( 'OK' );
    }
}

class oxuserTest_oxnewssubscribed extends oxnewssubscribed
{
    public $loadFromUserID;
    public $loadFromEMail;
    public function loadFromUserID( $sOXID )
    {
        if ( $sOXID == 'oxid' ) {
            $this->loadFromUserID = true;
        }
    }
    public function loadFromEMail( $sEmail )
    {
        if ( $sEmail == 'email' )
            $this->loadFromEMail = true;
    }
}


class Unit_oxuserTest_oxutils2 extends oxutils
{
    public function isValidEmail( $sEmail )
    {
        return false;
    }
}
class Unit_oxuserTest_oxUtilsServer extends oxUtilsServer
{
    public function getOxCookie( $sName = null )
    {
        return true;
    }
}
class Unit_oxuserTest_oxUtilsServer2 extends oxUtilsServer
{

    /**
     * $_COOKIE alternative for testing
     *
     * @var array
     */
    protected $_aCookieVars = array();

    public function setOxCookie( $sVar, $sVal = "", $iExpire = 0, $sPath = '/', $sDomain = null, $blToSession = true, $blSecure = false )
    {
        //unsetting cookie
        if (!isset($sVar) && !isset($sVal)) {
            $this->_aCookieVars = null;
            return;
        }

        $this->_aCookieVars[$sVar] = $sVal;
    }

    public function getOxCookie( $sVar = null )
    {
        if (!$sVar)
            return $this->_aCookieVars;

        if ($this->_aCookieVars[$sVar])
            return $this->_aCookieVars[$sVar];

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
class Unit_Core_oxuserTest extends OxidTestCase
{
    protected $_aShops = array();
    protected $_aUsers = array();

    protected $_aDynPaymentFields = array( 'kktype'  => 'Test Bank',
                                          'kknumber'=> '123456',
                                          'kkmonth' => '123456',
                                          'kkyear'  => 'Test User',
                                          'kkname'  => 'Test Name',
                                          'kkpruef' => '123456');

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxuserTestonlinevatidcheck::$exception = null;
        $myDB = oxDb::getDB();

        // selecting shop IDs
        $sQ = 'select oxid from oxshops order by oxid';
        $rs = $myDB->Execute( $sQ );
        if ( $rs != false && $rs->RecordCount() > 0 ) {
            while ( !$rs->EOF ) {
                $this->_aShops[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        // setting up users
        foreach ( $this->_aShops as $sShopID ) {
            $this->setupUsers( $sShopID );
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oActUser = new oxuser();
        if ( $oActUser->loadActiveUser() ) {
            $oActUser->logout();
        }

        oxSession::setVar( 'usr', null );
        oxSession::setVar( 'auth', null );

        // resetting globally admin mode
        $oUser = new oxUser();
        $oUser->setAdminMode( null );
        oxSession::deleteVar('deladrid');

        // removing email wrapper module
        oxRemClassModule( 'oxuserTest_oxnewssubscribed' );
        oxRemClassModule( 'oxuserTestonlinevatidcheck' );
        oxRemClassModule( 'Unit_oxuserTest_oxutils2' );
        oxRemClassModule( 'Unit_oxuserTest_oxUtilsServer' );
        oxRemClassModule( 'Unit_oxuserTest_oxUtilsServer2' );
        oxRemClassModule( 'oxuserTestEmail' );

        $oGroup = new oxgroups();
        $oGroup->delete( '_testGroup' );

        $oGroup = new oxgroups();
        $oGroup->delete( '_testGroup' );

        $oUser = oxNew( 'oxuser' );

        // removing users
        foreach ( $this->_aUsers as $aShopUsers ) {
            foreach ( $aShopUsers as $sUserId ) {
                $oUser->delete( $sUserId );
            }
        }

        // restore database
        $oDbRestore = self::_getDbRestore();
        $oDbRestore->restoreDB();

        parent::tearDown();
    }

    /**
     * Setting up users
     */
    protected function setupUsers( $sShopID )
    {
        $myUtils  = oxUtils::getInstance();
        $myConfig = oxConfig::getInstance();
        $myDB     = oxDb::getDB();

        // loading user groups
        $sQ = 'select oxid from oxgroups';
        $rs = $myDB->Execute( $sQ );
        if ( $rs != false && $rs->RecordCount() > 0 ) {
            while ( !$rs->EOF ) {
                $this->aGroupIds[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        $aActives = array( '0', '1' );
        $aRights  = array( 'user', 'malladmin' );
        $sTable   = getViewName( 'oxuser' );
        $iLastCustNr = 0;//( int ) $myDB->getOne( 'select max( oxcustnr ) from '.$sTable ) + 1;
        $sCountryId  = $myDB->getOne( 'select oxid from oxcountry where oxactive = "1"' );

        for ( $iCnt = 0; $iCnt < 5; $iCnt++ ) {
            $oUser = oxNew( 'oxuser' );
            $oUser->oxuser__oxshopid = new oxField($sShopID, oxField::T_RAW);

            // marking as active
            $iActive = $aActives[ rand( 0, count( $aActives ) - 1 ) ];
            $oUser->oxuser__oxactive = new oxField($iActive, oxField::T_RAW);

            // setting rights
            $sRights = $aRights[ rand( 0, count( $aRights ) - 1 ) ];
            $oUser->oxuser__oxrights = new oxField($sRights, oxField::T_RAW);

            // setting name
            $iLastCustNr++;
            $oUser->oxuser__oxusername = new oxField('test'.$iLastCustNr.'@oxid-esales.com', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField(crc32( 'Test'.$sRights. ''.$sShopID.'@oxid-esales.com' ), oxField::T_RAW);
            //$oUser->oxuser__oxcustnr = new oxField($iLastCustNr, oxField::T_RAW);
            $oUser->oxuser__oxcountryid = new oxField("testCountry", oxField::T_RAW);
            $oUser->save();

            $this->_aUsers[ $sShopID ][] = $oUser->getId();

            $sGroupId = $this->aGroupIds[ rand( 0, count( $this->aGroupIds ) - 1 ) ];
            $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "'.$oUser->getId().'", "'.$sShopID.'", "'.$oUser->getId().'", "'.$sGroupId.'" )';
            $myDB->Execute( $sQ );

             $sId = oxUtilsObject::getInstance()->generateUID();

            $sQ = 'insert into oxorder ( oxid, oxshopid, oxuserid, oxorderdate ) values ( "'.$sId.'", "'.$sShopID.'", "'.$oUser->getId().'", "'.date( 'Y-m-d  H:i:s', time() + 3600 ).'" ) ';
            $myDB->Execute( $sQ );

            // adding article to order
            $sArticleID = $myDB->getOne( 'select oxid from oxarticles order by rand() ' );
            $sQ = 'insert into oxorderarticles ( oxid, oxorderid, oxamount, oxartid, oxartnum ) values ( "'.$sId.'", "'.$sId.'", 1, "'.$sArticleID.'", "'.$sArticleID.'" ) ';
            $myDB->Execute( $sQ );

            // adding article to basket
            $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle ) values ( "'.$oUser->getId().'", "'.$oUser->getId().'", "oxtest" ) ';
            $myDB->Execute( $sQ );

            $sArticleID = $myDB->getOne( 'select oxid from oxarticles order by rand() ' );
            $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "'.$oUser->getId().'", "'.$oUser->getId().'", "'.$sArticleID.'", "1" ) ';
            $myDB->Execute( $sQ );

            // creating test address
            $sQ = 'insert into oxaddress ( oxid, oxuserid, oxaddressuserid, oxcountryid ) values ( "test_user'.$iCnt.'", "'.$oUser->getId().'", "'.$oUser->getId().'", "'.$sCountryId.'" ) ';
            $myDB->Execute( $sQ );

            // creating test executed user payment
            $aDynvalue = $this->_aDynPaymentFields;
            $oPayment = oxNew( 'oxpayment' );
            $oPayment->load( 'oxidcreditcard');
            $oPayment->setDynValues($myUtils->assignValuesFromText( $oPayment->oxpayments__oxvaldesc->value, true, true, true));
            $aDynValues = $oPayment->getDynValues();
            while (list($key, $oVal) = each($aDynValues )) {
                $oVal = new oxField($aDynvalue[$oVal->name], oxField::T_RAW);
                $oPayment->setDynValue($key, $oVal);
                $aDynVal[$oVal->name] = $oVal->value;
            }
            $sDynValues = '';
            if( isset( $aDynVal))
                $sDynValues = $myUtils->assignValuesToText( $aDynVal);

            $sQ = 'insert into oxuserpayments ( oxid, oxuserid, oxpaymentsid, oxvalue ) values ( "'.$sId.'", "'.$oUser->oxuser__oxid->value.'", "oxidcreditcard", "'.$sDynValues.'" ) ';
            $myDB->Execute( $sQ );
        }
    }

    /**
     * oxUser::getOrders() test case when paging is on
     *
     * @return null
     */
    public function testGetOrdersWhenPagingIsOn()
    {
        $myUtils  = oxUtilsObject::getInstance();
        $myDB     = oxDb::getDB();

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        $oUser = new oxuser();
        $oUser->load( $sUserId );

        $sShopID = $oUser->getShopId();

        // adding some more orders..
        for ( $i = 0; $i < 21; $i++ ) {
            $sId = $myUtils->generateUID();

            $sQ = 'insert into oxorder ( oxid, oxshopid, oxuserid, oxorderdate ) values ( "'.$sId.'", "'.$sShopID.'", "'.$oUser->getId().'", "'.date( 'Y-m-d  H:i:s', time() + 3600 ).'" ) ';
            $myDB->Execute( $sQ );

            // adding article to order
            $sArticleID = $myDB->getOne( 'select oxid from oxarticles order by rand() ' );
            $sQ = 'insert into oxorderarticles ( oxid, oxorderid, oxamount, oxartid, oxartnum ) values ( "'.$sId.'", "'.$sId.'", 1, "'.$sArticleID.'", "'.$sArticleID.'" ) ';
            $myDB->Execute( $sQ );
        }

        $iTotal = $myDB->getOne( "select count(*) from oxorder where oxshopid = '{$sShopID}' and oxuserid = '{$sUserId}'" );

        $oOrders = $oUser->getOrders( 10, 0 );
        $this->assertEquals( 10, $oOrders->count() );
        $iTotal -= 10;

        $oOrders = $oUser->getOrders( 10, 1 );
        $this->assertEquals( 10, $oOrders->count() );
        $iTotal -= 10;

        $oOrders = $oUser->getOrders( 10, 2 );
        $this->assertEquals( $iTotal, $oOrders->count() );

        $oOrders = $oUser->getOrders( 10, 3 );
        $this->assertEquals( 0, $oOrders->count() );
    }

    /**
     * oxUser::setCreditPointsForRegistrant() test case
     *
     * @return null
     */
    public function testSetCreditPointsForRegistrant()
    {
        $sDate = oxUtilsDate::getInstance()->formatDBDate( date("Y-m-d"), true );
        $myDB = oxDb::getDB();
        $sSql = "INSERT INTO oxinvitations SET oxuserid = 'oxdefaultadmin', oxemail = 'oxemail',  oxdate='$sDate', oxpending = '1', oxaccepted = '0', oxtype = '1' ";
        $myDB->execute( $sSql );
        modConfig::getInstance()->setConfigParam( "dPointsForRegistration", 10 );
        modConfig::getInstance()->setConfigParam( "dPointsForInvitation", false );
        modSession::getInstance()->setVar( 'su', 'oxdefaultadmin' );
        modSession::getInstance()->setVar( 're', md5('oxemail') );

        $oUser = $this->getMock( "oxuser", array( "save" ) );
        $oUser->expects( $this->once() )->method( 'save' )->will($this->returnValue( true ) );
        $this->assertFalse( $oUser->setCreditPointsForRegistrant( "oxdefaultadmin", md5('oxemail') ));
        $this->assertNull( oxSession::getVar( 'su' ) );
        $this->assertNull( oxSession::getVar( 're' ) );
    }

    /**
     * oxUser::setCreditPointsForInviter() test case
     *
     * @return null
     */
    public function testSetCreditPointsForInviter()
    {
        modConfig::getInstance()->setConfigParam( "dPointsForInvitation", 10 );

        $oUser = $this->getMock( "oxuser", array( "save" ) );
        $oUser->expects( $this->once() )->method( 'save' )->will($this->returnValue( true ) );
        $this->assertTrue( $oUser->setCreditPointsForInviter() );
    }

    /**
     * oxUser::isTermsAccepted() test case
     *
     * @return null
     */
    public function testIsTermsAccepted()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        oxDb::getDb()->execute( "insert into oxacceptedterms (`OXUSERID`, `OXSHOPID`, `OXTERMVERSION`) values ( 'testUserId', '{$sShopId}', '0' )" );

        $oUser = $this->getMock( "oxuser", array( "getId" ) );
        $oUser->expects( $this->once() )->method( 'getId' )->will($this->returnValue( 'testUserId' ));
        $this->assertTrue( $oUser->isTermsAccepted() );
    }

    /**
     * oxUser::acceptTerms() test case
     *
     * @return null
     */
    public function testAcceptTerms()
    {
        $oDb = oxDb::getDb();

        $this->assertFalse( (bool)$oDb->getOne( "select 1 from oxacceptedterms where oxuserid='oxdefaultadmin'" ) );

        $oUser = new oxUser();
        $oUser->load( "oxdefaultadmin" );
        $oUser->acceptTerms();

        $this->assertTrue( (bool)$oDb->getOne( "select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'" ) );

        $oDb->getOne( "update oxacceptedterms set oxtermversion='0'" );
        $this->assertTrue( (bool)$oDb->getOne( "select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='0'" ) );
        $this->assertFalse( (bool)$oDb->getOne( "select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'" ) );

        $oUser->acceptTerms();
        $this->assertTrue( (bool)$oDb->getOne( "select 1 from oxacceptedterms where oxuserid='oxdefaultadmin' and oxtermversion='1'" ) );
    }

    /**
     * Test case for bug entry #1714
     *
     * @return null
     */
    public function testCaseForBugEntry1714()
    {
        $iCustNr = oxDb::getDb()->getOne( "select max(oxcustnr) from oxuser" );

        $oUser = new oxUser();
        $oUser->setId( "testID" );
        $oUser->oxuser__oxusername = new oxField( "aaa@bbb.lt", oxField::T_RAW );
        $oUser->oxuser__oxshopid   = new oxField( oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW );
        $oUser->save();

        $oUser = new oxUser();
        $oUser->load( "testID" );
        $this->assertEquals( $iCustNr + 1, $oUser->oxuser__oxcustnr->value );

        $oUser->delete();

        $oUser = new oxUser();
        $oUser->setId( "testID" );
        $oUser->oxuser__oxusername = new oxField( "aaa@bbb.lt", oxField::T_RAW );
        $oUser->oxuser__oxshopid   = new oxField( oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW );
        $oUser->save();

        $oUser = new oxUser();
        $oUser->load( "testID" );
        $this->assertEquals( $iCustNr + 2, $oUser->oxuser__oxcustnr->value );

        $oUser->delete();
    }

    public function testSetSelectedAddressId()
    {
        $sAddressId = 'xxx';
        $oUser = new oxUser();
        $oUser->setSelectedAddressId( $sAddressId );
        $this->assertEquals( $sAddressId, $oUser->getSelectedAddressId() );
    }

    public function testCheckCountriesWrongCountries()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkCountries", "{ throw new oxUserException; }");

        try {
            $oUser = new oxuser();
            $oUser->UNITcheckCountries( array( "oxuser__oxcountryid" => "xxx" ), array( "oxaddress__oxcountryid" => "yyy" ) );
        } catch ( oxUserException $oExcp ) {
            return;
        }
        $this->fail( "error in oxUser::_checkCountries()" );
    }

    public function testCheckCountriesGoodCountries()
    {
        try {
            $oUser = new oxuser();
            $oUser->UNITcheckCountries( array( "oxuser__oxcountryid" => "a7c40f631fc920687.20179984" ), array( "oxaddress__oxcountryid" => "a7c40f6320aeb2ec2.72885259" ) );
        } catch ( oxUserException $oExcp ) {
            $this->fail( "error in oxUser::_checkCountries()" );
        }
    }


    public function testAllowDerivedUpdate()
    {
        $oUser = new oxuser();
        $this->assertTrue( $oUser->allowDerivedUpdate() );
    }


    public function testCheckRequiredArrayFieldsEmptyField()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkRequiredArrayFields", "{ throw new oxInputException; }");

        try {
            $oUser = new oxuser();
            $oUser->UNITcheckRequiredArrayFields( 'xxx', array( 'aaa' => ' ' ) );
        } catch ( oxInputException $oEx ) {
            return;
        } catch ( Exception $oEx ) {
        }
        $this->fail('failed while runing testCheckRequiredArrayFields');
    }
    public function testCheckRequiredArrayFieldsFilledField()
    {
        try {
            $oUser = new oxuser();
            $oUser->UNITcheckRequiredArrayFields( 'xxx', array( 'aaa' => 'xxx' ) );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckRequiredArrayFields');
        }
    }

    public function testGetPasswordHash()
    {
        $oUser1 = new oxUser();
        $oUser1->oxuser__oxpassword = new oxField( 'passwordHash' );

        $oUser2 = new oxuser();
        $oUser2->oxuser__oxpassword = new oxField( str_repeat( "*", 32 ) );

        $oUser3 = new oxuser();

        $this->assertEquals( 'passwordHash', $oUser1->getPasswordHash() );

        $sHash = $oUser2->getPasswordHash();
        $this->assertEquals( str_repeat( "*", 32 ), $sHash );

        $this->assertNull( $oUser3->getPasswordHash() );
    }


    public function testisExpiredUpdateId()
    {
        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        $oUser = new oxuser();
        $oUser->load( $sUserId );
        $oUser->setUpdateKey();

        $this->assertFalse( $oUser->isExpiredUpdateId( $oUser->getUpdateId() ) );
        $this->assertTrue( $oUser->isExpiredUpdateId( 'xxx' ) );

    }

    public function testMagicGetter()
    {
        $oNewsSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'getOptInEmailStatus' ) );
        $oNewsSubscription->expects( $this->once() )->method( 'getOptInStatus')->will( $this->returnValue( 'getOptInStatus' ) );
        $oNewsSubscription->expects( $this->once() )->method( 'getOptInEmailStatus')->will( $this->returnValue( 'getOptInEmailStatus' ) );

        $oUser = $this->getMock( 'oxuser', array( 'getUserGroups',
                                                  'getNoticeListArtCnt',
                                                  'getWishListArtCnt',
                                                  'getRecommListsCount',
                                                  'getUserAddresses',
                                                  'getUserPayments',
                                                  'getUserCountry',
                                                  'getNewsSubscription' ) );

        $oUser->expects( $this->once() )->method( 'getUserGroups')->will( $this->returnValue( 'getUserGroups' ) );
        $oUser->expects( $this->once() )->method( 'getNoticeListArtCnt')->will( $this->returnValue( 'getNoticeListArtCnt' ) );
        $oUser->expects( $this->once() )->method( 'getWishListArtCnt')->will( $this->returnValue( 'getWishListArtCnt' ) );
        $oUser->expects( $this->once() )->method( 'getRecommListsCount')->will( $this->returnValue( 'getRecommListsCount' ) );
        $oUser->expects( $this->once() )->method( 'getUserAddresses')->will( $this->returnValue( 'getUserAddresses' ) );
        $oUser->expects( $this->once() )->method( 'getUserPayments')->will( $this->returnValue( 'getUserPayments' ) );
        $oUser->expects( $this->once() )->method( 'getUserCountry')->will( $this->returnValue( 'getUserCountry' ) );
        $oUser->expects( $this->exactly( 2 ) )->method( 'getNewsSubscription')->will( $this->returnValue( $oNewsSubscription ) );

        $this->assertEquals( 'getUserGroups', $oUser->oGroups );
        $this->assertEquals( 'getNoticeListArtCnt', $oUser->iCntNoticeListArticles );
        $this->assertEquals( 'getWishListArtCnt', $oUser->iCntWishListArticles );
        $this->assertEquals( 'getRecommListsCount', $oUser->iCntRecommLists );
        $this->assertEquals( 'getUserAddresses', $oUser->oAddresses );
        $this->assertEquals( 'getUserPayments', $oUser->oPayments );
        $this->assertEquals( 'getUserCountry', $oUser->oxuser__oxcountry );

        $this->assertEquals( 'getOptInStatus', $oUser->sDBOptin );
        $this->assertEquals( 'getOptInEmailStatus', $oUser->sEmailFailed );
    }

    public function testIsSamePassword()
    {
        $oUser = new oxuser();

        // plain password in db
        $oUser->oxuser__oxpassword = new oxfield( 'aaa' );
        $this->assertFalse( $oUser->isSamePassword( 'aaa' ) );
        $this->assertFalse( $oUser->isSamePassword( 'bbb' ) );

        // hashed
        $oUser->setPassword( 'xxx' );
        $this->assertTrue( $oUser->isSamePassword( 'xxx' ) );
        $this->assertFalse( $oUser->isSamePassword( 'yyy' ) );
    }

    public function testSetPassword()
    {
        $oUser = new oxuser();
        $oUser->setPassword( 'xxx' );
        $this->assertFalse( '' == $oUser->oxuser__oxpassword->value );
        $this->assertTrue( $oUser->isSamePassword( 'xxx' ) );

        $oUser->setPassword();
        $this->assertTrue( '' == $oUser->oxuser__oxpassword->value );
    }

    public function testEncodePassword()
    {
        $sPassword = 'xxx';
        $sSalt = 'yyy';
        $sEncPass = hash('sha512', $sPassword . $sSalt);

        $oUser = new oxUser();
        $this->assertEquals( $sEncPass, $oUser->encodePassword( $sPassword, $sSalt ) );
    }

    public function testGetUpdateId()
    {
        $oUser = $this->getMock( 'oxuser', array( 'setUpdateKey' ) );
        $oUser->expects( $this->once() )->method( 'setUpdateKey');

        $oUser->setId( 'xxx' );
        $oUser->oxuser__oxshopid = new oxfield( 'yyy' );
        $oUser->oxuser__oxupdatekey = new oxfield( 'zzz' );

        $this->assertEquals( md5( 'xxx' . 'yyy' . 'zzz' ), $oUser->getUpdateId() );
    }

    public function testSetUpdateKey()
    {
        $iCurrTime = time();

        // overriding utility functions
        oxTestModules::addFunction( "oxUtilsObject", "generateUId", "{ return 'xxx'; }");
        oxTestModules::addFunction( "oxUtilsDate", "getTime", "{ return $iCurrTime; }");

        $oUser = $this->getMock( 'oxuser', array( 'save' ) );
        $oUser->expects( $this->once() )->method( 'save');
        $oUser->setUpdateKey();

        $this->assertEquals( 'xxx', $oUser->oxuser__oxupdatekey->value );
        $this->assertEquals( ( $iCurrTime + 3600 * 6 ), $oUser->oxuser__oxupdateexp->value );
    }

    public function testReSetUpdateKey()
    {
        $oUser = $this->getMock( 'oxuser', array( 'save' ) );
        $oUser->expects( $this->once() )->method( 'save');
        $oUser->setUpdateKey( true );

        $this->assertEquals( '', $oUser->oxuser__oxupdatekey->value );
        $this->assertEquals( 0, $oUser->oxuser__oxupdateexp->value );
    }

    public function testLoadUserByUpdateId()
    {
        // loadign and saving test user
        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        $oUser = new oxuser();
        $oUser->load( $sUserId );
        $oUser->oxuser__oxupdatekey = new oxfield( 'xxx' );
        $oUser->oxuser__oxupdateexp = new oxfield( time() + 3600 );
        $oUser->oxuser__oxshopid    = new oxfield( oxConfig::getInstance()->getShopId() );
        $oUser->save();

        $sUid = md5( $oUser->getId() . $oUser->oxuser__oxshopid->value . $oUser->oxuser__oxupdatekey->value );

        $oUser = new oxuser();
        $this->assertTrue( $oUser->loadUserByUpdateId( $sUid ) );
        $this->assertEquals( $sUserId, $oUser->getId() );

        $this->assertNull( $oUser->loadUserByUpdateId( 'xxx' ) );
    }


    /**
     * Boni index for newly created users must be 1000 instead of 1000
     */
    public function testBoniAfterUserInsert()
    {
        $sId = 'testuserx';
        $oUser = new oxUser();
        $oUser->setId( $sId );
        $oUser->oxuser__oxusername = new oxField( "aaa@bbb.lt", oxField::T_RAW );
        $oUser->oxuser__oxshopid   = new oxField( oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW );
        $oUser->UNITinsert();

        $this->assertEquals( '1000', oxDB::getDb()->getOne( "select oxboni from oxuser where oxid = '$sId' " ) );
    }


    public function testCheckIfEmailExistsMallUsersNonAdminNoPass()
    {
        $oUser = new oxuser();
        $oUser->load( $this->_aUsers[0][0] );
        $oUser->oxuser__oxusername = new oxField( 'admin@oxid.lt', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField( 'user' );
        $oUser->save();

        $oUser = new oxUser();
        $this->assertFalse( $oUser->checkIfEmailExists( 'admin@oxid.lt' ) );
    }

    public function testCheckIfEmailExistsMallUsersTryingToCreateUserWithNameAdmin()
    {
        $oUser = new oxUser();

        $this->assertTrue( $oUser->checkIfEmailExists( oxADMIN_LOGIN ) );
    }

    public function testCheckIfEmailExistsMallUsersOldEntryWithoutPass()
    {
        $oUser = new oxUser();

        $this->assertFalse( $oUser->checkIfEmailExists( 'aaa@bbb.lt' ) );
    }

    public function testCheckIfEmailExistsMallUsersOldEntryWithPass()
    {
        $oUser = new oxUser();

        $this->assertTrue( $oUser->checkIfEmailExists( oxADMIN_LOGIN ) );
    }

    /**
     * Test case:
     * creating new user in subshop which data is:
     * name: admin, pass: adminas, rights: user, shop id: 2
     */
    public function testNewUserInSubShop()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'getShopId' ) );
        $oConfig->expects( $this->any() )->method( 'getShopId')->will( $this->returnValue( 2 ) );

        $oUser = $this->getMock( 'oxuser', array( 'isAdmin', 'getConfig', 'getViewName' ), array(), '', false );
        $oUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( true ) );
        $oUser->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );
        $oUser->expects( $this->any() )->method( 'getViewName')->will( $this->returnValue( 'oxuser' ) );

        $oUser->init( 'oxuser' );
        $this->assertTrue( $oUser->checkIfEmailExists( oxADMIN_LOGIN ) );
    }

    // QA reported that newly created user has not rights set in db
    public function testCreatingUserRightsMustBeSet()
    {
        $myDB = oxDb::getDB();
        $myDB->execute( 'delete from oxuser where oxusername="aaa@bbb.lt" ' );

        $oUser = new oxuser();
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->createUser();

        $oNewUser = new oxuser();
        $oNewUser->load( $oUser->getId() );
        $this->assertEquals( 'user', $oNewUser->oxuser__oxrights->value );
    }

    /**
     * Test case:
     * newsletter registration is made using email, trying to register user using same email and without password
     */
    public function testCreateUserWhileRegistrationNoPass()
    {
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->delete( $sUserId );

        // simulating newsletter subscription
        $oUser = new oxuser();
        $oUser->setId( $sUserId );
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->save();
        $this->assertEquals( '1000', $oUser->oxuser__oxboni->value );

        $aInvAdress = array( 'oxuser__oxfname'        => 'xxx',
                             'oxuser__oxlname'        => 'yyy',
                             'oxuser__oxstreetnr'     => '11',
                             'oxuser__oxstreet'       => 'zzz',
                             'oxuser__oxzip'          => '22',
                             'oxuser__oxcity'         => 'ooo',
                             'oxuser__oxcountryid'    => 'a7c40f631fc920687.20179984' );
    $this->_aUsers[$sShopId][] = '_testUser';
        try {
            $oUser = new oxuser();
            $oUser->setId('_testUser');
            $oUser->checkValues( 'aaa@bbb.lt', '', '', $aInvAdress, array() );
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load( $oUser->oxuser__oxid->value );
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array() );
            $this->assertEquals( '1000', $oUser->oxuser__oxboni->value );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckValues test');
        }
    }

    /**
     * Test case:
     * newsletter registration is made using email, trying to register user using same email and with password
     */
    public function testCreateUserWhileRegistrationWithPass()
    {
        $sUserId = $this->_aUsers[0][0];
        $oUser = new oxuser();
        $oUser->delete( $sUserId );

        // simulating newsletter subscription
        $oUser = new oxuser();
        $oUser->setId( $sUserId );
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->save();

        $aInvAdress = array( 'oxuser__oxfname'        => 'xxx',
                             'oxuser__oxlname'        => 'yyy',
                             'oxuser__oxstreetnr'     => '11',
                             'oxuser__oxstreet'       => 'zzz',
                             'oxuser__oxzip'          => '22',
                             'oxuser__oxcity'         => 'ooo',
                             'oxuser__oxcountryid'    => 'a7c40f631fc920687.20179984' );

    $this->_aUsers[$sShopId][] = '_testUser';
        try {
            $oUser = new oxuser();
        $oUser->setId('_testUser');
            $oUser->checkValues( 'aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array() );
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load( $oUser->oxuser__oxid->value );
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array() );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckValues test: '."\n msg:\n".$oEx->getMessage()."\n trace:\n".$oEx->getTraceAsString() );
        }
    }

    /**
     * Test case:
     * user is registered (without pass), updating its data
     */
    public function testUpdateUserCheckValuesWithPass()
    {
        $sUserId = $this->_aUsers[0][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->save();

        $aInvAdress = array( 'oxuser__oxfname'        => 'xxx',
                             'oxuser__oxlname'        => 'yyy',
                             'oxuser__oxstreetnr'     => '11',
                             'oxuser__oxstreet'       => 'zzz',
                             'oxuser__oxzip'          => '22',
                             'oxuser__oxcity'         => 'ooo',
                             'oxuser__oxcountryid'    => 'a7c40f631fc920687.20179984' );

    $this->_aUsers[$sShopId][] = '_testUser';
        try {
            $oUser = new oxuser();
        $oUser->setId('_testUser');
            $oUser->checkValues( 'aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array() );
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load( $oUser->oxuser__oxid->value );
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array() );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckValues test: '."\n msg:\n".$oEx->getMessage()."\n trace:\n".$oEx->getTraceAsString() );
        }
    }

    /**
     * Test case:
     * user is registered (with pass), updating its data
     */
    public function testUpdateUserCheckValuesOldWithNewWithPass()
    {
        $sUserId = $this->_aUsers[oxConfig::getInstance()->getBaseShopId()][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('a', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('b', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('xxx', oxField::T_RAW);
        $oUser->save();
        $aInvAdress = array( 'oxuser__oxusername'  => 'aaa@bbb.lt',
                             'oxuser__oxfname'     => 'xxx',
                             'oxuser__oxlname'     => 'yyy',
                             'oxuser__oxstreetnr'  => '11',
                             'oxuser__oxstreet'    => 'zzz',
                             'oxuser__oxzip'       => '22',
                             'oxuser__oxcity'      => 'ooo',
                             'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
                             'oxuser__oxpassword'  => 'xxx' );

        try {
            $oUser = new oxuser();
            $oUser->load( $sUserId );
            $oUser->changeUserData( 'xxx@yyy.zzz', 'xxx', 'xxx', $aInvAdress, array() );
            $this->assertEquals( $sUserId, $oUser->getId() );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckValues test');
        }
    }

    /**
     * Test case:
     * creating user with pass
     */
    public function testCreateUserWithPass()
    {
        $aInvAdress = array( 'oxuser__oxfname'        => 'xxx',
                             'oxuser__oxlname'        => 'yyy',
                             'oxuser__oxstreetnr'     => '11',
                             'oxuser__oxstreet'       => 'zzz',
                             'oxuser__oxzip'          => '22',
                             'oxuser__oxcity'         => 'ooo',
                             'oxuser__oxcountryid'    => 'a7c40f631fc920687.20179984' );

    $this->_aUsers[$sShopId][] = '_testUser';
        try {
            $oUser = new oxuser();
        $oUser->setId('_testUser');
            $oUser->checkValues( 'aaa@bbb.lt', 'xxx', 'xxx', $aInvAdress, array() );
            $oUser->oxuser__oxusername = new oxField('aaa@bbb.lt', oxField::T_RAW);
            $oUser->oxuser__oxpassword = new oxField($sPassword, oxField::T_RAW);
            $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
            $oUser->createUser();

            $oUser->load( $oUser->oxuser__oxid->value );
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $oUser->oxuser__oxpassword->value, $oUser->oxuser__oxpassword->value, $aInvAdress, array() );
        } catch ( Exception $oEx ) {
            $this->fail('failed while runing testCheckValues test');
        }
    }

    /**
     * Testing getter which is used for backwards compatability
     */
    public function testGet()
    {
        $myConfig = oxConfig::getInstance();
        $myUtils  = oxUtils::getInstance();
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $oUser1 = oxNew( 'oxuser' );
        $oUser1->load( $sUserID );
        $oUser2 = $this->getProxyClass("oxUser");
        $oUser2->load( $sUserID );

        $this->assertEquals( $oUser1->oGroups->arrayKeys(), $oUser2->getUserGroups()->arrayKeys() );
        $this->assertEquals( $oUser1->oAddresses->arrayKeys(), $oUser2->getUserAddresses()->arrayKeys() );
        $this->assertEquals( $oUser1->oPayments, $oUser2->getUserPayments() );
        $this->assertEquals( $oUser1->oxuser__oxcountry->value, $oUser2->getUserCountry()->value );
        $this->assertEquals( $oUser1->sDBOptin, $oUser2->getNewsSubscription()->getOptInStatus() );
        $this->assertEquals( $oUser1->sEmailFailed, $oUser2->getNewsSubscription()->getOptInEmailStatus() );
    }

    /**
     * Testing user's news subscribtion object getter
     */
    // 1. for empty user or user which does not have any subscription info oxuser::getNewsSubscription
    //    must return empty oxnewssubscribed object
    public function testGetNewsSubscriptionNoUserEmptySubscription()
    {
        $oUser = new oxuser();
        $this->assertNull( $oUser->getNewsSubscription()->oxnewssubscribed__oxid->value );
    }
    // 2. loading subscription by user id
    public function testGetNewsSubscriptionNoUserReturnsByOxid()
    {
        oxAddClassModule( 'oxuserTest_oxnewssubscribed', 'oxnewssubscribed');
        $oUser = oxNew( 'oxuser' );
        $oUser->setId( 'oxid' );
        $this->assertTrue( $oUser->getNewsSubscription()->loadFromUserID );
    }
    // 3. loading subscription by user email
    public function testGetNewsSubscriptionNoUserReturnsByEmail()
    {
        oxAddClassModule( 'oxuserTest_oxnewssubscribed', 'oxnewssubscribed');
        $oUser = oxNew( 'oxuser' );
        $oUser->oxuser__oxusername = new oxField('email', oxField::T_RAW);
        $this->assertTrue( $oUser->getNewsSubscription()->loadFromEMail );
    }

    /**
     * Testing how group/address/exec. payments list loading works
     */
    // 1. fetching group info for existing user - must return 1 group
    public function testGetUserGroups_correctInput()
    { // tests with correct data
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->setId($sUserID);
        $oGroups = $oUser->getUserGroups();

        // each new created user is assigned to 1 new created user group
        $sGroupId = $myDB->getOne('select oxgroupsid from oxobject2group where oxobjectid="'.$sUserID.'"');
        $this->assertTrue( isset( $oGroups[$sGroupId] ) );
    }
    // 2. fetching group info for not existing user - must return 0 group
    public function testGetUserGroupsWrongInput()
    { // tests with not existing user id
        $myUtils  = oxUtils::getInstance();
        $myConfig = oxConfig::getInstance();

        $oUser   = oxNew( 'oxuser' );
        $oUser->setId('xxx');
        $oGroups = $oUser->getUserGroups();

        // no user for not existing user
        $this->assertEquals( 0, count( $oGroups ) );
    }


    /**
     * Testing user address getter
     */
    // 1. fetching address info for existing user - must return 1 address
    public function testGetUserAddressesCorrenctInput()
    { // testing with existing data
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID  = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser    = new oxuser();
        $oAddress = $oUser->getUserAddresses( $sUserID );

        // each new created user is assigned to 1 new created address
        $sAddressId = $myDB->getOne('select oxid from oxaddress where oxuserid="'.$sUserID.'"');
        $this->assertEquals( true, isset( $oAddress[$sAddressId] ) );

        $oAddress = $oUser->getUserAddresses( 'xxx' );
        $this->assertEquals( 0, count( $oAddress ) );
    }
    // 2. fetching address info for not existing user - must return 0 address
    public function testGetUserAddressesWrongInput()
    { // testing with not existing data
        $myUtils  = oxUtils::getInstance();
        $myConfig = oxConfig::getInstance();

        $oUser    = oxNew( 'oxuser' );
        $oAddress = $oUser->getUserAddresses( 'xxx' );

        // each new created user is assigned to 1 new created address
        $this->assertEquals( 0, count( $oAddress ) );
    }

    /**
     * Testing user payments getter
     */
    // 1. fetching payment info for existing user - must return 1 payment
    public function testGetUserPaymentsCorrectInput()
    { // testing with existing data
        $myUtils  = oxUtils::getInstance();
        $myConfig = oxConfig::getInstance();

        $sUserID   = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser     = $this->getProxyClass("oxuser");//oxNew( 'oxuser', 'core' );
        $oUserPayments = $oUser->getUserPayments( $sUserID );

        // each new created user is assigned to 1 new created exec. payment
        $this->assertEquals( 1, count( $oUserPayments ) );

        $oUserPayments->rewind();
       $oUserPayment = $oUserPayments->current();

       $this->assertEquals($oUserPayment->oxuserpayments__oxuserid->value, $sUserID);
       $this->assertEquals($oUserPayment->oxpayments__oxdesc->value, 'Kreditkarte');  //important for compatibility to templates
       $this->assertEquals($oUserPayment->oxuserpayments__oxpaymentsid->value, 'oxidcreditcard');  //important for compatibility to templates

    }
    // 2. fetching payment info for not existing user - must return 0 payment
    public function testGetUserPaymentsWrongInput()
    { // testing with not existing data
        $myUtils  = oxUtils::getInstance();
        $myConfig = oxConfig::getInstance();

        $oUser     = $this->getProxyClass("oxuser"); //oxNew( 'oxuser', 'core' );
        $oPayments = $oUser->getUserPayments( 'xxx' );

        // each new created user is assigned to 1 new created exec. payment
        $this->assertEquals( 0, count( $oPayments ) );
    }

    /**
     * Testing user recommendation lists getter
     */
    public function testGetUserRecommLists()
    {
        $myDB = oxDb::getDB();
        $sShopId = $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ;
        $sUserID   = $this->_aUsers[ $sShopId ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "test", "'.$sUserID.'", "oxtest", "oxtest", "'.$sShopId.'" ) ';
        $myDB->Execute( $sQ );

        $sArticleID = $myDB->getOne( 'select oxid from oxarticles order by rand() ' );
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid ) values ( "test", "'.$sArticleID.'", "test" ) ';
        $myDB->Execute( $sQ );

        $oUser = $this->getProxyClass("oxuser");
        $oRecommlists = $oUser->getUserRecommLists( $sUserID );

        $this->assertEquals( 1, count( $oRecommlists ) );
        $oRecommlist = $oRecommlists->current();
        $this->assertEquals($oRecommlist->oxrecommlists__oxuserid->value, $sUserID);
        $this->assertEquals($oRecommlist->oxrecommlists__oxtitle->value, "oxtest");
    }

    /**
     * Testing user recommendation lists getter
     */
    public function testRecommListsCount()
    {
        $myDB = oxDb::getDB();
        $sShopId = $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ;
        $sUserID   = $this->_aUsers[ $sShopId ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "test", "'.$sUserID.'", "oxtest", "oxtest", "'.$sShopId.'" ) ';
        $myDB->Execute( $sQ );

        $sArticleID = $myDB->getOne( 'select oxid from oxarticles order by rand() ' );
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid ) values ( "test", "'.$sArticleID.'", "test" ) ';
        $myDB->Execute( $sQ );

        $oUser = $this->getProxyClass("oxuser");
        $oUser->load($sUserID);
        $iRecommlists = $oUser->getRecommListsCount();

        $this->assertEquals( 1, $iRecommlists );
    }

    /**
     * Testing user object saving
     */
    public function testSave()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->Load( $sUserID );
        $oUser->delete();

        $oUser->oxuser__oxrights = new oxField(null, oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);
        $oUser->save();

        // first looking for DB record
        $sQ = 'select count(oxid) from oxuser where oxid = "'.$oUser->oxuser__oxid->value.'" ';

        // looking for other info
        $this->assertEquals( 'user', $oUser->oxuser__oxrights->value );
        $this->assertEquals( false, empty( $oUser->oxuser__oxregister->value ) );

        // looking for record in oxremark table
        $sQ = 'select count(oxid) from oxremark where oxparentid = "'.$oUser->getId().'" and oxtype !="o"';
        $this->assertEquals( 1, (int) $myDB->getOne( $sQ ) );

        $oUser = oxNew( 'oxuser' );
        $oUser->setId($sUserID);
        $oUser->save();
    }

    /**
     * Testing user object saving
     */
    public function testSaveWithSpecChar()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->Load( $sUserID );
        $aInvAddress ['oxuser__oxcompany'] ='test&';
        $aInvAddress ['oxuser__oxaddinfo'] ='test&';
        $oUser->assign( $aInvAddress );
        $oUser->save();
        $this->assertEquals( 'test&amp;', $oUser->oxuser__oxcompany->value );
        $this->assertEquals( 'test&amp;', $oUser->oxuser__oxaddinfo->value );
        $sQ = 'select oxcompany from oxuser where oxid = "'.$oUser->oxuser__oxid->value.'" ';
        $this->assertEquals( 'test&', $myDB->getOne( $sQ ) );
    }

    /**
     * Testing user object saving if birthday is added
     */
    public function testSaveWithBirthDay()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDb();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->Load( $sUserID );
        $oUser->delete();
        $oUser->oxuser__oxbirthdate = new oxField(array ('day' => '12', 'month' => '12', 'year' => '1212'), oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField(null, oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);
        $oUser->save();
        // first looking for DB record
        $sQ = 'select count(oxid) from oxuser where oxid = "'.$oUser->oxuser__oxid->value.'" ';

        // looking for other info
        $this->assertEquals( 'user', $oUser->oxuser__oxrights->value );
        $this->assertEquals( false, empty( $oUser->oxuser__oxregister->value ) );
        $this->assertEquals( '1212-12-12', $oUser->oxuser__oxbirthdate->value );

        // looking for record in oxremark tabl
        $sQ = 'select count(oxid) from oxremark where oxparentid = "'.$oUser->getId().'"  and oxtype !="o"';
        $this->assertEquals( 1, (int) $myDB->getOne( $sQ ) );

    }

    /**
     * Testing user rights getter
     */
    // 1. for user with no initial rights
    public function testGetUserRightsNoInitialRights()
    {
        $oUser = $this->getProxyClass("oxUser");
        $this->assertEquals( 'user', $oUser->UNITgetUserRights() );
    }
    // 2. user initial rights are malladmin
    public function testGetUserRightsInitialAdminRightsSessionUserIsAdmin()
    {
        modSession::getInstance()->setVar("usr", "oxdefaultadmin");

        $oUser = $this->getProxyClass("oxUser");
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals( 'malladmin', $oUser->UNITgetUserRights() );
    }
    // 3. user initial rights are "user"
    public function testGetUserRightsInitialAdminRightsSessionUserIsSimpleUser()
    {
        modSession::getInstance()->setVar("usr", null);
        $oUser = $this->getProxyClass("oxUser");
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals( 'user', $oUser->UNITgetUserRights() );
    }
    // 4. user initial rights are sub shop admin
    public function testGetUserRightsInitialAdminRightsSessionUserIsSubShopUser()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $oUser = oxNew( 'oxbase' );
        $oUser->init( 'oxuser' );
        $oUser->Load( $sUserID );
        $oUser->oxuser__oxrights = new oxField(oxConfig::getInstance()->GetShopId(), oxField::T_RAW);
        $oUser->save();

        modSession::getInstance()->setVar("usr", $oUser->oxuser__oxid->value);

        $oUser = $this->getProxyClass("oxUser");
        $oUser->oxuser__oxrights = new oxField(oxConfig::getInstance()->GetShopId(), oxField::T_RAW);
        $this->assertEquals( oxConfig::getInstance()->GetShopId(), $oUser->UNITgetUserRights() );

        // check for denial
        $oUser = $this->getProxyClass("oxUser");
        $oUser->oxuser__oxrights = new oxField(2, oxField::T_RAW);
        $this->assertEquals( "user", $oUser->UNITgetUserRights() );

        // check for denial
        $oUser = $this->getProxyClass("oxUser");
        $oUser->oxuser__oxrights = new oxField('malladmin', oxField::T_RAW);
        $this->assertEquals( "user", $oUser->UNITgetUserRights() );
    }

    /**
     * Testing if inGroup method works OK
     */
    public function testInGroupWrongGroup()
    { // non existing group
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // assigned to some group ?
        $this->assertEquals( false, $oUser->inGroup( 'oxtestgroup' ) );
    }
    public function testInGroupCorrectGroup()
    { // existing group
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // assigned to some group ?
        $sGroupId = $myDB->getOne('select oxgroupsid from oxobject2group where oxobjectid="'.$oUser->getId().'"');
        $this->assertEquals( true, $oUser->inGroup( $sGroupId ) );
    }

    /**
     * Testing if deletion doesnt leave any related records
     */
    public function testDeleteEmptyUser()
    { // trying to delete "something"
        $myUtils  = oxUtils::getInstance();

        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( false, $oUser->delete() );
    }
    public function testDelete()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB = $oDB = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        // user address
        $oAddress = new oxAddress();
        $oAddress->setId( "_testAddress" );
        $oAddress->oxaddress__oxuserid = new oxField( $sUserID );
        $oAddress->save();

        // user groups
        $o2g = new oxBase();
        $o2g->init( "oxobject2group" );
        $o2g->setId( "_testO2G" );
        $o2g->oxobject2group__oxobjectid = new oxField( $sUserID );
        $o2g->oxobject2group__oxgroupsid = new oxField( $sUserID );
        $o2g->save();

        // notice/wish lists
        $oU2B = new oxBase();
        $oU2B->init( "oxuserbaskets" );
        $oU2B->setId( "_testU2B" );
        $oU2B->oxuserbaskets__oxuserid = new oxField( $sUserID );
        $oU2B->save();

        // newsletter subscription
        $oNewsSubs = new oxBase();
        $oNewsSubs->init( "oxnewssubscribed" );
        $oNewsSubs->setId( "_testNewsSubs" );
        $oNewsSubs->oxnewssubscribed__oxemail = new oxField( $sUserID );
        $oNewsSubs->oxnewssubscribed__oxuserid = new oxField( $sUserID );
        $oNewsSubs->save();

        // delivery and delivery sets
        $o2d = new oxBase();
        $o2d->init( "oxobject2delivery" );
        $o2d->setId( "_testo2d" );
        $o2d->oxnewssubscribed__oxobjectid   = new oxField( $sUserID );
        $o2d->oxnewssubscribed__oxdeliveryid = new oxField( $sUserID );
        $o2d->save();

        // discounts
        $o2d = new oxBase();
        $o2d->init( "oxobject2discount" );
        $o2d->setId( "_testo2d" );
        $o2d->oxnewssubscribed__oxobjectid   = new oxField( $sUserID );
        $o2d->oxnewssubscribed__oxdiscountid = new oxField( $sUserID );
        $o2d->save();


        // order information
        $oRemark = new oxBase();
        $oRemark->init( "oxremark" );
        $oRemark->setId( "_testRemark" );
        $oRemark->oxremark__oxparentid = new oxField( $sUserID );
        $oRemark->oxremark__oxtype   = new oxField( 'r' );
        $oRemark->save();

        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $oUser->delete();

        $aWhat = array( 'oxuser'    => 'oxid',
                        'oxaddress' => 'oxuserid',
                        'oxuserbaskets'  => 'oxuserid',
                        //'oxuserbasketitems'  => 'oxuserid',
                        //'oxvouchers' => 'oxuserid', this could be usefull for statistics or so
                        'oxnewssubscribed'  => 'oxuserid',
                        'oxobject2delivery' => 'oxobjectid',
                        'oxobject2discount' => 'oxobjectid',
                        'oxobject2group'    => 'oxobjectid',
                        'oxobject2payment'  => 'oxobjectid',
                        // all order information must be preserved
                        'oxremark'          => 'oxparentid',
                        //'oxuserpayments'    => 'oxuserid'
                      );


        // now checking if all related records were deleted
        foreach ( $aWhat as $sTable => $sField ) {
            $sQ = 'select count(*) from '.$sTable.' where '.$sField.' = "'.$sUserID.'" ';

            if ($sTable == 'oxremark') {
                $sQ .= " AND oxtype ='o'";
            }

            $iCnt = $myDB->getOne( $sQ );
            if ( $iCnt > 0 ) {
                $this->fail( $iCnt.' records were not deleted from "'.$sTable.'" table');
            }
        }
    }
    //FS#2578
    public function testDeleteSpecialUser()
    {
        $myDB = oxDb::getDB();
        $iLastCustNr = ( int ) $myDB->getOne( 'select max( oxcustnr ) from oxuser' ) + 1;
        $sShopId = modConfig::getInstance()->getShopId();
        $sQ  = 'insert into oxuser (oxid, oxshopid, oxactive, oxrights, oxusername, oxpassword, oxcustnr, oxcountryid) ';
        $sQ .= 'values ( "oxtestuser", "'.$sShopId.'", "1", "user", "testuser", "", "'.$iLastCustNr.'", "testCountry" )';
        $myDB->Execute( $sQ );

        $myUtils  = oxUtils::getInstance();

        $oUser = oxNew( 'oxuser' );
        $oUser->delete("oxtestuser");
        $this->assertEquals( false, $myDB->getOne( 'select oxid from oxuser where oxid = "oxtestuser"' ) );
    }

    /**
     * Testing object loading
     */
    public function testLoad()
    { // mostly to check if create date value is formatted
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $sCreate = $myDB->getOne('select oxcreate from oxuser where oxid="'.$oUser->getId().'" ' );
        $this->assertEquals( oxUtilsDate::getInstance()->formatDBDate( $sCreate ), $oUser->oxuser__oxcreate->value );
    }


    /**
     * Testing how update/insert works
     */
    public function testInsert()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = $this->getProxyClass("oxUser");
        $oUser->load( $sUserID );

        // deleting before inserting
        $oUser->delete();

        // inserting
        $oUser->UNITinsert();

        // checking
        $sQ = 'select count(*) from oxuser where oxid = "'.$oUser->oxuser__oxid->value.'" ';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );

        // checking boni
        $sQ = 'select oxboni from oxuser where oxid = "'.$oUser->oxuser__oxid->value.'" ';
        $this->assertEquals( 1000, $myDB->getOne( $sQ ) );
    }


    /**
     * Testing update functionality
     */
    public function testUpdate()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = $this->getMock( "oxuser", array( 'isAdmin' ) );
        $oUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oUser->load( $sUserID );

        // copying to test
        $sOxCreate = $oUser->oxuser__oxcreate->value;
        $sOxCustNr = $oUser->oxuser__oxcustnr->value;

        // updating
        $oUser->UNITupdate();

        // reloading
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // checking
        $this->assertEquals( $sOxCreate, $oUser->oxuser__oxcreate->value );
        $this->assertEquals( $sOxCustNr, $oUser->oxuser__oxcustnr->value );
    }


    /**
     * Testing oxuser::exists method
     */
    public function testExistsNotExisting()
    {
        $oUser = new oxuser();
        //$oUser->exists( 'zzz' );
        //die();
        $this->assertFalse( $oUser->exists( 'zzz' ) );
    }
    public function testExistsMallUsers()
    {
        $myConfig = oxConfig::getInstance();

        // copying
        $blMall = $myConfig->blMallUsers;
        $myConfig->blMallUsers = true;

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $this->assertEquals( true, $oUser->exists( $sUserID ) );

        // restoring
        $myConfig->blMallUsers = $blMall;
    }
    public function testExistsIfMallAdmin()
    {
        $oUser   = oxNew( 'oxuser' );
        $oUser->load('oxdefaultadmin');
        $this->assertTrue( $oUser->exists() );
    }
    public function testExists()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $this->assertEquals( true, $oUser->exists() );
    }

    /**
     * Testing #5901 case
     */
    public function testExistsInOtherSubshops()
    {
        $oUser = new oxUser();
        $oUser->load('oxdefaultadmin');
        $oUser->oxuser__oxrights = new oxField("");
        $oUser->oxuser__oxshopid = new oxField("2");
        $oUser->oxuser__oxusername = new oxField("differentName");

        oxRegistry::getConfig()->setShopId(2);

        $this->assertTrue($oUser->exists());
    }

    /**
     * Testing existing username
     * (same as subscribing to newsletter logics)
     */
    public function testExistsUsername()
    {
        $oUser = new oxUser();
        $oUser->oxuser__oxusername = new oxField("admin", oxField::T_RAW);
        $this->assertTrue($oUser->exists());
    }

    /**
     * Testing existing username in different subshop
     * (same as subscribing to newsletter logics)
     */
    public function testExistsUsernameMultishop()
    {
        oxRegistry::getConfig()->setShopId(2);
        $oUser = new oxUser();
        $oUser->oxuser__oxusername = new oxField("admin", oxField::T_RAW);

        $this->assertFalse($oUser->exists());
    }



    /**
     * Checking amount of created orders
     */
    // 1. checking order count for random user. order count must be 1
    public function testGetOrdersForRandomUSer()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // checking order count
        $this->assertEquals( 1, count( $oUser->getOrders() ) );
    }


    // 3. checking order count for random user. order count must be 1
    public function testGetOrdersForNonRegUser()
    {
        $myUtils  = oxUtils::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $oUser->oxuser__oxregister = new oxField(0, oxField::T_RAW);

        // checking order count
        $this->assertEquals( 0, count( $oUser->getOrders() ) );
    }


    /**
     * Testing executed order count
     */
    // 1. empty user normally have no orders
    public function testGetOrderCountEmptyUser()
    {
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( 0, $oUser->getOrderCount() );
    }
    // 2. demo user has 1 demo order
    public function testGetOrderCountUserWithOrder()
    {
        $myDB = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $iOrderCnt = $myDB->getOne( 'select count(*) from oxorder where oxuserid = "'.$oUser->oxuser__oxid->value.'" and oxorderdate >= "'.$oUser->oxuser__oxregister->value.'" ' );
        $this->assertEquals( $iOrderCnt, $oUser->getOrderCount() );
    }


    /**
     * Testing active country
     */
    public function testGetActiveCountryEmptyUser()
    {
        $oUser = oxNew( 'oxuser' );
        //to make sure there is no user in the session
        $oUser->logout();
        $this->assertEquals( '', $oUser->getActiveCountry() );
    }

    public function testGetActiveCountryPassedAddress()
    {
        $myDB = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $sQ = 'select oxid from oxaddress where oxuserid = "'.$sUserID.'"';
        $sAddessId = $myDB->getOne( $sQ );
        oxSession::setVar('deladrid', $sAddessId );

        // loading user
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // checking country ID
        $sQ = 'select oxcountryid from oxaddress where oxuserid = "'.$sUserID.'" ';
        $this->assertEquals( $myDB->getOne( $sQ ), $oUser->getActiveCountry() );
    }

    public function testGetActiveCountryNoPassedAddressCountryIsTakenFromUser()
    {
        $myDB = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        modSession::getInstance()->addClassFunction( 'getUser', create_function( '', '$oUser = oxNew( "oxuser" ); $oUser->load( "'.$sUserID.'" ); return $oUser;' ) );

        // checking user country
        $sQ = 'select oxcountryid from oxuser where oxid = "'.$sUserID.'" ';
        $this->assertEquals($myDB->getOne( $sQ ), "testCountry");
        $this->assertEquals( $myDB->getOne( $sQ ), $oUser->getActiveCountry() );
    }

    public function testGetActiveCountryNoPassedAddressCountryIsTakenFromSessionUser()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $sUsrCountry = $oUser->oxuser__oxcountryid->value;
        modSession::getInstance()->setVar( 'usr', $sUserID );

        // checking user country
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( $sUsrCountry, $oUser->getActiveCountry() );
    }


    /**
     * Testing user creation
     */
    // 1. creating normalu user with password, after creation new DB record must appear
    public function testCreateUser()
    {
        $myDB    = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->Load( $sUserID );
        $oUser->delete();

        $oUser->createUser();

        // checking
        $sQ = 'select count(*) from oxuser where oxid = "'.$oUser->getId().'" ';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );

    }
    // 2. creating with additional dublicate entries check for mall users
    public function testCreateUserMallUsers()
    {
        $myDB     = oxDb::getDB();

        modConfig::getInstance()->addClassVar('blMallUsers', true );

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->Load( $sUserID );
        $oUser->delete();

        $oUser->createUser();

        // checking
        $sQ   = 'select count(*) from oxuser where oxid = "'.$oUser->getId().'" ';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );
    }

    //3. creating user which overrides some user without password. It should erase previously
    //user stored order info
    public function testCreateUserOverridingUserWithoutPassword()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ 0 ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $oUser->oxuser__oxpassword = new oxField('', oxField::T_RAW);
        $oUser->save();

        // recreating
        $oUser->createUser();

        // checking
        $sQ = 'select count(*) from oxuser where oxusername = "'.$oUser->oxuser__oxusername->value.'" ';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );
    }

    public function testCreateUserMallUsersTryingToCreateSameUserAgainShouldThrowAnExcp()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $oUser->oxuser__oxusername = new oxField( 'testuser'.time() );
        $oUser->setPassword( 'xxx' );
        $oUser->save();

        try {
            $oUser->setMallUsersStatus( true );
            $oUser->createUser();
        } catch ( Exception $oExcp ) {
            $oLang = oxLang::getInstance();
            $this->assertEquals( sprintf( $oLang->translateString( 'ERROR_MESSAGE_USER_USEREXISTS', $oLang->getTplLanguage() ), $oUser->oxuser__oxusername->value ), $oExcp->getMessage() );
            return;
        }

        $this->fail( 'user creation is not allowed' );
    }

    public function testCreateUserSavingFailsExcpThrown()
    {
        $sUserID = $this->_aUsers[ $this->_aShops[ 0 ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $oUser = $this->getMock( 'oxuser', array( 'save' ) );
        $oUser->expects($this->once())->method("save")->will( $this->returnValue( false ));
        $oUser->load( $sUserID );
        $oUser->delete();

        try {
        // recreating
        $oUser->createUser();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( 'EXCEPTION_USER_USERCREATIONFAILED', $oExcp->getMessage() );
            return;
        }
        $this->fail( 'user saving must fail' );
    }

    /**
     * Testing how oxid adds/removes user from group
     */
    // 1. trying to add to allready assigned group
    public function testAddToGroupToAssigned()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $sGroupId = $myDB->getOne( 'select oxgroupsid from oxobject2group where oxobjectid="'.$sUserID.'" ' );;

        // assigning to some group
        $this->assertEquals( false, $oUser->addToGroup( $sGroupId ) );
    }
    // 2. simply adding to
    public function testAddToGroupToNotAssigned()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        // looking for not assigned group
        $sNewGroup = $myDB->getOne( 'select oxid from oxgroups where oxid not in ( select oxgroupsid from oxobject2group where oxobjectid="'.$sUserID.'" ) ' );

        // checking before insert
        $this->assertEquals( 1, count( $oUser->getUserGroups() ) );

        // assigning to some group
        $this->assertTrue( $oUser->addToGroup( $sNewGroup ) );

        // checking DB
        $sCnt = $myDB->getOne( 'select count(*) from oxobject2group where oxobjectid="'.$sUserID.'" and oxgroupsid="'.$sNewGroup.'" ' );
        $this->assertEquals( 1, $sCnt );

        $oGroups = $oUser->getUserGroups();
        // checking group count after adding to new one
        $this->assertEquals( 2, count( $oGroups ) );

        // #0003218: validating loaded groups
        $this->assertEquals( true, isset($oGroups[$sNewGroup]) );
        $this->assertEquals( $sNewGroup, $oGroups[$sNewGroup]->getId() );
    }

    public function testRemoveFromGroup()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();
        $myConfig = oxConfig::getInstance();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $oUser = oxNew( 'oxuser' );

        $sQ  = 'select oxid from oxgroups where oxid <> (select oxgroupsid from oxobject2group where oxobjectid = "'.$sUserID.'") ';
        $sQ .= 'order by rand()';
        $sGroupId = $myDB->getOne( $sQ );

        // checking
        $sQ  = 'insert into oxobject2group ( oxid, oxshopid, oxobjectid, oxgroupsid ) ';
        $sQ .= 'values ( "_testO2G_id", "'.$myConfig->getShopId().'", "'.$sUserID.'", "'.$sGroupId.'" ) ';
        $sCnt = $myDB->Execute( $sQ );

        // loading to initialize group list
        $oUser->load( $sUserID );

        // checking before insert
        $this->assertEquals( 2, $oUser->getUserGroups()->count() );

        // assigning to some group
        $oUser->removeFromGroup( $sGroupId );

        // checking
        $sQ = 'select count(*) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "'.$sGroupId.'" ';
        $sCnt = $myDB->getOne( $sQ );

        $this->assertEquals( 0, $sCnt );

        // checking before insert
        $this->assertEquals( 1, count( $oUser->getUserGroups() ) );
    }


    /**
     * Testing onOrderExecute various combinations
     */
    public function testOnOrderExecute0()
    {
        modConfig::getInstance()->setConfigParam( 'sMidlleCustPrice', 99 );
        modConfig::getInstance()->setConfigParam( 'sLargeCustPrice', 999 );

        $myUtils = oxUtils::getInstance();
        $myDB    = oxDb::getDB();
        $myConfig= oxConfig::getInstance();

        $sShopid = $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ];
        $sUserID = $this->_aUsers[ $sShopid ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "'.oxUtilsObject::getInstance()->generateUID().'", "'.$sShopid.'", "'.$sUserID.'", "oxidnotyetordered" )';
        $myDB->Execute( $sQ );

        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $oUser->oxuser__oxdisableautogrp = new oxField(false, oxField::T_RAW);

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(9);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess  = 1;
        $oUser->onOrderExecute( $oBasket, $iSuccess );

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidcustomer"';

        $this->assertEquals( 1, $myDB->getOne( $sQ ) );
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidsmallcust"';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals( 0, $myDB->getOne( $sQ ) );
    }
    public function testOnOrderExecute1()
    {
        modConfig::getInstance()->setConfigParam( 'sMidlleCustPrice', 99 );
        modConfig::getInstance()->setConfigParam( 'sLargeCustPrice', 999 );

        $myUtils = oxUtils::getInstance();
        $myDB    = oxDb::getDB();
        $myConfig= oxConfig::getInstance();

        $sShopid = $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ];
        $sUserID = $this->_aUsers[ $sShopid ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "'.oxUtilsObject::getInstance()->generateUID().'", "'.$sShopid.'", "'.$sUserID.'", "oxidnotyetordered" )';
        $myDB->Execute( $sQ );

        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(699);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess = 1;
        $oUser->onOrderExecute( $oBasket, $iSuccess );

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidcustomer"';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidmiddlecust"';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals( 0, $myDB->getOne( $sQ ) );
    }

    public function testOnOrderExecute2()
    {
        modConfig::getInstance()->setConfigParam( 'sMidlleCustPrice', 99 );
        modConfig::getInstance()->setConfigParam( 'sLargeCustPrice', 999 );

        $myUtils = oxUtils::getInstance();
        $myDB    = oxDb::getDB();
        $myConfig= oxConfig::getInstance();

        $sShopid = $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ];
        $sUserID = $this->_aUsers[ $sShopid ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];

        $sQ = 'insert into oxobject2group (oxid,oxshopid,oxobjectid,oxgroupsid) values ( "'.oxUtilsObject::getInstance()->generateUID().'", "'.$sShopid.'", "'.$sUserID.'", "oxidnotyetordered" )';
        $myDB->Execute( $sQ );

        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $oBasket = $this->getProxyClass("oxBasket");
        $oPrice = oxNew("oxPrice");
        $oPrice->setPrice(1999);
        $oBasket->setNonPublicVar("_oPrice", $oPrice);

        $iSuccess = 1;
        $oUser->onOrderExecute( $oBasket, $iSuccess );

        // checking if (un)assigned to (from) groups
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidcustomer"';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );
        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidgoodcust"';
        $this->assertEquals( 1, $myDB->getOne( $sQ ) );

        $sQ = 'select count(oxid) from oxobject2group where oxobjectid = "'.$sUserID.'" and oxgroupsid = "oxidnotyetordered"';
        $this->assertEquals( 0, $myDB->getOne( $sQ ) );
    }

    /**
     * Testing user basket
     */
    // 1. fetching saved basket for existing user, should return 1
    public function testGetBasketExistingBasket()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();

        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $oBasket = $oUser->getBasket( 'oxtest' );
        $this->assertEquals( 1, count( $oBasket->getItemCount(false) ) );
    }
    // 2. fetching basket for no user - should return 0
    public function testGetBasketNotExistingBasket()
    {
        $myUtils  = oxUtils::getInstance();
        $myDB     = oxDb::getDB();

        $oUser = new oxuser();

        $oBasket = $oUser->getBasket( 'oxtest2' );
        $this->assertEquals( 0, count( $oBasket->oArticles ) );
    }


    /**
     * Testing user birth data converter
     */
    public function testConvertBirthdayGoodInput()
    { // good input
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( '1981-05-14', $oUser->convertBirthday( array( 'year'=> 1981, 'month' => 05, 'day' => 14 ) ) );
    }
    public function testConvertBirthdayAlmostGoodInput()
    { // good input
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( '1981-02-01', $oUser->convertBirthday( array( 'year'=> 1981, 'month' => 02, 'day' => 31 ) ) );
    }
    public function testConvertBirthdayAlmostGoodInput2()
    { // good input
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( '1981-04-01', $oUser->convertBirthday( array( 'year'=> 1981, 'month' => 04, 'day' => 31 ) ) );
    }
    public function testConvertBirthdayBadInput()
    { // bad input
        $oUser = oxNew( 'oxuser' );

        $this->assertEquals( '', $oUser->convertBirthday( 'oxtest' ) );
    }
    public function testConvertBirthdaySomeGoodInput()
    { // bad input
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( '1981-01-01', $oUser->convertBirthday( array( 'year'=> 1981 ) ) );
    }


    /**
     * Testing automatical adding to dyn group
     */
    public function testAddDynGroupEmptyGroup()
    {
        // testing
        $oUser = oxNew( 'oxuser' );
        $this->assertEquals( false, $oUser->addDynGroup(null, array()) );
    }
    public function testAddDynGroupDisabledAutoGrp()
    {
        // testing
        $oUser = oxNew( 'oxuser' );
        $oUser->oxuser__oxdisableautogrp = new oxField(true, oxField::T_RAW);
        $this->assertEquals( false, $oUser->addDynGroup("test", array()) );
    }
    public function testAddDynGroupTryingOxidadminGroup()
    {
        // testing
        $oUser = oxNew( 'oxuser' );
        $this->assertFalse( $oUser->addDynGroup( "oxidadmin", array() ) );
    }
    public function testAddDynGroupTryingAllreadyAdded()
    {
        $myDB      = oxDb::getDB();

        // looking for not assigned group
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $sNewGroup = $myDB->getOne( 'select oxgroupsid from oxobject2group where oxobjectid="'.$sUserID.'" ' );

        // testing
        $this->assertEquals( false, $oUser->addDynGroup($sNewGroup, array()) );
    }
    public function testAddDynGroupTryingNotAdded()
    {
        $myDB      = oxDb::getDB();

        // looking for not assigned group
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $sNewGroup = $myDB->getOne( 'select oxid from oxgroups where oxid not in ( select oxgroupsid from oxobject2group where oxobjectid="'.$sUserID.'" ) and oxid != "oxidadmin"' );

        // testing
        $this->assertEquals( true, $oUser->addDynGroup($sNewGroup, array()) );
    }


    public function testAddDynGroupWithDeniedDynGroups()
    {
        // looking for not assigned group
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
         $this->assertEquals( false, $oUser->addDynGroup("testg", array("testg")) );
    }

    /**
     * Testing login validator
     */
    // 1. testing if method detects dublicate records
    public function testCheckLoginUserWithPassDublicateLogin()
    {
        $myUtils   = oxUtils::getInstance();

        // loading some demo user to test if dublicates possible
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $aInvAdress['oxuser__oxusername'] = $oUser->oxuser__oxusername->value;

        $oLang = oxLang::getInstance();
        $sMsg = sprintf( $oLang->translateString( 'EXCEPTION_USER_USEREXISTS', $oLang->getTplLanguage() ), $aInvAdress['oxuser__oxusername'] );
        oxTestModules::addFunction( "oxInputValidator", "checkLogin", "{ throw new oxUserException('{$sMsg}'); }");

        //
        try {
            $oUser = $this->getProxyClass("oxUser");
            $oUser->UNITcheckLogin( '', $aInvAdress );
        } catch ( oxUserException $oEx){
            $this->assertEquals( $sMsg, $oEx->getMessage() );
            return;
        }
        $this->fail( 'failed test__checkLogin_userWithouPassDublicateLogin test ' );
    }
    // 2. if user tries to change login password must be entered ...
    public function testCheckLoginNewLoginNoPass()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkLogin", "{ throw new oxInputException('EXCEPTION_INPUT_NOTALLFIELDS'); }");
        //
        try {
            $oUser =  $this->getProxyClass("oxUser");

            $oUser->oxuser__oxpassword = new oxField('b@b.b', oxField::T_RAW);
            $oUser->oxuser__oxusername = new oxField('b@b.b', oxField::T_RAW);
            $aInvAdress['oxuser__oxusername'] = 'a@a.a';
            $aInvAdress['oxuser__oxpassword'] = '';

            $oUser->UNITcheckLogin( true, $aInvAdress );
        } catch ( oxInputException $oEx) {
            $this->assertEquals( $oEx->getMessage(), 'EXCEPTION_INPUT_NOTALLFIELDS');
            return;
        }
        $this->fail( 'failed test__checkLogin_userWithouPassDublicateLogin test ' );
    }
    // 3. if user tries to change login CORRECT password must be entered ...
    public function testCheckLoginNewLoginWrongPass()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkLogin", "{ throw new oxUserException('ERROR_MESSAGE_USER_PWDDONTMATCH'); }");
        //
        try {
            $oUser =  $this->getProxyClass("oxUser");

            $oUser->oxuser__oxpassword = new oxField('a@a.a', oxField::T_RAW);
            $oUser->oxuser__oxusername = new oxField('b@b.b', oxField::T_RAW);
            $aInvAdress['oxuser__oxusername'] = 'a@a.a';
            $aInvAdress['oxuser__oxpassword'] = 'b@b.b';

            $oUser->UNITcheckLogin( '', $aInvAdress );
        } catch ( oxUserException $oEx){
            $this->assertEquals( $oEx->getMessage(), 'ERROR_MESSAGE_USER_PWDDONTMATCH');
            return;
        }
        $this->fail( 'failed test__checkLogin_userWithouPassDublicateLogin test ' );
    }
    // 4. if user deletes his name and saves...
    public function testCheckLoginDeleteUserName()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkLogin", "{ throw new oxInputException('EXCEPTION_INPUT_NOTALLFIELDS'); }");
        $myUtils   = oxUtils::getInstance();

        // loading some demo user to test if dublicates possible
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][0];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );
        $aInvAdress['oxuser__oxusername'] = '';
        $aInvAdress['oxuser__oxpassword'] = '';
        $sLogin = $oUser->oxuser__oxusername->value;

        //
        try {
            $oUser->UNITcheckLogin( $sLogin, $aInvAdress );
        } catch ( oxInputException $oEx){
            $this->assertEquals( 'EXCEPTION_INPUT_NOTALLFIELDS', $oEx->getMessage() );
            return;
        }
        $this->fail( 'failed test__checkLogin_DeleteUserName test ' );
    }
    // 5. testing if method detects dublicate records
    public function testCheckForAvailableEmailChangingData()
    {
        // loading some demo user to test if dublicates possible
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $sUsername = $oUser->oxuser__oxusername->value;
        $oUser = $this->getProxyClass("oxUser");
        $oUser->load( 'oxdefaultadmin' );

        $this->assertTrue( $oUser->checkIfEmailExists( $sUsername) );
    }
    // 6. testing if method detects dublicate records
    public function testCheckForAvailableEmailIfNewEmail()
    {
        // loading some demo user to test if dublicates possible
        $sUserID = $this->_aUsers[ $this->_aShops[ rand(0, count( $this->_aShops ) - 1 ) ] ][ rand( 0, count( $this->_aUsers[ 0 ] ) - 1 ) ];
        $oUser   = oxNew( 'oxuser' );
        $oUser->load( $sUserID );

        $this->assertFalse( $oUser->checkIfEmailExists( 'aaaaa') );
    }

    /**
     * Testing email validator
     */
    // 1. user forgot to pass user login - must fail
    public function testCheckEmailNoEmail()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkEmail", "{ throw new oxInputException('EXCEPTION_INPUT_NOTALLFIELDS'); }");
        try {
            $oUser = $this->getProxyClass("oxuser");
            $oUser->UNITcheckEmail( '', 1 );
        } catch ( oxInputException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'EXCEPTION_INPUT_NOTALLFIELDS');
            return;
        }
        $this->fail( 'failed test__checkLogin_userWithouPassDublicateLogin test ' );
    }
    // 2. checking is email validation is executed
    public function testCheckEmailEmailValidation()
    {
        oxAddClassModule( 'Unit_oxuserTest_oxutils2', 'oxUtils' );
        oxTestModules::addFunction( "oxInputValidator", "checkEmail", "{ throw new oxInputException('EXCEPTION_INPUT_NOVALIDEMAIL'); }");

        try {
            $oUser = $this->getProxyClass("oxuser");
            $oUser->UNITcheckEmail( 'a@a.a', 1 );
        } catch ( oxInputException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'EXCEPTION_INPUT_NOVALIDEMAIL' );
            return;
        }
        $this->fail( 'failed test__checkLogin_userWithouPassDublicateLogin test ' );
    }


    /**
     * Testing password validator
     */
    // 1. for user without password - no checks
    public function testCheckPasswordUserWithoutPasswordNothingMustHappen()
    {
        $oUser = $this->getProxyClass("oxuser");
        $this->assertNull( $oUser->checkPassword( '', '' ) );
    }
    // 2. for user without password - and check if it is empty on
    public function testCheckPasswordUserWithoutPassword()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkPassword", "{ throw new oxInputException('ERROR_MESSAGE_INPUT_EMPTYPASS'); }");
        try {
            $oUser = $this->getProxyClass("oxuser");
            $oUser->checkPassword( '', '', true );
        } catch ( oxInputException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'ERROR_MESSAGE_INPUT_EMPTYPASS');
            return;
        }
        $this->fail( 'failed test__checkPassword_passIsEmpty test ' );
    }
    // 3. for user without password - no checks
    public function testCheckPasswordPassTooShort()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkPassword", "{ throw new oxInputException('ERROR_MESSAGE_PASSWORD_TOO_SHORT'); }");
        try {
            $oUser = $this->getProxyClass("oxuser");
            $oUser->checkPassword( 'xxx', '', true );
        } catch ( oxInputException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'ERROR_MESSAGE_PASSWORD_TOO_SHORT');
            return;
        }
        $this->fail( 'failed test__checkPassword_passTooShort test ' );
    }
    // 4. for user without password - no checks
    public function testCheckPasswordPassDoNotMatch()
    {
        oxTestModules::addFunction( "oxInputValidator", "checkPassword", "{ throw new oxUserException('ERROR_MESSAGE_USER_PWDDONTMATCH'); }");
        try {
            $oUser = $this->getProxyClass("oxuser");
            $oUser->checkPassword( 'xxxxxx', 'yyyyyy', $blCheckLenght = false  );
        } catch ( oxUserException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'ERROR_MESSAGE_USER_PWDDONTMATCH');
            return;
        }
        $this->fail( 'failed test__checkPassword_passDoNotMatch test ' );
    }


    /**
     * Testing required fields checker
     */
    // 1. defining required fields in aMustFillFields. While testing original
    // function must throw an exception that not all required fields are filled
    public function testCheckRequiredFieldsSomeMissingAccordingToaMustFillFields()
    {

        $aMustFillFields = array( 'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxstreet',
                                  'oxuser__oxstreetnr', 'oxuser__oxzip', 'oxuser__oxcity',
                                  'oxuser__oxcountryid',
                                  'oxaddress__oxfname', 'oxaddress__oxlname', 'oxaddress__oxstreet',
                                  'oxaddress__oxstreetnr', 'oxaddress__oxzip', 'oxaddress__oxcity',
                                  'oxaddress__oxcountryid'
                                  );

        oxTestModules::addFunction( "oxInputValidator", "checkRequiredFields", "{ throw new oxInputException('EXCEPTION_INPUT_NOTALLFIELDS'); }");
        modConfig::getInstance()->setConfigParam( 'aMustFillFields', $aMustFillFields );

        try {
            $aInvAdress = array();
            $aDelAdress = array();

            $oUser = $this->getProxyClass("oxUser");
            $oUser->UNITcheckRequiredFields( $aInvAdress, $aDelAdress);
        } catch ( oxInputException $oEx ) {
            $this->assertEquals( $oEx->getMessage(), 'EXCEPTION_INPUT_NOTALLFIELDS');
            return;
        }
        $this->fail( 'failed test test_checkRequiredFields' );
    }

    // 2. defining required fields in aMustFillFields. While testing original
    // function must not fail because all defined fields are filled with some values
    public function testCheckRequiredFieldsAllFieldsAreFine()
    {

        $aMustFillFields = array( 'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxbirthdate' );

        modConfig::getInstance()->setConfigParam( 'aMustFillFields', $aMustFillFields );

        try {
            $aInvAdress = array( 'oxuser__oxfname' => 'xxx', 'oxuser__oxbirthdate' => array( 'year' => '123' ) );
            $aDelAdress = array( 'oxuser__oxlname' => 'yyy' );

            $oUser = $this->getProxyClass("oxUser");
            $oUser->UNITcheckRequiredFields( $aInvAdress, $aDelAdress);
        } catch ( oxExcpUserDataCheck $oException ) {
            $this->fail( 'failed test test_checkRequiredFields' );
        }
    }

    /**
     * Testing VAT id checker - no check if no vat id or company name in params list
     */
    public function testCheckVatIdWithoutVatIdOrCompanyName()
    {
        $oUser = $this->getProxyClass("oxUser");

        try {
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 1 ) );
        } catch ( Exception $oException ) {
            $this->fail( 'Check performed when company name param is empty' );
        }

        try {
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 0) );
        } catch ( Exception $oException ) {
            $this->fail( 'Check performed when vat id param is empty' );
        }
    }

    /**
     * Testing VAT id checker - with vat id, company name, but without or bad country id
     */
    public function testCheckVatIdWithBadCountryId()
    {
        try {
            $oUser = $this->getProxyClass("oxUser");
            $oUser->UNITcheckVatId( array( 'oxuser__oxustid' => 1, 'oxuser__oxcountryid' => null ) );
        } catch ( Exception $oException ) {
            $this->fail( 'Vat Id should not be checked without country id' );
        }
    }

    /**
     * Testing VAT id checker - with home country id
     */
    public function testCheckVatIdWithHomeCountryId()
    {
        $oUser = $this->getProxyClass("oxUser");
        $aHome = oxConfig::getInstance()->getConfigParam( 'aHomeCountry' );

        try {
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 'DE123', 'oxuser__oxcountryid' => $aHome[0]) );
        } catch ( Exception $oException ) {
            $this->fail( "while trying to check home country business user with vat id" );
        }
    }

    /**
     * Testing VAT id checker - with foreign country id in which disabled vat checking
     */
    public function testCheckVatIdWithForeignCountryWithDisabledVatChecking()
    {
        $oUser = $this->getProxyClass("oxUser");

        $sForeignCountryId = "a7c40f6321c6f6109.43859248"; //Switzerland

        try {
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => $sForeignCountryId) );
        } catch ( Exception $oException ) {
            $this->fail( "while trying to check foreign country business user with vat id, but country does not allow checking" );
        }
    }

    /**
     * Testing VAT id checker - with foreign country id and bad vat id
     */
    public function testCheckVatIdWithForeignCountryIdAndBadVatId()
    {
        oxTestModules::addFunction('oxInputValidator', 'checkVatId', '{$oEx = oxNew("oxInputException"); $oEx->setMessage("VAT_MESSAGE_ID_NOT_VALID"); throw $oEx;}');

        $sForeignCountryId = "a7c40f6320aeb2ec2.72885259"; //Austria

        try {
            $oUser = oxNew( "oxUser" );
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => $sForeignCountryId) );
            $this->fail( "while trying to check foreign country business user with bad vat id" );
        } catch ( oxInputException $oException ) {
                $this->assertEquals( 'VAT_MESSAGE_ID_NOT_VALID', $oException->getMessage() );
        }
    }

    /**
     * Testing VAT id checker - with foreign country id and good vat id
     */
    public function testCheckVatId()
    {

        $oUser = $this->getProxyClass("oxUser");

        $sForeignCountryId = "a7c40f6320aeb2ec2.72885259"; //Austria

        try {
            $oUser->UNITcheckVatId( array('oxuser__oxustid' => 'AT123', 'oxuser__oxcountryid' => $sForeignCountryId) );
        } catch ( oxInputException $oException ) {
            $this->fail( "while trying to check foreign country business user with good vat id" );
        }
    }


    /**
     * Testing if method checkValues performs all defined actions
     */
    public function testCheckValues()
    {
        $oUser = $this->getMock("oxUser", array("_checkLogin", "_checkEmail", "checkPassword", "_checkRequiredFields", "_checkCountries", "_checkVatId"));
        $oUser->expects($this->once())->method("_checkLogin");
        $oUser->expects($this->once())->method("_checkEmail");
        $oUser->expects($this->once())->method("checkPassword");
        $oUser->expects($this->once())->method("_checkRequiredFields");
        $oUser->expects($this->once())->method("_checkCountries");
        $oUser->expects($this->once())->method("_checkVatId");
        $oUser->checkValues("X", "X", "X", array(), array() );

        $oUser = $this->getMock("oxUser", array("_checkLogin", "_checkEmail", "checkPassword", "_checkRequiredFields", "_checkCountries", "_checkVatId"));
        $oUser->expects($this->once())->method("_checkLogin");
        $oUser->expects($this->once())->method("_checkEmail");
        $oUser->expects($this->once())->method("checkPassword");
        $oUser->expects($this->once())->method("_checkRequiredFields");
        $oUser->expects($this->once())->method("_checkCountries");
        $oUser->expects($this->once())->method("_checkVatId")->will( $this->throwException(new oxInputException()));
        try {
            $oUser->checkValues("X", "X", "X", array(), array() );
            $this->fail('exception not thrown');
        } catch (oxInputException $e) {
        }

    }


    /**
     * Testing if auto group assignment works fine
     */
    // 1. testing if foreigner is automatically assigned/removed to/from special user groups
    public function testSetAutoGroupsForeigner()
    {

        //create the object once, so the proxy class exists exists
        $this->getProxyClass("oxUser");

        $oUser = $this->getMock("oxUserProxy", array("ingroup", "removefromgroup", "addtogroup"));
        $oUser->expects($this->once())->method("removeFromGroup");
        $oUser->expects($this->once())->method("addToGroup");
        $oUser->expects($this->exactly(2))->method('inGroup')->will($this->onConsecutiveCalls($this->returnValue(false), $this->returnValue(true)));

        $oUser->UNITsetAutoGroups( 'xxx', array());

    }
    // 2. testing if native country customer is automatically assigned/removed to/from special user groups
    public function testSetAutoGroupsNative()
    {
        //create the object once, so the proxy class exists exists
        $this->getProxyClass( "oxUser" );

        $oUser = $this->getMock( "oxUserProxy", array( "ingroup", "removefromgroup", "addtogroup" ) );
        modConfig::getInstance()->setConfigParam('aHomeCountry', 'xxx');
        $oUser->expects( $this->once() )->method( "removeFromGroup" );
        $oUser->expects( $this->once() )->method( "addToGroup" );
        $oUser->expects( $this->any() )->method( 'inGroup' )->will($this->onConsecutiveCalls( $this->returnValue( true ), $this->returnValue( false ) ) );

        $oUser->UNITsetAutoGroups( 'xxx' );

    }

    public function testSetAutoGroupsNativeMultiple()
    {
        //create the object once, so the proxy class exists exists
        $this->getProxyClass( "oxUser" );

        $oUser = $this->getMock( "oxUserProxy", array( "ingroup", "removefromgroup", "addtogroup" ) );
        modConfig::getInstance()->setConfigParam('aHomeCountry', array('asd', 'xxx', 'ad'));
        $oUser->expects( $this->once() )->method( "removeFromGroup" );
        $oUser->expects( $this->once() )->method( "addToGroup" );
        $oUser->expects( $this->any() )->method( 'inGroup' )->will($this->onConsecutiveCalls( $this->returnValue( true ), $this->returnValue( false ) ) );

        $oUser->UNITsetAutoGroups( 'xxx' );
    }

    /**
     * Testing if newsletter subscription setter is executed properly
     */

    public function testSetNewsSubscriptionSubscribesButOntInStatusEq1()
    {
        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->once() )->method( 'hasModule')->will( $this->returnValue( true ) );
        $oConfig = $this->getMock( 'oxconfig');

        $oSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'setOptInStatus' ) );
        $oSubscription->expects( $this->once() )->method( 'getOptInStatus')->will( $this->returnValue( 1 ) );
        $oSubscription->expects( $this->never() )->method( 'setOptInStatus');

        $oUser = $this->getMock( 'oxuser', array( 'getNewsSubscription', 'addToGroup', 'removeFromGroup' ) );
        $oUser->expects( $this->once() )->method( 'getNewsSubscription')->will( $this->returnValue( $oSubscription ) );
        $oUser->expects( $this->never() )->method( 'addToGroup');
        $oUser->expects( $this->never() )->method( 'removeFromGroup');
        $oUser->setConfig( $oConfig );

        $this->assertFalse( $oUser->setNewsSubscription( true, false ) );
    }
    public function testSetNewsSubscriptionSubscribesNoOptInEmail()
    {
        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->once() )->method( 'hasModule')->will( $this->returnValue( true ) );
        $oConfig = $this->getMock( 'oxconfig');
        $oConfig->setConfigParam( 'blOrderOptInEmail', false );

        $oSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'setOptInStatus' ) );
        $oSubscription->expects( $this->once() )->method( 'getOptInStatus')->will( $this->returnValue( 0 ) );
        $oSubscription->expects( $this->once() )->method( 'setOptInStatus')->with( $this->equalTo( 1 ) );

        $oUser = $this->getMock( 'oxuser', array( 'getNewsSubscription', 'addToGroup', 'removeFromGroup' ) );
        $oUser->expects( $this->once() )->method( 'getNewsSubscription')->will( $this->returnValue( $oSubscription ) );
        $oUser->expects( $this->once() )->method( 'addToGroup')->with( $this->equalTo( 'oxidnewsletter' ) );
        $oUser->expects( $this->never() )->method( 'removeFromGroup');
        $oUser->setConfig( $oConfig );

        $this->assertTrue( $oUser->setNewsSubscription( true, false ) );
    }
    public function testSetNewsSubscriptionSubscribesWithOptInEmail()
    {
        oxAddClassModule( 'oxuserTestEmail', 'oxemail' );

        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->once() )->method( 'hasModule')->will( $this->returnValue( true ) );
        $oConfig = $this->getMock( 'oxconfig');
        $oConfig->setConfigParam( 'blOrderOptInEmail', true );

        $oSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'setOptInStatus' ) );
        $oSubscription->expects( $this->once() )->method( 'getOptInStatus')->will( $this->returnValue( 0 ) );
        $oSubscription->expects( $this->once() )->method( 'setOptInStatus')->with( $this->equalTo( 2 ) );

        $oUser = $this->getMock( 'oxuser', array( 'getNewsSubscription', 'addToGroup', 'removeFromGroup' ) );
        $oUser->expects( $this->once() )->method( 'getNewsSubscription')->will( $this->returnValue( $oSubscription ) );
        $oUser->expects( $this->never() )->method( 'addToGroup');
        $oUser->expects( $this->never() )->method( 'removeFromGroup');
        $oUser->setConfig( $oConfig );

        $this->assertTrue( $oUser->setNewsSubscription( true, true ) );
    }

    public function testSetNewsSubscriptionSubscribesWithOptInEmail_sendsOnlyOnce()
    {
        // email should be sent only once
        $oEmail = $this->getMock( 'oxemail', array( 'sendNewsletterDBOptInMail') );
        $oEmail->expects( $this->once() )->method( 'sendNewsletterDBOptInMail')->will( $this->returnValue( true ) );

        oxTestModules::addModuleObject( "oxemail", $oEmail );

        $oConfig = $this->getMock( 'oxconfig');
        $oConfig->setConfigParam( 'blOrderOptInEmail', true );

        $oSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'setOptInStatus' ) );
        $oSubscription->expects( $this->at( 0 ) )->method( 'getOptInStatus')->will( $this->returnValue( 0 ) );
        $oSubscription->expects( $this->at( 1 ) )->method( 'setOptInStatus')->with( $this->equalTo( 2 ) );
        $oSubscription->expects( $this->at( 2 ) )->method( 'getOptInStatus')->will( $this->returnValue( 2 ) );
        $oSubscription->expects( $this->at( 3 ) )->method( 'setOptInStatus')->with( $this->equalTo( 2 ) );

        $oUser = $this->getMock( 'oxuser', array( 'getNewsSubscription', 'addToGroup', 'removeFromGroup' ) );
        $oUser->expects( $this->any() )->method( 'getNewsSubscription')->will( $this->returnValue( $oSubscription ) );
        $oUser->setConfig( $oConfig );

        // first call, mail should be sent
        $this->assertTrue( $oUser->setNewsSubscription( true, true ) );

        // second call, mail should not be sent
        $this->assertTrue( $oUser->setNewsSubscription( true, true ) );
    }

    public function testSetNewsSubscriptionUnsubscribes()
    {
        oxAddClassModule( 'oxuserTestEmail', 'oxemail' );

        //$oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        //$oConfig->expects( $this->once() )->method( 'hasModule')->will( $this->returnValue( true ) );
        $oConfig = $this->getMock( 'oxconfig');

        $oSubscription = $this->getMock( 'oxnewssubscribed', array( 'getOptInStatus', 'setOptInStatus' ) );
        $oSubscription->expects( $this->never() )->method( 'getOptInStatus');
        $oSubscription->expects( $this->once() )->method( 'setOptInStatus')->with( $this->equalTo( 0 ) );

        $oUser = $this->getMock( 'oxuser', array( 'getNewsSubscription', 'addToGroup', 'removeFromGroup' ) );
        $oUser->expects( $this->once() )->method( 'getNewsSubscription')->will( $this->returnValue( $oSubscription ) );
        $oUser->expects( $this->never() )->method( 'addToGroup');
        $oUser->expects( $this->once() )->method( 'removeFromGroup')->with( $this->equalTo( 'oxidnewsletter' ) );
        $oUser->setConfig( $oConfig );

        $this->assertTrue( $oUser->setNewsSubscription( false, false ) );
    }

    /**
     * Testing customer information update function
     */
    // 1. all data "is fine" (emulated), just checking if all necessary methods were called
    public function testChangeUserDataAllDataIsFine()
    {
        //modConfig::getInstance()->addClassFunction( 'hasModule', create_function( '$sModule', 'return true;' ) );
            $oUser = $this->getMock("oxUser", array("checkValues", "assign", "save", "_setAutoGroups"));

        $oUser->expects( $this->once() )->method( 'checkValues' );
        $oUser->expects( $this->once() )->method( 'assign' );
        $oUser->expects( $this->once() )->method( 'save' )->will($this->returnValue(true));
        $oUser->expects( $this->once() )->method( '_setAutoGroups' );

        $oUser->changeUserData( null, null, null, null, null, null, null, null, null, null );

    }

    /**
     * oxuser::loadAdminUser() test
     */
    public function testLoadAdminUser()
    {
        oxAddClassModule('Unit_oxuserTest_oxUtilsServer', 'oxUtilsServer');
        //not logged in
        $oUser = oxNew( 'oxuser' );
        $this->assertFalse($oUser->loadAdminUser());
        //logging in
        $testUser = new oxuser();
        $testUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oActUser = new oxuser();
        $oActUser->loadAdminUser();
        $this->assertNull($oActUser->oxuser__oxusername->value);
        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( true ) );
        $testUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oActUser->loadAdminUser();
        $this->assertEquals($oActUser->oxuser__oxusername->value, oxADMIN_LOGIN);
        $testUser->logout();
        $oUser = oxNew( 'oxuser' );
        $this->assertFalse($oUser->loadAdminUser());
    }

    /**
     * oxuser::getUser() test
     */
    public function testGetUser()
    {
        //not logged in
        $oActUser = new oxuser();
        $this->assertFalse($oActUser->loadActiveUser());
        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        //trying to login
        $testUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oActUser->loadActiveUser();
        $testUser->logout();
        $this->assertEquals($oActUser->oxuser__oxusername->value, oxADMIN_LOGIN);
    }

    /**
     * oxuser::getUser() test
     */
    public function testGetUserNotAdmin()
    {
        $this->markTestSkipped('skip for user login');
        oxAddClassModule( 'Unit_oxuserTest_oxUtilsServer2', 'oxutilsserver' );
        $sShopId = oxConfig::getInstance()->getShopId();

        //not logged in
        $oActUser = new oxuser();
        $this->assertFalse($oActUser->loadActiveUser());
        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $sPassSalt = oxDb::getDb()->getOne('select OXPASSSALT from oxuser where OXID="oxdefaultadmin"');
        $sVal = oxADMIN_LOGIN . '@@@' . crypt( $oActUser->encodePassword( oxADMIN_PASSWD, $sPassSalt ), $sPassSalt );
        oxUtilsServer::getInstance()->setOxCookie( 'oxid_'.$sShopId, $sVal );

        $oActUser->loadActiveUser();
        $testUser->logout();
        $this->assertEquals( $oActUser->oxuser__oxusername->value, oxADMIN_LOGIN );
    }


    /**
     * oxuser::login() test. Checks if login process throws an exception when cookies are not
     * supported for admin.
     */
    public function testLogin_AdminCookieSupport()
    {
        oxAddClassModule('Unit_oxuserTest_oxUtilsServer2', 'oxUtilsServer');
        $oUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $oUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( true ) );
        oxUtilsServer::getInstance()->delOxCookie();
        try
        {
            //should throw no cookie support exception
            $oUser->login(1, oxADMIN_PASSWD);
        }
        catch (Exception $e) {
            return ;
        }

        $this->fail("Mandatory admin cookies are not checked");
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLogin_Logout()
    {
        $oUser = new oxuser();
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $this->assertEquals( oxSession::getVar( 'usr' ), 'oxdefaultadmin' );
        $this->assertNull( oxSession::getVar( 'auth' ) );

        $oUser = $oUser->getUser();

        $this->assertNotNull( $oUser );
        $this->assertEquals( 'oxdefaultadmin', $oUser->getId() );

        $oUser->logout();

        $this->assertNull( oxSession::getVar( 'usr' ) );
        $this->assertNull( oxSession::getVar( 'auth' ) );
        $this->assertFalse( $oUser->getUser() );
    }

    /**
     * oxuser::login() - restets active user on login
     */
    public function testLogin_resetsActiveUser()
    {
        $oUser = $this->getMock("oxuser", array("setUser"));
        $oUser->expects($this->once())->method("setUser")->with( $this->equalTo(null) );

        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
    }

    /**
     * oxuser::login() test, tests if basket loading is called after successful login
     * Bug #2039
     *
     */
    /*
    public function testLoginBasketLoaded()
    {
        $oBasket = $this->getMock("oxbasket", array("load"));
        $oBasket->expects($this->once())->method("load");

        oxTestModules::addModuleObject( "oxbasket", $oBasket );

        $oUser = new oxuser();
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
    }*/

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginByPassingCustomerNumber_Logout()
    {
        $oUser = new oxuser();
        $oUser->login( 1, oxADMIN_PASSWD);
        $this->assertEquals( oxSession::getVar( 'usr' ), 'oxdefaultadmin' );
        $this->assertNull( oxSession::getVar( 'auth' ) );

        $oUser = $oUser->getUser();

        $this->assertNotNull( $oUser );
        $this->assertEquals( 'oxdefaultadmin', $oUser->getId() );

        $oUser->logout();

        $this->assertNull( oxSession::getVar( 'usr' ) );
        $this->assertNull( oxSession::getVar( 'auth' ) );
        $this->assertFalse( $oUser->getUser() );
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginButUnableToLoadExceptionWillBeThrown()
    {
        $oUser = $this->getMock( 'oxuser', array( 'load' ));
        $oUser->expects( $this->atLeastOnce() )->method( 'load' )->will( $this->returnValue( false ) );

        try {
            $oUser->login( oxADMIN_LOGIN, oxADMIN_PASSWD);
        } catch ( Exception $oExcp ) {

            $this->assertEquals( 'ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage() );
            return;
        }
        $this->fail( 'exception must be thrown due to problems loading user object' );
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginOxidNotSet()
    {
        modConfig::getInstance()->setConfigParam( 'blUseLDAP', 1 );
        modConfig::getInstance()->setConfigParam( 'blMallUsers', 1 );

        $oUser = $this->getMock( 'oxuser', array( 'load', '_ldapLogin' ));
        $oUser->expects( $this->atLeastOnce() )->method( 'load' )->will( $this->returnValue( true ) );

        try {
            $oUser->login( oxADMIN_LOGIN, oxADMIN_PASSWD);
        } catch ( Exception $oExcp ) {
            $this->assertEquals( 'ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage() );
            return;
        }
        $this->fail( 'exception must be thrown due to problems loading user object' );
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginCookieMustBeSet()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setUserCookie', '{ throw new Exception( "cookie is set" ); }');

        $oUser = new oxuser();
        try {
            $this->assertTrue( $oUser->login( oxADMIN_LOGIN, oxADMIN_PASSWD, true ) );
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "cookie is set", $oExcp->getMessage() );
            return;
        }
        $this->fail( 'forced exception must be thrown' );
    }

    /**
     * oxuser::login() and oxuser::logout() test
     */
    public function testLoginCookie_disabled()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setUserCookie', '{ throw new Exception( "cookie is set" ); }');
        modConfig::getInstance()->setConfigParam( 'blShowRememberMe', 0 );

        $oUser = new oxuser();
        try {
            $this->assertTrue( $oUser->login( oxADMIN_LOGIN, oxADMIN_PASSWD, true ) );
        } catch ( Exception $oExcp ) {
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
        modConfig::getInstance()->setConfigParam( 'blDemoShop', 1 );

        $oUser = $this->getMock( 'oxuser', array( 'isAdmin' ));
        $oUser->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( true ) );

        try {
            $oUser->login( 'nonadmin', oxADMIN_PASSWD);
        } catch ( Exception $oExcp ) {
            $this->assertEquals( 'ERROR_MESSAGE_USER_NOVALIDLOGIN', $oExcp->getMessage() );
            return;
        }
        $this->fail( 'exception must be thrown' );
    }

    /**
     * oxUser::login() and oxUser::logout() test for demo shop
     */
    public function testLogin_Logout_AdminDemoShop()
    {
        $oConfig = $this->getConfig();

        oxAddClassModule( 'Unit_oxuserTest_oxUtilsServer', 'oxutilsserver' );
        $oConfig->setConfigParam( 'blDemoShop', 1 );
        $oConfig->setAdminMode( true );

        $oUser = new oxUser();
        // demo shop login data: admin/admin here
        $oUser->login( "admin", "admin" );

        $this->assertNotNull( $this->getSessionParam('auth') );

        // 'usr' var should not be set here in admin
        $this->assertNull( $this->getSessionParam('usr') );

        $oUser = $oUser->getUser();

        $this->assertNotNull( $oUser );
        $this->assertNotNull( $oUser->getId() );

        $oUser->logout();
        $this->assertNull( $this->getSessionParam('usr') );
        $this->assertNull( $this->getSessionParam('auth') );
        $this->assertFalse( $oUser->getUser() );

    }

    /**
     * oxuser::logout() test
     */
    public function testLogout()
    {
        $oUser = new oxuser();
        $oUser->login( oxADMIN_LOGIN, oxADMIN_PASSWD );

        oxSession::setVar( 'dgr', 'test' );
        oxSession::setVar( 'dynvalue', 'test' );
        oxSession::setVar( 'paymentid', 'test' );
        //oxSession::setVar( 'deladrid', 'test' );

        $oUser = $oUser->getUser();

        if ( $oUser ) {
            $this->assertNotNull( $oUser );
            $this->assertEquals( 'oxdefaultadmin', $oUser->getId() );

            $oUser->logout();

            $this->assertNull( oxSession::getVar( 'dgr' ) );
            $this->assertNull( oxSession::getVar( 'dynvalue' ) );
            $this->assertNull( oxSession::getVar( 'paymentid' ) );
            //$this->assertNull( oxSession::getVar( 'deladrid' ) );
            $this->assertFalse( $oUser->getUser() );
        } else {
            $this->fail( 'User not loaded' );
        }
    }

    /**
     * Address assignment test
     */
    // trying to set empty address
    public function testAssignAddressNoAddressIsSet()
    {
        oxSession::setVar( 'deladrid', 'xxx' );
        $aDelAddress = array();

        $oUser = new oxuser();
        $oUser->UNITassignAddress( $aDelAddress );

        $this->assertNull( oxSession::getVar( 'deladrid' ) );
    }

    // trying to set non empty address
    public function testAssignAddress()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';
        $aDelAddress['oxaddress__oxcountryid'] = 'a7c40f631fc920687.20179984';

        $this->getConfig()->setParameter( 'oxaddressid', 'xxx' );

        $oUser->UNITassignAddress( $aDelAddress );
        $myDB = oxDb::getDB();
        $sSelect = 'select oxaddress.oxcountry from oxaddress where oxaddress.oxid = "xxx" AND oxaddress.oxuserid = "'.$sUserId.'" ';

        $sCountry = $myDB->getOne( $sSelect);
        $this->assertEquals( 'xxx', oxSession::getVar( 'deladrid' ) );
        $this->assertEquals( 'Deutschland', $sCountry );
    }

    // trying to set non empty address
    public function testAssignAddressWithSpecialChar()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';
        $aDelAddress['oxaddress__oxcountryid'] = 'a7c40f631fc920687.20179984';
        $aDelAddress['oxaddress__oxcompany'] = 'xxx & CO.';

        modConfig::setParameter( 'oxaddressid', 'xxx' );

        $oUser->UNITassignAddress( $aDelAddress );
        $myDB = oxDb::getDB();
        $this->assertEquals( 'xxx', oxSession::getVar( 'deladrid' ) );
        $sSelect = 'select oxaddress.oxcompany from oxaddress where oxaddress.oxuserid = "'.$sUserId.'" AND oxid = "xxx" ';

        $sCompany = $myDB->getOne( $sSelect);
        $this->assertEquals( 'xxx & CO.', $sCompany );
    }

    /**
     * oxuser::getSelectedAddress() test
     */
    public function testGetSelectedAddress()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][1];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        modConfig::setParameter( 'deladrid', null );
        modConfig::setParameter( 'oxaddressid', 'test_user1' );

        $oAddress = $oUser->getSelectedAddress();
        $this->assertEquals( 'test_user1', $oAddress->getId() );
    }

    /**
     * oxuser::getSelectedAddress() test
     */
    public function testGetSelectedAddressNewAddress()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        $aDelAddress = array();
        $aDelAddress['oxaddress__oxfname'] = 'xxx';
        $aDelAddress['oxaddress__oxlname'] = 'xxx';

        modConfig::setParameter( 'deladrid', null );
        modConfig::setParameter( 'oxaddressid', 'xxx' );

        $oUser->UNITassignAddress( $aDelAddress );

        oxSession::setVar( 'oxaddressid', null );
        $oAddress = $oUser->getSelectedAddress();
        $this->assertEquals( 'xxx', $oAddress->getId() );
    }

    /**
     * oxuser::getSelectedAddress() if address is not selected
     */
    public function testGetSelectedAddressNotSelected()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        oxSession::setVar( 'deladrid', null );
        oxSession::setVar( 'oxaddressid', null );
        $oSelAddress = $oUser->getSelectedAddress();
        $oUser->oAddresses->rewind();
        $oAddress = $oUser->oAddresses->current();
        $this->assertEquals( $oAddress->getId(), $oSelAddress->getId() );
        $this->assertEquals( 1, $oAddress->selected );
    }

    /**
     * oxuser::getSelectedAddress() if article
     * from wishlist is added, load wishid address
     */
    public function testGetSelectedAddressWishId()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = new oxuser();
        $oUser->load( $sUserId );

        oxSession::setVar( 'deladrid', null );
        oxSession::setVar( 'oxaddressid', null );

        $oSelAddress = $oUser->getSelectedAddress( $sUserId );
        $this->assertEquals( 'test_user0', $oSelAddress->getId() );
    }



    public function testGetNoticeListArtCnt()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = $this->getProxyClass( "oxuser" );
        $oUser->load( $sUserId );

        $oBasket = $this->getMock( 'oxbasket', array( 'getItemCount' ) );
        $oBasket->expects( $this->once() )->method( 'getItemCount' )->will( $this->returnValue( 11 ) );
        $aBaskets['noticelist'] = $oBasket;
        $oUser->setNonPublicVar('_aBaskets', $aBaskets);

        $this->assertEquals( 11, $oUser->getNoticeListArtCnt() );
    }

    public function testGetWishListArtCnt()
    {
        reset( $this->_aShops );
        $sShopId = reset( $this->_aShops );
        $sUserId = $this->_aUsers[$sShopId][0];
        $oUser = $this->getProxyClass( "oxuser" );
        $oUser->load( $sUserId );

        $oBasket = $this->getMock( 'oxbasket', array( 'getItemCount' ) );
        $oBasket->expects( $this->once() )->method( 'getItemCount' )->will( $this->returnValue( 11 ) );
        $aBaskets['wishlist'] = $oBasket;
        $oUser->setNonPublicVar('_aBaskets', $aBaskets);

        $this->assertEquals( 11, $oUser->getWishListArtCnt() );
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
        $oUser = new oxUser();
        $oUser->oxuser__oxcompany   = new oxField('Company');
        $oUser->oxuser__oxfname     = new oxField('First name');
        $oUser->oxuser__oxlname     = new oxField('Last name');
        $oUser->oxuser__oxstreet    = new oxField('Street');
        $oUser->oxuser__oxstreetnr  = new oxField('Street number');
        $sEncoded = $oUser->getEncodedDeliveryAddress();

        $oUser->oxuser__oxstreetnr  = new oxField('Street 41');

        $this->assertNotEquals( $sEncoded, $oUser->getEncodedDeliveryAddress() );

        $oUser->oxuser__oxstreetnr  = new oxField('Street number');

        $this->assertEquals( $sEncoded, $oUser->getEncodedDeliveryAddress() );
    }

    public function testIsLoadedFromCookie()
    {
        $oUser = $this->getProxyClass( "oxuser" );
        $oUser->setNonPublicVar('_blLoadedFromCookie', true);

        $this->assertTrue( $oUser->isLoadedFromCookie() );
    }

    /**
     * oxuser::getUserCountryId()
     */
    public function testGetUserCountryId()
    {
        $oUser = new oxuser();
        $this->assertEquals( "a7c40f631fc920687.20179984", $oUser->getUserCountryId('DE') );
    }

    /**
     * oxuser::getUserCountry()
     */
    public function testGetUserCountryWithId()
    {
        $oUser = $this->getProxyClass("oxUser");
        $this->assertEquals( "Deutschland", $oUser->getUserCountry("a7c40f631fc920687.20179984")->value );
        $this->assertNull( $oUser->getNonPublicVar("_oUserCountryTitle") );
    }

    /**
     * oxuser::getUserCountry()
     */
    public function testGetUserCountry()
    {
        $oUser = $this->getProxyClass("oxUser");
        $oUser->load('oxdefaultadmin');
        $this->assertEquals( "Deutschland", $oUser->getUserCountry()->value );
        $this->assertEquals( "Deutschland", $oUser->getNonPublicVar("_oUserCountryTitle")->value );
        $this->assertEquals( $oUser->getUserCountry()->value, oxDb::getDb()->getOne( 'select oxtitle'.oxLang::getInstance()->getLanguageTag( null ).' from oxcountry where oxid = "'.$oUser->oxuser__oxcountryid->value.'"' ) );
    }

    public function testGetReviewUserHash()
    {
        $sReviewUser = oxDb::getDB()->getOne('select md5(concat("oxid", oxpassword, oxusername )) from oxuser where oxid = "oxdefaultadmin"');
        $oUser = $this->getProxyClass( "oxuser" );

        $this->assertEquals( $sReviewUser, $oUser->getReviewUserHash('oxdefaultadmin') );
    }

    public function testGetReviewUserId()
    {
        $sReviewUser = oxDb::getDB()->getOne('select md5(concat("oxid", oxpassword, oxusername )) from oxuser where oxid = "oxdefaultadmin"');
        $oUser = $this->getProxyClass( "oxuser" );

        $this->assertEquals( 'oxdefaultadmin', $oUser->getReviewUserId($sReviewUser) );
    }

    /**
     * Testing state getter
     */
    public function testGetState()
    {
        $oSubj = new oxUser();
        $oSubj->oxuser__oxstateid = new oxField('TTT');
        $this->assertEquals('TTT', $oSubj->getState());
    }

    /**
     * Testing saving updating user facebook ID if user is connete via Facebook connect
     */
    public function testSaveUpdatesFacebookId()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return true;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", false );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // FB connect is disabled so no value should be saved
        $oUser = new oxUser();
        $oUser->load( $sUserId );
        $oUser->save();

        $this->assertEquals( 0, oxDb::getDb()->getOne("select oxfbid from oxuser where oxid='$sUserId' ")  );

        // FB connect is eanbled, FB ID is expected
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );
        $oUser->save();

        $this->assertEquals( 123456, oxDb::getDb()->getOne("select oxfbid from oxuser where oxid='$sUserId' ")  );
    }

    /**
     * Testing saving updating user facebook ID - user is not connected via Facebook
     */
    public function testSaveUpdatesFacebookId_notConnected()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return false;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // FB connect is disabled so no value should be saved
        $oUser = new oxUser();
        $oUser->load( $sUserId );
        $oUser->save();

        $this->assertEquals( 0, oxDb::getDb()->getOne("select oxfbid from oxuser where oxid='$sUserId' ")  );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via facebook connect
     * when user logged in to fb and user exists in db
     */
    public function testLoadActiveUser_FacebookConnectLoggedIn()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return true;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // Saving user Facebook ID
        oxDb::getDb()->execute( "update oxuser set oxactive = 1, oxfbid='123456' where oxid='$sUserId' " );

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertTrue( $testUser->loadActiveUser() );
        $this->assertEquals( $sUserId, $testUser->getId() );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via facebook connect
     * when user logged in to fb and no user exists in db
     */
    public function testLoadActiveUser_FacebookConnectLoggedInNoUser()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return true;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // Saving user Facebook ID
        oxDb::getDb()->execute( "update oxuser set oxactive = 1, oxfbid='' where oxid='$sUserId' " );

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertFalse( $testUser->loadActiveUser() );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via facebook connect
     * when user is not connected to fb, but exists in db
     */
    public function testLoadActiveUser_FacebookConnectNotLoggedIn()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return false;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // Saving user Facebook ID
        oxDb::getDb()->execute( "update oxuser set oxactive = 1, oxfbid='123456' where oxid='$sUserId' " );

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertFalse( $testUser->loadActiveUser() );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via facebook connect
     * when facebook connect is disabled
     */
    public function testLoadActiveUser_FacebookConnectDisabled()
    {
        oxTestModules::addFunction( "oxFb", "isConnected", "{return true;}" );
        oxTestModules::addFunction( "oxFb", "getUser", "{return 123456;}" );
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", false );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        // Saving user Facebook ID
        oxDb::getDb()->execute( "update oxuser set oxactive = 1, oxfbid='123456' where oxid='$sUserId' " );

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertFalse( $testUser->loadActiveUser() );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via cookie
     * when user exists and cookie info is correct
     */
    public function testLoadActiveUser_CookieLogin()
    {
        modConfig::getInstance()->setConfigParam( "blShowRememberMe", true );

        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        $oUser = new oxUser();
        $oUser->load($sUserId);
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->setPassword('testPassword');
        $oUser->save();

        oxRegistry::get("oxUtilsServer")->setUserCookie(
            $oUser->oxuser__oxusername->value,
            $oUser->oxuser__oxpassword->value, null, 31536000, $oUser->oxuser__oxpasssalt->value
        );

        $sCookie = oxRegistry::get("oxUtilsServer")->getUserCookie();

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertTrue( $testUser->loadActiveUser() );

        $this->assertEquals( $sCookie, oxRegistry::get("oxUtilsServer")->getUserCookie() );
    }

    /**
     * oxuser::laodActiveUser() test loading active user via cookie
     * when user defined in cookie is not found
     */
    public function testLoadActiveUser_CookieResetting()
    {
        modConfig::getInstance()->setConfigParam( "blShowRememberMe", true );

        oxRegistry::get("oxUtilsServer")->setUserCookie( 'RandomUserId', 'RandomPassword');

        $testUser = $this->getMock( 'oxuser', array( 'isAdmin' ) );
        $testUser->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );

        $this->assertFalse( $testUser->loadActiveUser() );

        $this->assertNull( oxRegistry::get("oxUtilsServer")->getUserCookie() );
    }


    public function testGetWishListId()
    {
        $oBasketItem = $this->getMock( 'oxBasketItem', array( 'getWishId' ) );
        $oBasketItem->expects( $this->once() )->method( 'getWishId')->will( $this->returnValue( "testwishid" ) );
        $oBasket = $this->getMock( 'oxBasket', array( 'getContents' ) );
        $oBasket->expects( $this->once() )->method( 'getContents')->will( $this->returnValue( array($oBasketItem) ) );
        $oSession = $this->getMock( 'oxSession', array( 'getBasket' ) );
        $oSession->expects( $this->once() )->method( 'getBasket')->will( $this->returnValue( $oBasket ) );
        $oUserView = $this->getMock( 'oxuser', array( 'getSession' ) );
        $oUserView->expects( $this->once() )->method( 'getSession')->will( $this->returnValue( $oSession ) );
        $this->assertEquals( "testwishid", $oUserView->UNITgetWishListId() );
    }

    /**
     * Testing method updateInvitationStatistics()
     *
     * @return null
     */
    public function testUpdateInvitationStatistics()
    {
        $aRecEmails = array( "test1@oxid-esales.com", "test2@oxid-esales.com" );

        $oUser = $this->getProxyClass( 'oxuser' );
        $oUser->load("oxdefaultadmin");
        $oUser->updateInvitationStatistics( $aRecEmails );

        $aRec = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getAll( "select * from oxinvitations order by oxemail");

        $this->assertEquals( "oxdefaultadmin", $aRec[0]["OXUSERID"] );
        $this->assertEquals( "test1@oxid-esales.com", $aRec[0]["OXEMAIL"] );
        $this->assertEquals( "1", $aRec[0]["OXPENDING"] );
        $this->assertEquals( "0", $aRec[0]["OXACCEPTED"] );
        $this->assertEquals( "1", $aRec[0]["OXTYPE"] );

        $this->assertEquals( "oxdefaultadmin", $aRec[1]["OXUSERID"] );
        $this->assertEquals( "test2@oxid-esales.com", $aRec[1]["OXEMAIL"] );
        $this->assertEquals( "1", $aRec[1]["OXPENDING"] );
        $this->assertEquals( "0", $aRec[1]["OXACCEPTED"] );
        $this->assertEquals( "1", $aRec[1]["OXTYPE"] );
    }

    /**
     * Test case for oxUSer::_getLoginQuery() - demoshop + admin mode
     *
     * @return null
     */
    public function testGetLoginQuery_demoShopAdminMode()
    {
        $this->markTestSkipped('replace with integration test');
        // demoshop + admin

            $oConfig = $this->getMock( "oxConfig", array( "isDemoShop" ) );
            $oConfig->expects( $this->once() )->method( 'isDemoShop')->will( $this->returnValue( true ) );
            $sWhat = "oxid";

        $oUser = $this->getMock( "oxUser", array( "getConfig" ), array(), '', false );
        $oUser->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $sQ = "select $sWhat from oxuser where oxrights = 'malladmin' ";

        $this->assertEquals( $sQ, $oUser->UNITgetLoginQuery( "admin", "admin", "testShopId", true ) );
    }

    /**
     * Test case for oxUSer::_getLoginQuery() - staging mode
     *
     * @return null
     */
    public function testGetLoginQuery_demoShopAdminMode_InvalidLogin()
    {
        $this->markTestSkipped('replace with integration');
        // demoshop + admin

            $oConfig = $this->getMock( "oxConfig", array( "isDemoShop" ) );
            $oConfig->expects( $this->once() )->method( 'isDemoShop')->will( $this->returnValue( true ) );

        $oUser = $this->getMock( "oxUser", array( "getConfig" ), array(), '', false );
        $oUser->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );

        $this->setExpectedException( 'oxUserException' );
        $oUser->UNITgetLoginQuery( "notadmin", "notadmin", "testShopId", true );
    }

    /**
     * Test case for oxUSer::_getLoginQuery() - staging mode
     *
     * @return null
     */
    public function testGetLoginQuery_stagingMode()
    {
        $this->markTestSkipped('repalce with integration');
    }

    /**
     * Test case for oxUSer::_getLoginQuery() - staging mode
     *
     * @return null
     */
    public function testGetLoginQuery_stagingMode_InvalidLogin()
    {
        $this->markTestSkipped('replace with integrations');
    }

    /**
     * Test case for #0002616: oxuser: addToGroup and inGroup inconsistent
     *
     * @return null
     */
    public function testAddToGroupFor0002616()
    {
        $aUsers  = current( $this->_aUsers );
        $sUserId = current( $aUsers );

        $oUser = $this->getMock( "oxuser", array( "inGroup" ) );
        $oUser->expects( $this->any() )->method( 'inGroup')->will( $this->returnValue( false ) );
        $oUser->load( $sUserId );

        $oGroup = new oxGroups();
        $oGroup->setId( '_testGroup' );
        $oGroup->oxgroups__oxtitle  = new oxfield( '_testGroup' );
        $oGroup->oxgroups__oxactive = new oxfield( 1 );
        $oGroup->save();

        $this->assertTrue( $oUser->addToGroup( "_testGroup" ) );
        $this->assertFalse( $oUser->addToGroup( "nonsense" ) );
    }



    public function testGetIdByUserName()
    {
        $oUser = new oxUser();
        $oUser->setId( "_testId_1" );
        $oUser->oxuser__oxusername = new oxField( "aaa@bbb.lt", oxField::T_RAW );
        $oUser->oxuser__oxshopid   = new oxField( oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW );
        $oUser->save();

        $oUser = new oxUser();
        $oUser->setId( "_testId_2" );
        $oUser->oxuser__oxusername = new oxField( "bbb@ccc.lt", oxField::T_RAW );
        $oUser->oxuser__oxshopid   = new oxField( 'xxx' );
        $oUser->save();

        $oU = new oxUser();

        modConfig::getInstance()->setConfigParam( 'blMallUsers', false );
        $this->assertEquals('_testId_1', $oU->getIdByUserName( 'aaa@bbb.lt' ) );
        $this->assertFalse($oU->getIdByUserName( 'bbb@ccc.lt' ) );

        modConfig::getInstance()->setConfigParam( 'blMallUsers', true );
        $this->assertEquals('_testId_1', $oU->getIdByUserName( 'aaa@bbb.lt' ) );
        $this->assertEquals('_testId_2', $oU->getIdByUserName( 'bbb@ccc.lt' ) );
    }


    public function testIsPriceViewModeNetto()
    {
        $oUser = new oxUser();

        $this->getConfig()->setConfigParam('blShowNetPrice', false);
        $this->assertFalse($oUser->isPriceViewModeNetto() );

        $this->getConfig()->setConfigParam('blShowNetPrice', true);
        $this->assertTrue($oUser->isPriceViewModeNetto() );
    }

    /**
     * Test configurable user credit rating (getBoni());
     * Config option for this: iCreditRating;
     */
    public function testUserCreditRating()
    {
        $oUser = new oxUser();
        $this->assertEquals( 1000, $oUser->getBoni() );

        $this->getConfig()->setConfigParam('iCreditRating', 100);
        $this->assertEquals( 100, $oUser->getBoni() );
    }


}
