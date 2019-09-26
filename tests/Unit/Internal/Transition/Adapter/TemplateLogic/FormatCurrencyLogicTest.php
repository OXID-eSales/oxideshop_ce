<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatCurrencyLogic;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class FormatCurrencyLogicTest
 */
class FormatCurrencyLogicTest extends UnitTestCase
{

    /** @var FormatCurrencyLogic */
    private $numberFormatLogic;

    protected function setUp()
    {
        $this->numberFormatLogic = new FormatCurrencyLogic();
        parent::setUp();
    }

    /**
     * @param string     $format
     * @param string|int $value
     * @param string     $expected
     *
     * @dataProvider numberFormatProvider
     */
    public function testNumberFormat($format, $value, $expected)
    {
        $this->assertEquals($expected, $this->numberFormatLogic->numberFormat($format, $value));
    }

    /**
     * @return array
     */
    public function numberFormatProvider(): array
    {
        return [
            ["EUR@ 1.00@ ,@ .@ EUR@ 2", 25000, '25.000,00'],
            ["EUR@ 1.00@ ,@ .@ EUR@ 2", 25000.1584, '25.000,16'],
            ["EUR@ 1.00@ ,@ .@ EUR@ 3", 25000.1584, '25.000,158'],
            ["EUR@ 1.00@ ,@ .@ EUR@ 0", 25000000.5584, '25.000.001'],
            ["EUR@ 1.00@ .@ ,@ EUR@ 2", 25000000.5584, '25,000,000.56'],
        ];
    }
}
