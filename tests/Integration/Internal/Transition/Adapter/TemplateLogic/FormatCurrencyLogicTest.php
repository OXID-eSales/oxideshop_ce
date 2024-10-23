<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatCurrencyLogic;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FormatCurrencyLogicTest extends IntegrationTestCase
{

    /** @var FormatCurrencyLogic */
    private $numberFormatLogic;

    public function setUp(): void
    {
        $this->numberFormatLogic = new FormatCurrencyLogic();
        parent::setUp();
    }

    /**
     * @param string     $format
     * @param string|int $value
     * @param string     $expected
     */
    #[DataProvider('numberFormatProvider')]
    public function testNumberFormat($format, $value, $expected)
    {
        $this->assertEquals($expected, $this->numberFormatLogic->numberFormat($format, $value));
    }

    /**
     * @return array
     */
    public static function numberFormatProvider(): array
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
