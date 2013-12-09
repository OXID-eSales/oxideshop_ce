\<?php
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
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id
 */

require_once realpath( "." ) . '/unit/OxidTestCase.php';
require_once realpath( "." ) . '/unit/test_config.inc.php';

/**
 * oxSepaValidator test class
 *
 * Can validate:
 *  - IBAN (International Business Account Number)
 *  - IBAN Registry (all IBAN lengths by country)
 *  - BIC (Bank International Code)
 */
class Unit_Core_oxSepaValidatorTest extends OxidTestCase
{

    /**
     * IBAN Registry data provider
     *
     * @return array
     */
    public function _dpIBANRegistry()
    {
        $sNotValidMsg = "IBAN registry must be not valid";
        $sValidMsg    = "IBAN registry must be valid";

        return array(
            array( true, null, $sValidMsg ),
            array( false, array( "AL", "GR", 33, 21 ), $sNotValidMsg ),
            array( false, array( "GER" => 22 ), $sNotValidMsg ),
            array( false, array( "DE" => "twotwo" ), $sNotValidMsg ),
            array( false, array( "de" => "22" ), $sNotValidMsg ),
            array( false, array( "EN" => "2.2" ), $sNotValidMsg ),
            array( true, array( "DE" => "22" ), $sValidMsg ),
        );
    }

    /**
     * Test case to check getting IBAN registry records
     */
    public function testGetIBANRegistry()
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = $oSepaValidator->getIBANRegistry();

        $this->assertNotNull( $aIBANRegistry['DE'], "IBAN length for SEPA country (DE) must be not null" );
    }

    /**
     * Test case to check setting of IBAN registry with custom data
     */
    public function testSetIBANRegistry()
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = array( "DE" => 22 );

        $this->assertEquals( true, $oSepaValidator->setIBANRegistry( $aIBANRegistry ), "IBAN registry must be set" );

        $this->assertEquals( $aIBANRegistry, $oSepaValidator->getIBANRegistry(), "IBAN registry must be set" );
    }

    /**
     * Test case to check IBAN registry validation
     *
     * @dataProvider _dpIBANRegistry
     */
    public function testValidateIBANRegistry( $blExpected, $aIBANRegistry, $sMessage )
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertEquals( $blExpected, $oSepaValidator->isValidIBANRegistry( $aIBANRegistry ), $sMessage );
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBANFromNonexistentCountry_false( )
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = array( "DE" => 22 );
        $oSepaValidator->setIBANRegistry( $aIBANRegistry );

        $this->assertFalse( $oSepaValidator->isValidIBAN( "MT84MALT011000012345MTLCAST001S" ), "IBAN must be not valid" );
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBANFromExistentCountryIBANTooLong_false( )
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = array( "MT" => 30 );
        $oSepaValidator->setIBANRegistry( $aIBANRegistry );

        $this->assertFalse( $oSepaValidator->isValidIBAN( "MT84MALT011000012345MTLCAST001S" ), "IBAN must be not valid" );
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBANFromExistentCountryIBANTooShort_false( )
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = array( "MT" => 32 );
        $oSepaValidator->setIBANRegistry( $aIBANRegistry );

        $this->assertFalse( $oSepaValidator->isValidIBAN( "MT84MALT011000012345MTLCAST001S" ), "IBAN must be not valid" );
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBANFromExistentCountry_true( )
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = array( "MT" => 31 );
        $oSepaValidator->setIBANRegistry( $aIBANRegistry );

        $this->assertTrue( $oSepaValidator->isValidIBAN( "MT84MALT011000012345MTLCAST001S" ), "IBAN must be valid" );
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValidIBAN_validIBAN_true()
    {
        return array(
            array( "AL47212110090000000235698741" ),
            array( "MT84MALT011000012345MTLCAST001S" ),
            array( "NO9386011117947" ),
            array( "NO9386011117947 " ),
            array( " NO9386011117947" ),
        );
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValidIBAN_validIBAN_true
     */
    public function testIsValidIBAN_validIBAN_true( $sIBAN )
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertTrue( $oSepaValidator->isValidIBAN( $sIBAN ), "IBAN must be valid" );
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValidIBAN_invalidIBAN_false()
    {
        return array(
            array( "_NO9386011117947" ),
            array( "NX9386011117947" ),
            // Fix for bug entry 0005538: SEPA validator class IBAN validation issue
            array( "1234567895" ),
        );
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValidIBAN_invalidIBAN_false
     */
    public function testIsValidIBAN_invalidIBAN_false( $sIBAN )
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertFalse( $oSepaValidator->isValidIBAN( $sIBAN ), "IBAN must be not valid" );
    }


    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValidBIC_validBIC_true()
    {
        return array(
            array( "ASPKAT2L" ),
            array( "AAAACCXX" ),
            array( "AAAACC22" ),
            array( "AAAACCXXHHH" ),
            array( "AAAACC33555" ),
            array( "AAAACCXX555" ),
            array( " AAAACCXX" ),
            array( "AAAACCXX " ),
            array( "\tAAAACCXX" ),
            array( "AAAACCXX\n" ),
            array( "AAAACCXX\n\r" ),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array( "COBADEHD055" ),
        );
    }


    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValidBIC_validBIC_true
     */
    public function testIsValidBIC_validBIC_true( $sBIC )
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertTrue( $oSepaValidator->isValidBIC( $sBIC ), "BIC must be valid" );
    }

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValidBIC_invalidBIC_false()
    {
        return array(
            array( "AAAACCX" ),
            array( "AAAACCXXX" ),
            array( "AAAACCXXXX" ),
            array( "AAAACC2233" ),
            array( "AAAACC2233*" ),
            array( "AAAACC224444X" ),
            array( "AAAACC224444XX" ),
            array( "AAA1CC22" ),
            array( "1AAAACXX" ),
            array( "A1AAACXX" ),
            array( "AA1AACXX" ),
            array( "AAA1ACXX" ),
            array( "AAAA1CXX" ),
            array( "AAAAC1XX" ),
            array( "AAAAC122" ),
            array( "ASPK AT 2L" ),
            array( "ASPK\tAT\t2L" ),
            array( "123 ASPKAT2L" ),
            array( "_ASPKAT2L" ),
            array( "ASPKAT2" ),
            array( "ASP_AT2L" ),
            array( "ASPK*T2L" ),
            array( "ASPKA-2L" ),
            array( "AAAßCCXX" ),
            array( "AAAACßXX" ),
            array( "AAAACCXö" ),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array( "123COBADEHD055ABC" ),
        );
    }

    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValidBIC_invalidBIC_false
     */
    public function testIsValidBIC_invalidBIC_false( $sBIC )
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertFalse( $oSepaValidator->isValidBIC( $sBIC ), "BIC must be not valid" );
    }
}