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
class SepaBICValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_validBIC_true(): \Iterator
    {
        yield ["ASPKAT2L"];
        yield ["AAAACCXX"];
        yield ["AAAACC22"];
        yield ["AAAACCXXHHH"];
        yield ["AAAACC33555"];
        yield ["AAAACCXX555"];
        yield [" AAAACCXX"];
        yield ["AAAACCXX "];
        yield ["\tAAAACCXX"];
        yield ["AAAACCXX\n"];
        yield ["AAAACCXX\n\r"];
        // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
        yield ["COBADEHD055"];
    }


    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_validBIC_true
     */
    public function testIsValid_validBIC_true($sBIC)
    {
        $oSepaBICValidator = oxNew('oxSepaBICValidator');

        $this->assertTrue($oSepaBICValidator->isValid($sBIC), "BIC must be valid");
    }

    /**
     * BIC validation data provider
     *
     * @return array
     */
    public function providerIsValid_invalidBIC_false(): \Iterator
    {
        yield ["AAAACCX"];
        yield ["AAAACCXXX"];
        yield ["AAAACCXXXX"];
        yield ["AAAACC2233"];
        yield ["AAAACC2233*"];
        yield ["AAAACC224444X"];
        yield ["AAAACC224444XX"];
        yield ["AAA1CC22"];
        yield ["1AAAACXX"];
        yield ["A1AAACXX"];
        yield ["AA1AACXX"];
        yield ["AAA1ACXX"];
        yield ["AAAA1CXX"];
        yield ["AAAAC1XX"];
        yield ["AAAAC122"];
        yield ["ASPK AT 2L"];
        yield ["ASPK\tAT\t2L"];
        yield ["123 ASPKAT2L"];
        yield ["_ASPKAT2L"];
        yield ["ASPKAT2"];
        yield ["ASP_AT2L"];
        yield ["ASPK*T2L"];
        yield ["ASPKA-2L"];
        yield ["AAAßCCXX"];
        yield ["AAAACßXX"];
        yield ["AAAACCXö"];
        // Fix for bug entry 0005564: oxSepaValidator::isValidBIC($sBIC) only verifies substring of BIC
        yield ["123COBADEHD055ABC"];
    }

    /**
     * Test case to check BIC validation
     *
     * @dataProvider providerIsValid_invalidBIC_false
     */
    public function testIsValid_invalidBIC_false($sBIC)
    {
        $oSepaBICValidator = oxNew('oxSepaBICValidator');

        $this->assertFalse($oSepaBICValidator->isValid($sBIC), "BIC must be not valid");
    }
}
