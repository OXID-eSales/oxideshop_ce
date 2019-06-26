<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxNewsSubscribedHelper.php';

class NewssubscribedTest extends \OxidTestCase
{
    private $_oNewsSub = null;
    private $_oUser = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oNewsSub = oxNew("oxnewssubscribed");
        $this->_oNewsSub->setId('_testNewsSubscrId');
        $this->_oNewsSub->oxnewssubscribed__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxemail = new oxField('useremail@useremail.nl', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxdboptin = new oxField('1', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxunsubscribed = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $this->_oNewsSub->save();

        //set default user
        $this->_oUser = oxNew("oxuser");
        $this->_oUser->setId('_testUserId');
        $this->_oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oUser->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $this->_oUser->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $this->_oUser->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);
        $this->_oUser->oxuser__oxshopid = new oxField('1', oxField::T_RAW);
        $this->_oUser->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oNewsSub->delete('_testNewsSubscrId');
        $this->_oNewsSub->delete('_testNewsSubscr2Id');
        $this->_oUser->delete();

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxnewssubscribed');

        parent::tearDown();
    }

    /**
     * Test object loading
     */
    public function testLoad()
    {
        $oNewsSubscribed = oxNew('oxNewsSubscribed');
        $this->assertTrue($oNewsSubscribed->load('_testNewsSubscrId'));
    }

    /**
     * Test if load method sets parameter blWasSubscribed value
     * when user already registered
     */
    public function testLoadSetsWasSubscribed()
    {
        $oNewsSubscribed = oxNew('oxNewsSubscribedHelper');
        $oNewsSubscribed->load('_testNewsSubscrId');
        $this->assertTrue($oNewsSubscribed->getWasSubscribed());
    }

    /**
     * Testing email subscription loader by user email with existing user.
     */
    public function test_loadFromEmailExistingUser()
    {
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $this->assertTrue($oNewsSubscribed->loadFromEmail('useremail@useremail.nl'));
        $this->assertEquals('_testNewsSubscrId', $oNewsSubscribed->oxnewssubscribed__oxid->value);
    }

    /**
     * Testing email subscription loader by user email with not existing user.
     */
    public function testLoadFromEmailNotExistingUser()
    {
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $this->assertFalse($oNewsSubscribed->loadFromEmail('nosuchuser@useremail.nl'));
        $this->assertNull($oNewsSubscribed->oxnewssubscribed__oxid->value);
    }

    /**
     * Testing email subscription loader by user id with existing user
     */
    public function testLoadFromUserIdExistingUser()
    {
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $this->assertTrue($oNewsSubscribed->loadFromUserId('_testUserId'));
        $this->assertEquals('_testNewsSubscrId', $oNewsSubscribed->oxnewssubscribed__oxid->value);
    }

    /**
     * Testing email subscription loader by user id with not existing user
     */
    public function testLoadFromUserIdNotExistingUser()
    {
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $this->assertFalse($oNewsSubscribed->loadFromUserId('noSuchUserId'));
        $this->assertNull($oNewsSubscribed->oxnewssubscribed__oxid->value);
    }

    /**
     * Testing if insert updates timestamp field value
     */
    public function testInsert()
    {
        $sNow = date('Y-m-d H:i:s');
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->setId('_testNewsSubscr2Id');
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField('_testUser2Id', oxField::T_RAW);
        $oNewsSubscribed->save();

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscr2Id');
        $this->assertTrue(($oNewsSubscribed->oxnewssubscribed__oxsubscribed->value >= $sNow));
    }

    /**
     * Testing update. If user was subscribed already and unsubscribing him
     * (oxdboptin = 0), set unsubscribe date
     */
    public function testUpdateWasSubscribed()
    {
        $sNow = date('Y-m-d H:i:s');

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField(0, oxField::T_RAW);
        $oNewsSubscribed->save();

        // reloading and testing
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertTrue($oNewsSubscribed->oxnewssubscribed__oxunsubscribed->value >= $sNow);
    }

    /**
     * Testing update. If user was not subscribed, unsubscribe date shoud not change
     */
    public function testUpdateWasUnsubscribed()
    {
        $oNewsSubscribed = oxNew('oxNewsSubscribedHelper');
        $oNewsSubscribed->load('_testNewsSubscrId');
        $oNewsSubscribed->setWasSubscribed(false);

        $sUnsubscribeDate = $oNewsSubscribed->oxnewssubscribed__oxunsubscribed->value;
        $oNewsSubscribed->UNITupdate();

        // reloading and testing
        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertEquals($sUnsubscribeDate, $oNewsSubscribed->oxnewssubscribed__oxunsubscribed->value);
    }

    /**
     * Testing db-opt-in status getter
     */
    public function testGetOptInStatus()
    {
        $this->_oNewsSub->oxnewssubscribed__oxdboptin = new oxField('xxx', oxField::T_RAW);
        $this->assertEquals('xxx', $this->_oNewsSub->getOptInStatus());
    }

    /**
     * Testing registration db-opt-in status setter
     */
    public function testSetOptInStatus()
    {
        $this->_oNewsSub->setOptInStatus('9'); //oxnewssubscribed__oxdboptin = new oxField('9', oxField::T_RAW);

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertEquals(9, $oNewsSubscribed->oxnewssubscribed__oxdboptin->value);
    }


    /**
     * Testing subscription email sending status getter
     */
    public function testGetOptInEmailStatus()
    {
        $this->_oNewsSub->oxnewssubscribed__oxemailfailed = new oxField('xxx', oxField::T_RAW);
        $this->assertEquals('xxx', $this->_oNewsSub->getOptInEmailStatus());
    }


    /**
     * Testing subscription email sending status setter
     */
    public function testSetOptInEmailStatus()
    {
        $this->_oNewsSub->setOptInEmailStatus(1);


        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertEquals(1, $oNewsSubscribed->oxnewssubscribed__oxemailfailed->value);
    }


    /**
     * Testing subscription email updater
     */
    public function testUpdateSubscription()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxsal = new oxField('newusersal', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('newuserfname', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('newuserlname', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('newuseremail@useremail.nl', oxField::T_RAW);

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');
        $this->assertTrue($oNewsSubscribed->updateSubscription($oUser));

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertEquals('newusersal', $oNewsSubscribed->oxnewssubscribed__oxsal->value);
        $this->assertEquals('newuserfname', $oNewsSubscribed->oxnewssubscribed__oxfname->value);
        $this->assertEquals('newuserlname', $oNewsSubscribed->oxnewssubscribed__oxlname->value);
        $this->assertEquals('newuseremail@useremail.nl', $oNewsSubscribed->oxnewssubscribed__oxemail->value);
    }

    /**
     * Testing subscription email updater with empty email param
     * Email should not change
     */
    public function testUpdateSubscriptionWhenEmailParamIsEmpty()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxsal = new oxField('newusersal', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('newuserfname', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('newuserlname', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('', oxField::T_RAW);

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');
        $this->assertTrue($oNewsSubscribed->updateSubscription($oUser));

        $oNewsSubscribed = oxNew('oxnewssubscribed');
        $oNewsSubscribed->load('_testNewsSubscrId');

        $this->assertEquals('newusersal', $oNewsSubscribed->oxnewssubscribed__oxsal->value);
        $this->assertEquals('newuserfname', $oNewsSubscribed->oxnewssubscribed__oxfname->value);
        $this->assertEquals('newuserlname', $oNewsSubscribed->oxnewssubscribed__oxlname->value);
        $this->assertEquals('useremail@useremail.nl', $oNewsSubscribed->oxnewssubscribed__oxemail->value);
    }

    /**
     * Check if return right result after subscribe and unsubscribe.
     */
    public function testWasUnsubscribed()
    {
        $oUser = $this->_oUser;
        $oNewsSubscribed = $this->_oNewsSub;

        $this->assertEquals($oNewsSubscribed->wasUnsubscribed(), false);
        $oUser->setNewsSubscription(false, false);
        $oNewsSubscribed->load('_testNewsSubscrId');
        $this->assertEquals($oNewsSubscribed->wasUnsubscribed(), true);
    }

    /**
     * Check if return value is valid after subscribe and unsubscribe.
     */
    public function testWasUnsubscribedBug5416()
    {
        $oNewsSubscribed = oxNew('oxnewssubscribed');

        //not subscribed
        $oNewsSubscribed->oxnewssubscribed__oxunsubscribed = new oxField('0000-00-00 00:00:00', oxField::T_TEXT);
        $this->assertEquals($oNewsSubscribed->wasUnsubscribed(), false);

        //subscribed
        $oNewsSubscribed->oxnewssubscribed__oxunsubscribed = new oxField('0000-00-00 00:00:01', oxField::T_TEXT);
        $this->assertEquals($oNewsSubscribed->wasUnsubscribed(), true);
    }
}
