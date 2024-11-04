<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\DateFormatHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatHelper::class)]
final class DateFormatHelperTest extends TestCase
{
    public static function provider(): array
    {
        return [
            ['%D %h %n %r %R %t %T', 1543850519, "%m/%d/%y %b \n %I:%M:%S %p %H:%M 	 %H:%M:%S"],
            ['%T %t %R %r %n %h %D', 1543850519, "%H:%M:%S 	 %H:%M %I:%M:%S %p \n %b %m/%d/%y"],
        ];
    }

    #[DataProvider('provider')]
    public function testFixWindowsTimeFormat(string $format, int|string $timestamp, string $expectedFormat): void
    {
        $actualFormat = (new DateFormatHelper())->fixWindowsTimeFormat($format, $timestamp);

        $this->assertEquals($expectedFormat, $actualFormat);
    }

    public function testFixWindowsTimeFormatWithDay(): void
    {
        $someTimestamp = 691200;
        $dayWithoutZero = date('j', $someTimestamp);

        $actualFormat = (new DateFormatHelper())->fixWindowsTimeFormat('%e', $someTimestamp);

        $this->assertEquals(" $dayWithoutZero", $actualFormat);
    }

    public function testFixWindowsTimeFormatWithHour(): void
    {
        $someTimestamp = 46800;
        $hourWithoutZero = date('g', $someTimestamp);

        $actualFormat = (new DateFormatHelper())->fixWindowsTimeFormat('%l', $someTimestamp);

        $this->assertEquals(" $hourWithoutZero", $actualFormat);
    }

    public function testFixWindowsTimeFormatWithNonTimeString(): void
    {
        $actualFormat = (new DateFormatHelper())->fixWindowsTimeFormat('foo', '');

        $this->assertEquals('foo', $actualFormat);
    }
}
