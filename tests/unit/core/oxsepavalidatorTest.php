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
        $this->markTestIncomplete('not implemented');
        $oSepaValidator = new oxSepaValidator();
        $oSepaValidator->setIBANRegistry();
    }

    /**
     * Test case to check IBAN registry validation
     */
    public function testValidateIBANRegistry()
    {
        $this->markTestIncomplete('not implemented');

        $oSepaValidator = new oxSepaValidator();
        $this->assertEquals( true, $oSepaValidator->validateIBANRegistry(), "Default IBAN registry must be valid");

        $oSepaValidator->setIBANRegistry();
        $this->assertEquals( true, $oSepaValidator->validateIBANRegistry(), "Current IBAN registry must be valid");
    }

    /**
     * Test case to check IBAN validation
     */
    public function testIsValidIBAN()
    {
        $this->markTestIncomplete('not implemented');
        $oSepaValidator = new oxSepaValidator();
    }

    /**
     * Test case to check BIC validation
     */
    public function testIsValidBIC()
    {
        $this->markTestIncomplete('not implemented');
        $oSepaValidator = new oxSepaValidator();
    }
}