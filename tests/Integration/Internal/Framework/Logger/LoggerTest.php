<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LogLevel;

final class LoggerTest extends TestCase
{
    private string|bool $logFilePath;

    public function setup(): void
    {
        parent::setUp();

        $this->logFilePath = tempnam(sys_get_temp_dir(), 'test_');
    }

    public function tearDown(): void
    {
        unlink($this->logFilePath);
        parent::tearDown();
    }

    public function testLogging(): void
    {
        $context = $this->getContextStub(LogLevel::ERROR);

        $logger = $this->getLogger($context);
        $logger->critical('Carthago delenda est');

        $this->assertFileExists(
            $context->getLogFilePath()
        );

        $this->assertStringContainsString(
            'Carthago delenda est',
            file_get_contents($context->getLogFilePath())
        );
    }

    public function testLoggerDoesNotLogMessagesLowerAsLogLevel(): void
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
     * @return LoggerInterface
     */
    private function getLogger(ContextInterface|MockObject $context)
    {
        $loggerServiceFactory = new LoggerServiceFactory($context);

        return $loggerServiceFactory->getLogger();
    }

    private function getContextStub(string $logLevelFromConfig): ContextInterface|MockObject
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
