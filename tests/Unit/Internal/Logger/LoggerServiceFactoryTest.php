<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger;

use OxidEsales\EshopCommunity\Internal\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
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


    public function testAnExceptionIsThrownIfLogLevelIsNotConfigured()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $context = new ContextStub();
        $context->setLogLevel(NULL);

        $loggerServiceFactory = new LoggerServiceFactory($context);

        $loggerServiceFactory->getLogger();
    }

    private function getLoggerServiceFactory()
    {
        $context = new ContextStub();

        return new LoggerServiceFactory($context);
    }
}
