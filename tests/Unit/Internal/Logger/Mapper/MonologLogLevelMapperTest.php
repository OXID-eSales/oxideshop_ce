<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\Mapper;

use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Logger\DataObject\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Mapper\MonologLogLevelMapper;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class MonologLogLevelMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testLogLevelMapping()
    {
        $configuration = $this->getMockBuilder(MonologConfigurationInterface::class)->getMock();
        $configuration
            ->method('getLogLevel')
            ->willReturn(LogLevel::WARNING);

        $validator = new PsrLoggerConfigurationValidator();
        $mapper = new MonologLogLevelMapper($validator);

        $this->assertEquals(
            Logger::WARNING,
            $mapper->getLoggerLogLevel($configuration)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLogLevelMappingWithInvalidLogLevel()
    {
        $configuration = $this->getMockBuilder(MonologConfigurationInterface::class)->getMock();
        $configuration
            ->method('getLogLevel')
            ->willReturn('invalidLogLevel');

        $validator = new PsrLoggerConfigurationValidator();
        $mapper = new MonologLogLevelMapper($validator);

        $mapper->getLoggerLogLevel($configuration);
    }
}
