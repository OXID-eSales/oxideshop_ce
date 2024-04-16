<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentWithOrphanSettingEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Path;

final class ShopEnvironmentMisconfigurationEventSubscriberTest extends TestCase
{
    use ContainerTrait;

    private ?string $testLog = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareLogger();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestLog();
        parent::tearDown();
    }

    public function testLogIsCreatedOnEventDispatch(): void
    {
        $this->get(EventDispatcherInterface::class)
            ->dispatch(
                new ShopEnvironmentWithOrphanSettingEvent(
                    123,
                    'some-module',
                    'some-setting'
                )
            );

        $this->assertFileExists($this->testLog);
    }

    private function prepareLogger(): void
    {
        /** @var ContextStub $context */
        $context = $this->get(ContextInterface::class);
        $logDirectory = Path::getDirectory($context->getLogFilePath());
        $testLogFile = uniqid('test.log.', true);
        $this->testLog = Path::join($logDirectory, $testLogFile);
        $context->setLogFilePath($this->testLog);
        $context->setLogLevel(LogLevel::WARNING);
    }

    private function cleanupTestLog(): void
    {
        if (\file_exists($this->testLog)) {
            \unlink($this->testLog);
        }
    }
}
