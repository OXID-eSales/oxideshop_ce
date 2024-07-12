<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxCompanyVatIn;

class CompanyVatInTest extends \OxidTestCase
{
    public function testConstruct()
    {
        $oVatIn = new oxCompanyVatIn('LT12345');
        $this->assertSame('LT12345', (string) $oVatIn);
    }

    public function testConstruct_empty()
    {
        $oVatIn = new oxCompanyVatIn('');
        $this->assertSame('', (string) $oVatIn);
    }

    /**
     * @dataProvider vatInProviderForCountryCode
     */
    public function testGetVatInCountryCode($sVatIn, $sExpectCode)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $this->assertSame($sExpectCode, $oVatIn->getCountryCode());
    }

    public function vatInProviderForCountryCode()
    {
        return [['LT12345', 'LT'], [' LT12345', 'LT'], ['lt12345', 'LT'], ['LT 12345', 'LT'], ['LT-12 345', 'LT'], ['LT.123.45', 'LT'], ['LT,123,45', 'LT'], ['', ''], [null, ''], [1, '1'], ['1111', '11'], ['abcd', 'AB']];
    }

    /**
     * @dataProvider vatInProviderForNumbers
     */
    public function testGetVatInNumbers($sVatIn, $sExpectCode)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $this->assertSame($sExpectCode, $oVatIn->getNumbers());
    }

    public function vatInProviderForNumbers()
    {
        return [['LT12345', '12345'], [' LT12345', '12345'], ['LT12345 ', '12345'], [' LT12345 ', '12345'], ['', ''], ['1111', '11'], ['abcd', 'cd'], ['LT 12345', '12345'], ['LT-12 345', '12345'], ['LT.123.45', '.123.45'], ['LT,123,45', ',123,45']];
    }
}
