<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;

class CountryTest extends \PHPUnit\Framework\TestCase
{
    public $oObj;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $oObj = oxNew('oxbase');
        $oObj->init('oxcountry');

        $oObj->oxcountry__oxtitle = new oxField('oxCountryTestDE', oxField::T_RAW);
        $oObj->oxcountry__oxtitle_1 = new oxField('oxCountryTestENG', oxField::T_RAW);
        $oObj->save();

        $this->oObj = oxNew('oxCountry');
        $this->oObj->load($oObj->getId());
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
        $oObj = oxNew('oxCountry');
        $oObj->load($this->oObj->getId());
        $this->assertSame('oxCountryTestDE', $oObj->oxcountry__oxtitle->value);
    }

    // for second language
    public function testLoadingCountrySecondLanguage()
    {
        $oObj = oxNew('oxCountry');
        //$this->getConfig()->setLanguage( 1 );
        $oObj->loadInLang(1, $this->oObj->getId());
        $this->assertSame('oxCountryTestENG', $oObj->oxcountry__oxtitle->value);
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
        $oObj = oxNew('oxCountry');
        $aHome = $this->getConfig()->getConfigParam('aHomeCountry');
        $oObj->setId($aHome[0]);
        $this->assertFalse($oObj->isForeignCountry());

        $oObj->setId('country');
        $this->assertTrue($oObj->isForeignCountry());
    }

    public function testisInEU()
    {
        $oObj = oxNew('oxCountry');
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
        $oSubj = oxNew('oxCountry');
        $oSubj->load('8f241f11095649d18.02676059');

        $aStates = $oSubj->getStates();
        $this->assertCount(13, $aStates);
    }

    /**
     * Tests state getter returned ordered list
     *
     * @return null;
     */
    public function testGetStatesIsOrdered()
    {
        $oSubj = oxNew('oxCountry');
        $oSubj->load('8f241f11096877ac0.98748826');

        $aStates = $oSubj->getStates();
        $aKeys = $aStates->arrayKeys();
        $this->assertSame('AL', $aKeys[0]);
        $this->assertSame('AA', $aKeys[6]);
        $this->assertSame('WY', $aKeys[61]);
    }


    /**
     * Tests state getter
     *
     * @return null;
     */
    public function testGetStates()
    {
        $oSubj = oxNew('oxCountry');
        $oSubj->load('8f241f11095649d18.02676059');

        $aStates = $oSubj->getStates();
        $this->assertSame('Manitoba', $aStates['MB']->oxstates__oxtitle->value);
    }

    /**
     * Tests state getter
     *
     * @return null;
     */
    public function testGetIdByCode()
    {
        $oSubj = oxNew('oxCountry');
        $this->assertSame('a7c40f631fc920687.20179984', $oSubj->getIdByCode('DE'));
    }

    public function providerGetVatIdentificationNumberPrefix(): \Iterator
    {
        yield ['a7c40f631fc920687.20179984', 'DE'];
        // Exceptional country
        yield ['a7c40f633114e8fc6.25257477', 'EL'];
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
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sCountryId);

        $this->assertEquals($sPrefix, $oCountry->getVATIdentificationNumberPrefix());
    }
}
