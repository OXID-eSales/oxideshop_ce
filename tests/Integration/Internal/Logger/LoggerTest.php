<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Logger;

use OxidEsales\EshopCommunity\Internal\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Utility\Context;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Internal\Logger
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    private $logFilePath;

    public function setUp()
    {
        parent::setUp();

        $this->logFilePath = tempnam(sys_get_temp_dir() . '/test_logger', 'test_');
    }

    public function tearDown()
    {
        unlink($this->logFilePath);
        parent::tearDown();
    }

    public function testLogging()
    {
        $context = $this->getContextStub(LogLevel::ERROR);

        $logger = $this->getLogger($context);
        $logger->critical('Carthago delenda est');

        $this->assertTrue(
            file_exists($context->getLogFilePath())
        );

        $this->assertContains(
            'Carthago delenda est',
            file_get_contents($context->getLogFilePath())
        );
    }

    public function testLoggerDoesNotLogMessagesLowerAsLogLevel()
    {
        $contextStub = $this->getContextStub(LogLevel::WARNING);
        $logger = $this->getLogger($contextStub);
        $infoMessage = 'Info message';
        $logger->info($infoMessage);

        $this->assertFalse(
            strpos(file_get_contents($contextStub->getLogFilePath()), $infoMessage)
        );
    }

    /**
     * @param $context Context
     *
     * @return \Psr\Log\LoggerInterface
     */
    private function getLogger($context)
    {
        $loggerServiceFactory = new LoggerServiceFactory($context);

        return $loggerServiceFactory->getLogger();
    }

    /**
     * Log level is not configured by default.
     *
     * @param string $logLevelFromConfig
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ContextInterface
     */
    private function getContextStub($logLevelFromConfig = null)
    {
        $context = $this
            ->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context
            ->method('getLogFilePath')
            ->willReturn($this->logFilePath);

        $context
            ->method('getLogLevel')
            ->willReturn($logLevelFromConfig);

        return $context;
    }
}
