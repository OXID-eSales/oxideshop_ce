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
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

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
            array( true , null,                      $sValidMsg    ),
            array( false, array("AL", "GR", 33, 21), $sNotValidMsg ),
            array( false, array("GER" => 22       ), $sNotValidMsg ),
            array( false, array("DE" => "twotwo"  ), $sNotValidMsg ),
            array( false, array("de" => "22"      ), $sNotValidMsg ),
            array( false, array("EN" => "2.2"     ), $sNotValidMsg ),
            array( true , array("DE" => "22"      ), $sValidMsg    ),
        );
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function _dpIBANValidation()
    {
        $sNotValidMsg = "IBAN must be not valid";
        $sValidMsg    = "IBAN must be valid";

        return array(
            array( true,  "AL47212110090000000235698741"    , $sValidMsg    ),
            array( true,  "MT84MALT011000012345MTLCAST001S" , $sValidMsg    ),
            array( true,  "NO9386011117947"                 , $sValidMsg    ),
            array( false, "NX9386011117947"                 , $sNotValidMsg ),
            // Fix for bug entry 0005538: SEPA validator class IBAN validation issue
            array( false, "1234567895"                      , $sNotValidMsg ),
        );
    }

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function _dpBICValidation()
    {
        $sNotValidMsg = "IBAN must be not valid";
        $sValidMsg    = "IBAN must be valid";

        return array(
            array( true,  "ASPKAT2L", $sValidMsg    ),
            array( false, "123ABCDE", $sNotValidMsg ),
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

        $aIBANRegistry = array("DE" => 22);

        $this->assertEquals( true,           $oSepaValidator->setIBANRegistry($aIBANRegistry), "IBAN registry must be set" );

        $this->assertEquals( $aIBANRegistry, $oSepaValidator->getIBANRegistry(),               "IBAN registry must be set" );
    }

    /**
     * Test case to check IBAN registry validation
     *
     * @dataProvider _dpIBANRegistry
     */
    public function testValidateIBANRegistry($blExpected, $aIBANRegistry, $sMessage)
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertEquals( $blExpected, $oSepaValidator->isValidIBANRegistry($aIBANRegistry), $sMessage);
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider _dpIBANValidation
     */
    public function testIsValidIBAN($blExpected, $sIBAN, $sMessage)
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertEquals( $blExpected, $oSepaValidator->isValidIBAN($sIBAN), $sMessage );
    }

    /**
     * Test case to check BIC validation
     *
     * @dataProvider _dpBICValidation
     */
    public function testIsValidBIC($blExpected, $sBIC, $sMessage)
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertEquals( $blExpected, $oSepaValidator->isValidBIC($sBIC), $sMessage );
    }
}
