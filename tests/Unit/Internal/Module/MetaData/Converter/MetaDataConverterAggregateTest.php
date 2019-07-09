<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Converter\Service;

use OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter\MetaDataConverterAggregate;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter\MetaDataConverterAggregate
 */
class MetaDataConverterAggregateTest extends TestCase
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
