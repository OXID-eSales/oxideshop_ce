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
class Unit_Core_oxSepaValidatorTest extends OxidTestCase
{

    /**
     * Test case to check getting IBAN registry records
     */
    public function testGetIBANRegistry()
    {
        $oSepaValidator = new oxSepaValidator();

        $aIBANRegistry = $oSepaValidator->getIBANCodeLengths();

        $this->assertNotNull($aIBANRegistry['DE'], "IBAN length for SEPA country (DE) must be not null");
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBAN_true()
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertTrue($oSepaValidator->isValidIBAN("MT84MALT011000012345MTLCAST001S"), "IBAN must be valid");
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_invalidIBAN_false()
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertFalse($oSepaValidator->isValidIBAN("NX9386011117947"), "IBAN must be not valid");
    }

    /**
     * Test case to check BIC validation
     *
     */
    public function testIsValidBIC_validBIC_true()
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertTrue($oSepaValidator->isValidBIC("ASPKAT2L"), "BIC must be valid");
    }

    /**
     * Test case to check BIC validation
     *
     */
    public function testIsValidBIC_invalidBIC_false()
    {
        $oSepaValidator = new oxSepaValidator();

        $this->assertFalse($oSepaValidator->isValidBIC("AAAACCX"), "BIC must be not valid");
    }
}