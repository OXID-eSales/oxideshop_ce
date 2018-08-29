<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * oxSepaValidator test class
 *
 * Can validate:
 *  - IBAN (International Business Account Number)
 *  - IBAN Registry (all IBAN lengths by country)
 *  - BIC (Bank International Code)
 */
class SepaValidatorTest extends \OxidTestCase
{

    /**
     * Test case to check getting IBAN registry records
     */
    public function testGetIBANRegistry()
    {
        $oSepaValidator = oxNew('oxSepaValidator');

        $aIBANRegistry = $oSepaValidator->getIBANCodeLengths();

        $this->assertNotNull($aIBANRegistry['DE'], "IBAN length for SEPA country (DE) must be not null");
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_validIBAN_true()
    {
        $oSepaValidator = oxNew('oxSepaValidator');

        $this->assertTrue($oSepaValidator->isValidIBAN("MT84MALT011000012345MTLCAST001S"), "IBAN must be valid");
    }

    /**
     * Test case to check IBAN validation
     *
     */
    public function testIsValidIBAN_invalidIBAN_false()
    {
        $oSepaValidator = oxNew('oxSepaValidator');

        $this->assertFalse($oSepaValidator->isValidIBAN("NX9386011117947"), "IBAN must be not valid");
    }

    /**
     * Test case to check BIC validation
     *
     */
    public function testIsValidBIC_validBIC_true()
    {
        $oSepaValidator = oxNew('oxSepaValidator');

        $this->assertTrue($oSepaValidator->isValidBIC("ASPKAT2L"), "BIC must be valid");
    }

    /**
     * Test case to check BIC validation
     *
     */
    public function testIsValidBIC_invalidBIC_false()
    {
        $oSepaValidator = oxNew('oxSepaValidator');

        $this->assertFalse($oSepaValidator->isValidBIC("AAAACCX"), "BIC must be not valid");
    }
}
