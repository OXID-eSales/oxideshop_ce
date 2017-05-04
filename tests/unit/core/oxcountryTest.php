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

class Unit_Core_oxCountryTest extends OxidTestCase
{

    public $oObj = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $oObj = new oxbase();
        $oObj->init('oxcountry');
        $oObj->oxcountry__oxtitle = new oxField('oxCountryTestDE', oxField::T_RAW);
        $oObj->oxcountry__oxtitle_1 = new oxField('oxCountryTestENG', oxField::T_RAW);
        $oObj->save();

        $this->oObj = new oxCountry();
        $this->oObj->load($oObj->getId());
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->oObj->delete();
        parent::tearDown();
    }

    /**
     *  Test loading country
     */
    // for default lang
    public function testLoadingCountryDefLanguage()
    {
        $oObj = new oxCountry();
        $oObj->load($this->oObj->getId());
        $this->assertEquals('oxCountryTestDE', $oObj->oxcountry__oxtitle->value);
    }

    // for second language
    public function testLoadingCountrySecondLanguage()
    {
        $oObj = new oxCountry();
        //$this->getConfig()->setLanguage( 1 );
        $oObj->loadInLang(1, $this->oObj->getId());
        $this->assertEquals('oxCountryTestENG', $oObj->oxcountry__oxtitle->value);
    }

    /**
     *  Test loading not existing country
     */
    public function testLoadingNotExistingCountry()
    {
        $oObj = oxNew("oxcountry");
        $this->assertFalse($oObj->load('noSuchCountry'));
    }

    public function testIsForeignCountry()
    {
        $oObj = new oxCountry();
        $aHome = $this->getConfig()->getConfigParam('aHomeCountry');
        $oObj->setId($aHome[0]);
        $this->assertFalse($oObj->isForeignCountry());

        $oObj->setId('country');
        $this->assertTrue($oObj->isForeignCountry());
    }

    public function testisInEU()
    {
        $oObj = new oxCountry();
        $oObj->setId('test');
        $oObj->oxcountry__oxvatstatus = new oxField(1, oxField::T_RAW);
        $this->assertTrue($oObj->isInEU());

        $oObj->oxcountry__oxvatstatus = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oObj->isInEU());
    }


    /**
     * Tests state getter returned count
     *
     * @return null;
     */
    public function testGetStatesNumber()
    {
        $oSubj = new oxCountry();
        $oSubj->load('8f241f11095649d18.02676059');
        $aStates = $oSubj->getStates();
        $this->assertEquals(13, count($aStates));
    }

    /**
     * Tests state getter returned ordered list
     *
     * @return null;
     */
    public function testGetStatesIsOrdered()
    {
        $oSubj = new oxCountry();
        $oSubj->load('8f241f11096877ac0.98748826');
        $aStates = $oSubj->getStates();
        $aKeys = $aStates->arrayKeys();
        $this->assertEquals('AL', $aKeys[0]);
        $this->assertEquals('AA', $aKeys[6]);
        $this->assertEquals('WY', $aKeys[61]);
    }


    /**
     * Tests state getter
     *
     * @return null;
     */
    public function testGetStates()
    {
        $oSubj = new oxCountry();
        $oSubj->load('8f241f11095649d18.02676059');
        $aStates = $oSubj->getStates();
        $this->assertEquals('Manitoba', $aStates['MB']->oxstates__oxtitle->value);
    }

    /**
     * Tests state getter
     *
     * @return null;
     */
    public function testGetIdByCode()
    {
        $oSubj = new oxCountry();
        $this->assertEquals('a7c40f631fc920687.20179984', $oSubj->getIdByCode('DE'));
    }

    public function providerGetVatIdentificationNumberPrefix()
    {
        return array(
            array('a7c40f631fc920687.20179984', 'DE'),
            // Exceptional country
            array('a7c40f633114e8fc6.25257477', 'EL'),
        );
    }

    /**
     * Checks if returned vat identification number was returned correctly.
     *
     * @param $sCountryId
     * @param $sPrefix
     *
     * @dataProvider providerGetVatIdentificationNumberPrefix
     */
    public function testGetVatIdentificationNumberPrefix($sCountryId, $sPrefix)
    {
        $oCountry = new oxCountry();
        $oCountry->load($sCountryId);

        $this->assertEquals($sPrefix, $oCountry->getVATIdentificationNumberPrefix());
    }
}
