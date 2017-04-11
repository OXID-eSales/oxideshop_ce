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

/**
 * Testing oxaddress class.
 */
class Unit_Core_oxAddressTest extends OxidTestCase
{

    /**
     * Test address object to string generator.
     *
     * @return null
     */
    public function testToString()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxfname = new oxField('Fname');
        $oSubj->oxaddress__oxlname = new oxField('Lname');
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('StreetNr');
        $oSubj->oxaddress__oxcity = new oxField('Kaunas');

        $this->assertEquals("Fname Lname, Street StreetNr, Kaunas", $oSubj->toString());
    }

    /**
     * Test address object without name to string generator.
     *
     * @return null
     */
    public function testToStringNoName()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('StreetNr');
        $oSubj->oxaddress__oxcity = new oxField('Kaunas');

        $this->assertEquals("Street StreetNr, Kaunas", $oSubj->toString());

    }

    /**
     * Test address object without first name to string generator.
     *
     * @return null
     */
    public function testToStringNoFirstName()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxlname = new oxField('Lname');
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('StreetNr');
        $oSubj->oxaddress__oxcity = new oxField('Kaunas');

        $this->assertEquals("Lname, Street StreetNr, Kaunas", $oSubj->toString());

    }

    /**
     * Test address object string generator using magic getter.
     *
     * @return null
     */
    public function testToStringMagic()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxfname = new oxField('Fname');
        $oSubj->oxaddress__oxlname = new oxField('Lname');
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('StreetNr');
        $oSubj->oxaddress__oxcity = new oxField('Kaunas');

        $this->assertEquals("Fname Lname, Street StreetNr, Kaunas", $oSubj->toString());
    }

    /**
     * Test if address object string generator using magic getter was called.
     *
     * @return null
     */
    public function testToStringMagicMocked()
    {
        $oSubj = $this->getMock("oxaddress", array("toString"));
        $oSubj->expects($this->once())->method('toString')->will($this->returnValue("teststr"));
        (string) $oSubj;
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
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxcompany = new oxField('Company');
        $oSubj->oxaddress__oxfname = new oxField('First name');
        $oSubj->oxaddress__oxlname = new oxField('Last name');
        $oSubj->oxaddress__oxstreet = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr = new oxField('Street number');
        $sEncoded = $oSubj->getEncodedDeliveryAddress();

        $oSubj->oxaddress__oxstreetnr = new oxField('Street 41');

        $this->assertNotEquals($sEncoded, $oSubj->getEncodedDeliveryAddress());

        $oSubj->oxaddress__oxstreetnr = new oxField('Street number');

        $this->assertEquals($sEncoded, $oSubj->getEncodedDeliveryAddress());
    }

    /**
     * Testing state ID getter
     */
    public function testGetStateId()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxstateid = new oxField('TTT');
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
        $oAddressMock = $this->getMock('oxAddress', array('_getStateObject', 'getStateId'));

        $oAddressMock->expects($this->any())
            ->method('_getStateObject')
            ->will($this->returnValue($oStateMock));

        $oAddressMock->expects($this->any())
            ->method('getStateId')
            ->will($this->returnValue($iAlternateStateId));

        $sExpected = oxDb::getDb()->getOne('SELECT oxtitle FROM oxstates WHERE oxid = "' . $iStateId . '"');
        $this->assertSame($sExpected, $oAddressMock->getStateTitle($iStateId), "State title is correct");

        $sExpected = oxDb::getDb()->getOne('SELECT oxtitle FROM oxstates WHERE oxid = "' . $iAlternateStateId . '"');
        $this->assertSame($sExpected, $oAddressMock->getStateTitle(), "State title is correct when ID is not passed");

        $this->setLanguage(1);

        $sExpected = oxDb::getDb()->getOne('SELECT oxtitle_1 FROM oxstates WHERE oxid = "' . $iStateId . '"');
        $this->assertSame($sExpected, $oAddressMock->getStateTitle($iStateId), "State title is correct");

        $sExpected = oxDb::getDb()->getOne(
            'SELECT oxtitle_1 FROM oxstates WHERE oxid = "' . $iAlternateStateId . '"'
        );
        $this->assertSame($sExpected, $oAddressMock->getStateTitle(), "State title is correct when ID is not passed");
    }

    /**
     *
     */
    public function testSetSelected()
    {
        $oSubj = new oxAddress();
        $this->assertFalse($oSubj->isSelected());

        $oSubj->setSelected();
        $this->assertTrue($oSubj->isSelected());
    }
}