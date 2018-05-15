<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Logger\DataObject\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Logger\Mapper\MonologLogLevelMapper;
use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\MonologLoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MonologFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $configuration = new MonologConfiguration(
            'testLogger',
            'pathString',
            LogLevel::ERROR
        );

        $validator = new PsrLoggerConfigurationValidator();
        $mapper = new MonologLogLevelMapper($validator);

        $loggerFactory = new MonologLoggerServiceFactory(
            $configuration,
            $mapper
        );

        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerFactory->create()
        );
    }
}
