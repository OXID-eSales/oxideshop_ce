<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

class LoggerServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLogger()
    {
        $loggerServiceFactory = $this->getLoggerServiceFactory();

        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerServiceFactory->getLogger()
        );
    }

    private function getLoggerServiceFactory()
    {
        $context = $this->getMock(ContextInterface::class);
        $context
            ->method('getLogFilePath')
            ->willReturn('log.txt');
        $context
            ->method('getLogLevel')
            ->willReturn('error');

        return new LoggerServiceFactory($context);
    }
}
