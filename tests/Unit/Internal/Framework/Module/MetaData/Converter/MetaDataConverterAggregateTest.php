<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Converter\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterAggregate;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetaDataConverterAggregate::class)]
final class MetaDataConverterAggregateTest extends TestCase
{
    public function testConvert(): void
    {
        $metaData = ['some metadata contents'];
        $converterStub = $this->getMockBuilder(MetaDataConverterInterface::class)->getMock();
        $converterStub->method('convert')->willReturn($metaData);

        $metaDataFromConverter = (new MetaDataConverterAggregate($converterStub))->convert(['any']);
        $this->assertSame($metaData, $metaDataFromConverter);
    }
}
