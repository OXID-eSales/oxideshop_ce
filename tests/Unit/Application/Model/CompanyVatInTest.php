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
        return array(
            array('LT12345', 'LT'),
            array(' LT12345', 'LT'),
            array('lt12345', 'LT'),
            array('LT 12345', 'LT'),
            array('LT-12 345', 'LT'),
            array('LT.123.45', 'LT'),
            array('LT,123,45', 'LT'),
            array('', ''),
            array(null, ''),
            array(1, '1'),
            array('1111', '11'),
            array('abcd', 'AB')
        );
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
        return array(
            array('LT12345', '12345'),
            array(' LT12345', '12345'),
            array('LT12345 ', '12345'),
            array(' LT12345 ', '12345'),
            array('', ''),
            array('1111', '11'),
            array('abcd', 'cd'),
            array('LT 12345', '12345'),
            array('LT-12 345', '12345'),
            array('LT.123.45', '.123.45'),
            array('LT,123,45', ',123,45'),
        );
    }
}
