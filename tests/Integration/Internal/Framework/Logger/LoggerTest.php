<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    private string $logFilePath;
    private ContextInterface $context;

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
        $loggedMessage = uniqid('message-', true);

        $logger = (new LoggerServiceFactory($this->getContext()))->getLogger();
        $logger->critical($loggedMessage);

        $this->assertStringContainsString(
            $loggedMessage,
            file_get_contents($this->logFilePath)
        );
    }

    public function testLoggerDoesNotLogMessagesLowerAsLogLevel(): void
    {
        $loggedMessage = uniqid('message-', true);

        $logger = (new LoggerServiceFactory($this->getContext()))->getLogger();
        $logger->info($loggedMessage);

        $this->assertStringNotContainsString(
            $loggedMessage,
            file_get_contents($this->logFilePath)
        );
    }

    public function testLoggerWithEnvValueMissingWillUseDefaultLogLevel(): void
    {
        putenv('OXID_LOG_LEVEL=');
        $loggedMessage = uniqid('message-', true);

        $logger = (new LoggerServiceFactory($this->getContext()))->getLogger();
        $logger->error($loggedMessage);

        $this->assertStringContainsString(
            $loggedMessage,
            file_get_contents($this->logFilePath)
        );
    }

    private function getContext(): ContextStub
    {
        $context = new ContextStub();
        $context->setLogFilePath($this->logFilePath);

        return $context;
    }
}
