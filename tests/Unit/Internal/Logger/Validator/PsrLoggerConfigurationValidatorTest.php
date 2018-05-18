<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\PsrLoggerConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use Psr\Log\LogLevel;

class PsrLoggerConfigurationValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider dataProviderValidLogLevels
     */
    public function testValidLogLevelValidation($logLevel)
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMock(PsrLoggerConfigurationInterface::class);
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
        $this->setExpectedException(\InvalidArgumentException::class);

        /** @var PHPUnit_Framework_MockObject_MockObject|PsrLoggerConfigurationInterface $configurationMock */
        $configurationMock = $this->getMock(PsrLoggerConfigurationInterface::class);
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
            [NULL],
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