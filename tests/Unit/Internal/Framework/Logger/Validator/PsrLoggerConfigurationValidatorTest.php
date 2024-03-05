<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Logger\Validator;

use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\PsrLoggerConfigurationValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use stdClass;

final class PsrLoggerConfigurationValidatorTest extends TestCase
{
    #[DataProvider('dataProviderValidLogLevels')]
    #[DoesNotPerformAssertions]
    public function testValidLogLevelValidation(string $logLevel): void
    {
        /** @var MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMockBuilder(PsrLoggerConfigurationInterface::class)->getMock();
        $configurationMock
            ->method('getLogLevel')
            ->willReturn($logLevel);

        $validator = new PsrLoggerConfigurationValidator();
        $validator->validate($configurationMock);
    }

    public static function dataProviderValidLogLevels(): array
    {
        return [
            [LogLevel::DEBUG],
            [LogLevel::INFO],
            [LogLevel::NOTICE],
            [LogLevel::WARNING],
            [LogLevel::ERROR],
            [LogLevel::CRITICAL],
            [LogLevel::ALERT],
            [LogLevel::EMERGENCY],
        ];
    }

    #[DataProvider('dataProviderInvalidLogLevels')]
    public function testInvalidLogLevelValidation(bool|string|int|float|stdClass|array|null $logLevel): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMockBuilder(PsrLoggerConfigurationInterface::class)->getMock();
        $configurationMock
            ->method('getLogLevel')
            ->willReturn($logLevel);

        $validator = new PsrLoggerConfigurationValidator();
        $validator->validate($configurationMock);
    }

    public static function dataProviderInvalidLogLevels(): array
    {
        return [
            [null],
            [false],
            [true],
            ['string'],
            [0],
            [1.0000],
            [new stdClass()],
            [['array']],
        ];
    }
}
