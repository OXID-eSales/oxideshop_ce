<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatCurrencyLogic;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class FormatCurrencyLogicTest extends IntegrationTestCase
{
    private FormatCurrencyLogic $numberFormatLogic;

    public function setUp(): void
    {
        $this->numberFormatLogic = new FormatCurrencyLogic();
        parent::setUp();
    }

    /**
     * @param string|int $value
     */
    #[DataProvider('numberFormatProvider')]
    public function testNumberFormat(string $format, int|float $value, string $expected): void
    {
        $this->assertEquals($expected, $this->numberFormatLogic->numberFormat($format, $value));
    }

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
