<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Converter\Service;

use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorAggregate;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorAggregate
 */
class MetaDataValidatorAggregateTest extends TestCase
{
    public function testValidate(): void
    {
        $this->expectException(\Exception::class);

        $validatorStub = $this->getMockBuilder(MetaDataValidatorInterface::class)->getMock();
        $validatorStub->method('validate')->willThrowException(new \Exception());

        (new MetaDataValidatorAggregate($validatorStub))->validate(['any']);
    }
}
