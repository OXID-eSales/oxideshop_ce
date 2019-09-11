<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\PsrLoggerConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\PsrLoggerConfigurationValidator;
use Psr\Log\LogLevel;

class PsrLoggerConfigurationValidatorTest extends PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider dataProviderValidLogLevels
     */
    public function testValidLogLevelValidation($logLevel)
    {
        /** @var PHPUnit\Framework\MockObject\MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMockBuilder(PsrLoggerConfigurationInterface::class)->getMock();
        $configurationMock
            ->expects($this->any())
            ->method('getLogLevel')
            ->will($this->returnValue($logLevel));

        $validator = new PsrLoggerConfigurationValidator();
        $validator->validate($configurationMock);
    }

    public function dataProviderValidLogLevels()
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

    /**
     * @dataProvider dataProviderInvalidLogLevels
     */
    public function testInvalidLogLevelValidation($logLevel)
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var PHPUnit\Framework\MockObject\MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMockBuilder(PsrLoggerConfigurationInterface::class)->getMock();
        $configurationMock
            ->expects($this->any())
            ->method('getLogLevel')
            ->will($this->returnValue($logLevel));

        $validator = new PsrLoggerConfigurationValidator();
        $validator->validate($configurationMock);
    }

    public function dataProviderInvalidLogLevels()
    {
        return [
            [null],
            [false],
            [true],
            ['string'],
            [0],
            [1.0000],
            [new \stdClass()],
            [['array']],
        ];
    }
}
