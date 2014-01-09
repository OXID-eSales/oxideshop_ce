<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

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

        $this->assertEquals("Fname Lname, Street StreetNr Kaunas", $oSubj->toString());
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

        $this->assertEquals("Street StreetNr Kaunas", $oSubj->toString());

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

        $this->assertEquals("Lname, Street StreetNr Kaunas", $oSubj->toString());

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

        $this->assertEquals("Fname Lname, Street StreetNr Kaunas", $oSubj->toString());
    }

    /**
     * Test if address object string generator using magic getter was called.
     *
     * @return null
     */
    public function testToStringMagicMocked()
    {
        $oSubj = $this->getMock("oxaddress", array("toString"));
        $oSubj->expects( $this->once() )->method( 'toString' )->will($this->returnValue( "teststr" ));
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
        $oSubj->oxaddress__oxcompany   = new oxField('Company');
        $oSubj->oxaddress__oxfname     = new oxField('First name');
        $oSubj->oxaddress__oxlname     = new oxField('Last name');
        $oSubj->oxaddress__oxstreet    = new oxField('Street');
        $oSubj->oxaddress__oxstreetnr  = new oxField('Street number');
        $sEncoded = $oSubj->getEncodedDeliveryAddress();

        $oSubj->oxaddress__oxstreetnr  = new oxField('Street 41');

        $this->assertNotEquals( $sEncoded, $oSubj->getEncodedDeliveryAddress() );

        $oSubj->oxaddress__oxstreetnr  = new oxField('Street number');

        $this->assertEquals( $sEncoded, $oSubj->getEncodedDeliveryAddress() );
    }

    /**
     * Testing state getter
     *
     * @return null
     */
    public function testGetState()
    {
        $oSubj = new oxAddress();
        $oSubj->oxaddress__oxstateid = new oxField('TTT');
        $this->assertEquals('TTT', $oSubj->getState());
    }

    /**
     *
     */
    public function testSetSelected()
    {
        $oSubj = new oxAddress();
        $this->assertFalse( $oSubj->isSelected() );

        $oSubj->setSelected();
        $this->assertTrue( $oSubj->isSelected() );
    }
}