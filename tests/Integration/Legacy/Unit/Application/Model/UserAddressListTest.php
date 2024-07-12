<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxRegistry;

class UserAddressListTest extends \PHPUnit\Framework\TestCase
{
    public $aList = [];

    public const AUSTRIA_ID = 'a7c40f6320aeb2ec2.72885259';

    public const GERMANY_ID = 'a7c40f631fc920687.20179984';

    private $_iAddressCounter = 0;

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxaddress');

        parent::tearDown();
    }

    /**
     * @return array
     */
    public function providerLoadActiveAddress(): \Iterator
    {
        yield [0, 'Österreich'];
        yield [1, 'Austria'];
    }

    /**
     * Tests if country name selected in correct language.
     *
     * Fix for bug entry 4960: Address country title is saved into user session and doesn't get updated, when user switches languages
     *
     * @param int    $iLanguageId
     * @param string $sCountryNameExpected
     *
     * @dataProvider providerLoadActiveAddress
     */
    public function testLoadCheckCountryNamePerLanguage($iLanguageId, $sCountryNameExpected)
    {
        $sUserId = 'oxdefaultadmin';
        oxRegistry::getLang()->setBaseLanguage($iLanguageId, self::AUSTRIA_ID);
        $sAddressId = $this->createAddress($sUserId, self::AUSTRIA_ID);

        $oAddressList = oxNew('oxUserAddressList');
        $oAddressList->load($sUserId);

        $this->assertCount(1, $oAddressList, 'User has one address - Austria.');
        $this->assertSame($sCountryNameExpected, $oAddressList[$sAddressId]->oxaddress__oxcountry->value, 'Country name is different in different language.');
    }

    /**
     * Check if address count match created.
     */
    public function testLoadCheckSeveralAddress()
    {
        $sUserId = 'oxdefaultadmin';
        $this->createAddress($sUserId, self::AUSTRIA_ID);
        $this->createAddress($sUserId, self::GERMANY_ID);

        $oAddressList = oxNew('oxUserAddressList');
        $oAddressList->load($sUserId);

        $this->assertCount(2, $oAddressList, 'User has two addresses - Austria and Germany.');
    }

    /**
     * Create address for given user.
     *
     * @param $sUserId
     * @param $sCountryId
     *
     * @return string
     */
    private function createAddress($sUserId, $sCountryId)
    {
        $sOXID = '__testAddress' . $this->_iAddressCounter;
        $this->_iAddressCounter++;

        $oSubj = oxNew('oxAddress');
        $oSubj->setId($sOXID);

        $oSubj->oxaddress__oxuserid = new oxField($sUserId);
        // Set country Austria as this country has different name in english and germany.
        $oSubj->oxaddress__oxcountryid = new oxField($sCountryId);
        $oSubj->oxaddress__oxfname = new oxField('Fname');
        $oSubj->oxaddress__oxlname = new oxField('Lname');
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('StreetNr');
        $oSubj->oxaddress__oxcity = new oxField('Kaunas');
        $oSubj->save();

        return $sOXID;
    }
}
