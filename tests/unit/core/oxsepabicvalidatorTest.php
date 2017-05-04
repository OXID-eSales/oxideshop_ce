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
class Unit_Core_oxSepaBICValidatorTest extends OxidTestCase
{

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_validBIC_true()
    {
        return array(
            array("ASPKAT2L"),
            array("AAAACCXX"),
            array("AAAACC22"),
            array("AAAACCXXHHH"),
            array("AAAACC33555"),
            array("AAAACCXX555"),
            array(" AAAACCXX"),
            array("AAAACCXX "),
            array("\tAAAACCXX"),
            array("AAAACCXX\n"),
            array("AAAACCXX\n\r"),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array("COBADEHD055"),
        );
    }


    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_validBIC_true
     */
    public function testIsValid_validBIC_true($sBIC)
    {
        $oSepaBICValidator = new oxSepaBICValidator();

        $this->assertTrue($oSepaBICValidator->isValid($sBIC), "BIC must be valid");
    }

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_invalidBIC_false()
    {
        return array(
            array("AAAACCX"),
            array("AAAACCXXX"),
            array("AAAACCXXXX"),
            array("AAAACC2233"),
            array("AAAACC2233*"),
            array("AAAACC224444X"),
            array("AAAACC224444XX"),
            array("AAA1CC22"),
            array("1AAAACXX"),
            array("A1AAACXX"),
            array("AA1AACXX"),
            array("AAA1ACXX"),
            array("AAAA1CXX"),
            array("AAAAC1XX"),
            array("AAAAC122"),
            array("ASPK AT 2L"),
            array("ASPK\tAT\t2L"),
            array("123 ASPKAT2L"),
            array("_ASPKAT2L"),
            array("ASPKAT2"),
            array("ASP_AT2L"),
            array("ASPK*T2L"),
            array("ASPKA-2L"),
            array("AAAßCCXX"),
            array("AAAACßXX"),
            array("AAAACCXö"),
            // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
            array("123COBADEHD055ABC"),
        );
    }

    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_invalidBIC_false
     */
    public function testIsValid_invalidBIC_false($sBIC)
    {
        $oSepaBICValidator = new oxSepaBICValidator();

        $this->assertFalse($oSepaBICValidator->isValid($sBIC), "BIC must be not valid");
    }
}