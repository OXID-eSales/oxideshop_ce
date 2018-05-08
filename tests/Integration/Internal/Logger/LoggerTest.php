<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Logger;

use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\LoggerServiceFactory;
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
    /** @var \OxidEsales\TestingLibrary\VfsStreamWrapper */
    private $vfsStreamWrapper = null;

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
            ->willReturn($this->getVfsLogFile());

        $context
            ->method('getLogLevel')
            ->willReturn($logLevelFromConfig);

        return $context;
    }

    /**
     * @return string
     */
    private function getVfsLogFile()
    {
        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $relativeLogFilePath = 'logs/vfsLogFile.txt';
        $logFilePath = $vfsStreamWrapper->getRootPath() . $relativeLogFilePath;

        if (!is_file($logFilePath)) {
            $vfsStreamWrapper->createFile($relativeLogFilePath);
        }

        return $logFilePath;
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
     * @return \OxidEsales\TestingLibrary\VfsStreamWrapper
     */
    private function getVfsStreamWrapper()
    {
        if (is_null($this->vfsStreamWrapper)) {
            $this->vfsStreamWrapper = new \OxidEsales\TestingLibrary\VfsStreamWrapper();
        }

        return $this->vfsStreamWrapper;
    }
}
