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
 * oxSepaValidator test class
 *
 * Can validate:
 *  - IBAN (International Business Account Number)
 *  - IBAN Registry (all IBAN lengths by country)
 *  - BIC (Bank International Code)
 */
class Unit_Core_oxSepaIBANValidatorTest extends OxidTestCase
{

    /**
     * Test case to check setting of IBAN code lengths with custom data
     */
    public function testGetCodeLengths()
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $aCodeLengths = array("DE" => 22);

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertEquals($aCodeLengths, $oSepaIBANValidator->getCodeLengths(), "IBAN code lengths must be set");
    }

    /**
     * Test case to check setting of IBAN code lengths with custom data
     */
    public function testIsValid_noCodeLengthsSetCorrectIBANGiven_false()
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $this->assertFalse($oSepaIBANValidator->isValid("MT84MALT011000012345MTLCAST001S"), "IBAN must be not valid");
    }

    /**
     * IBAN Registry data provider
     *
     * @return array
     */
    public function providerCodeLengths()
    {
        $sNotValidMsg = "IBAN code lengths must be not valid";
        $sValidMsg = "IBAN code lengths must be valid";

        return array(
            array(false, null, $sValidMsg),
            array(false, array("AL", "GR", 33, 21), $sNotValidMsg),
            array(false, array("GER" => 22), $sNotValidMsg),
            array(false, array("DE" => "twotwo"), $sNotValidMsg),
            array(false, array("de" => "22"), $sNotValidMsg),
            array(false, array("EN" => "2.2"), $sNotValidMsg),
            array(false, array("22" => "DE"), $sNotValidMsg),
            array(false, array(22 => "DE"), $sNotValidMsg),
            array(true, array("DE" => "22"), $sValidMsg),
            array(true, array("DE" => 22), $sValidMsg),
        );
    }


    /**
     * Test case to check IBAN code lengths validation
     *
     * @dataProvider providerCodeLengths
     */
    public function testValidateCodeLengths($blExpected, $aCodeLengths, $sMessage)
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $this->assertEquals($blExpected, $oSepaIBANValidator->isValidCodeLengths($aCodeLengths), $sMessage);
    }

    /**
     * Test case to check IBAN code lengths validation
     *
     * @dataProvider providerCodeLengths
     */
    public function testSetCodeLengths($blExpected, $aCodeLengths, $sMessage)
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $this->assertEquals($blExpected, $oSepaIBANValidator->setCodeLengths($aCodeLengths), $sMessage);
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValid_validIBAN_true()
    {
        return array(
            array("AL47212110090000000235698741", array('AL' => 28)),
            array("MT84MALT011000012345MTLCAST001S", array('MT' => 31)),
            array("NO9386011117947", array('NO' => 15)),
            array("NO9386011117947 ", array('NO' => 15)),
            array(" NO9386011117947", array('NO' => 15)),
        );
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValid_validIBAN_true
     */
    public function testIsValid_validIBAN_true($sIBAN, $aCodeLengths)
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertTrue($oSepaIBANValidator->isValid($sIBAN), "IBAN must be valid");
    }

    /**
     * IBAN validation data provider
     *
     * @return array
     */
    public function providerIsValid_invalidIBAN_false()
    {
        return array(
            array("_NO9386011117947", array('NO' => 15)),
            array("NX9386011117947", array('NX' => 15)),
            array("MT84MALT011000012345MTLCAST001S", array('MT' => 30)),
            array("MT84MALT011000012345MTLCAST001S", array('MT' => 32)),
            array("MT84MALT011000012345MTLCAST001S", array("DE" => 22)),
            array("MT84MALT011000012345MTLCAST001S", array("DE" => 31)),
            // Fix for bug entry 0005538: SEPA validator class IBAN validation issue
            array("1234567895", array('NO' => 15)),
        );
    }

    /**
     * Test case to check IBAN validation
     *
     * @dataProvider providerIsValid_invalidIBAN_false
     */
    public function testIsValid_invalidIBAN_false($sIBAN, $aCodeLengths)
    {
        $oSepaIBANValidator = new oxSepaIBANValidator();

        $oSepaIBANValidator->setCodeLengths($aCodeLengths);

        $this->assertFalse($oSepaIBANValidator->isValid($sIBAN), "IBAN must be not valid");
    }

    /**
     * @return array
     */
    protected function _getTestCodeLengths()
    {
        return array(
            'AL' => 28,
            'DE' => 22,
            'LT' => 20,
            'MT' => 31,
            'NO' => 15,
            'NX' => 15,
        );
    }

}