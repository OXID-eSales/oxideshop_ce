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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxUserAddressListTest extends OxidTestCase
{

    public $aList = array();

    const AUSTRIA_ID = 'a7c40f6320aeb2ec2.72885259';

    const GERMANY_ID = 'a7c40f631fc920687.20179984';

    private $_iAddressCounter = 0;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxaddress');

        parent::tearDown();
    }

    /**
     * @return array
     */
    public function providerLoadActiveAddress()
    {
        return array(
            array(0, 'Österreich'),
            array(1, 'Austria'),
        );
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
        $sAddressId = $this->_createAddress($sUserId, self::AUSTRIA_ID);

        $oAddressList = new oxUserAddressList;
        $oAddressList->load($sUserId);

        $this->assertSame(1, count($oAddressList), 'User has one address - Austria.');
        $this->assertSame($sCountryNameExpected, $oAddressList[$sAddressId]->oxaddress__oxcountry->value, 'Country name is different in different language.');
    }

    /**
     * Check if address count match created.
     */
    public function testLoadCheckSeveralAddress()
    {
        $sUserId = 'oxdefaultadmin';
        $sAustriaAddressId = $this->_createAddress($sUserId, self::AUSTRIA_ID);
        $sGermanyAddressId = $this->_createAddress($sUserId, self::GERMANY_ID);

        $oAddressList = new oxUserAddressList;
        $oAddressList->load($sUserId);

        $this->assertSame(2, count($oAddressList), 'User has two addresses - Austria and Germany.');
    }

    /**
     * Create address for given user.
     *
     * @param $sUserId
     * @param $sCountryId
     *
     * @return string
     */
    private function _createAddress($sUserId, $sCountryId)
    {
        $sOXID = '__testAddress' . $this->_iAddressCounter;
        $this->_iAddressCounter++;

        $oSubj = new oxAddress();
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
