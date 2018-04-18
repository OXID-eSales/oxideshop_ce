<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\MonologLoggerServiceFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MonologFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $loggerFactory = new MonologLoggerServiceFactory(
            'testLogger',
            'log.txt',
            LogLevel::WARNING
        );


        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerFactory->create()
        );
    }
}
