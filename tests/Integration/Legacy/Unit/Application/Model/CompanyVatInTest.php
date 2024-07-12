<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxCompanyVatIn;

class CompanyVatInTest extends \PHPUnit\Framework\TestCase
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

    public function vatInProviderForCountryCode(): \Iterator
    {
        yield ['LT12345', 'LT'];
        yield [' LT12345', 'LT'];
        yield ['lt12345', 'LT'];
        yield ['LT 12345', 'LT'];
        yield ['LT-12 345', 'LT'];
        yield ['LT.123.45', 'LT'];
        yield ['LT,123,45', 'LT'];
        yield ['', ''];
        yield [null, ''];
        yield [1, '1'];
        yield ['1111', '11'];
        yield ['abcd', 'AB'];
    }

    /**
     * @dataProvider vatInProviderForNumbers
     */
    public function testGetVatInNumbers($sVatIn, $sExpectCode)
    {
        $oVatIn = new oxCompanyVatIn($sVatIn);
        $this->assertSame($sExpectCode, $oVatIn->getNumbers());
    }

    public function vatInProviderForNumbers(): \Iterator
    {
        yield ['LT12345', '12345'];
        yield [' LT12345', '12345'];
        yield ['LT12345 ', '12345'];
        yield [' LT12345 ', '12345'];
        yield ['', ''];
        yield ['1111', '11'];
        yield ['abcd', 'cd'];
        yield ['LT 12345', '12345'];
        yield ['LT-12 345', '12345'];
        yield ['LT.123.45', '.123.45'];
        yield ['LT,123,45', ',123,45'];
    }
}
