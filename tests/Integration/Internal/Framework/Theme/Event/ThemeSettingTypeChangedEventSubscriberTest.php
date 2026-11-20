<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingTypeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Path;

final class ThemeSettingTypeChangedEventSubscriberTest extends TestCase
{
    use ContainerTrait;

    private string $testLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareLogger();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testLog)) {
            unlink($this->testLog);
        }
        parent::tearDown();
    }

    public function testWarningIsLoggedOnEventDispatch(): void
    {
        $this->get(EventDispatcherInterface::class)
            ->dispatch(new ThemeSettingTypeChangedEvent(1, 'some-theme', 'some-setting', 'bool', 'select'));

        $this->assertFileExists($this->testLog);
        $this->assertStringContainsString('some-setting', file_get_contents($this->testLog));
    }

    private function prepareLogger(): void
    {
        /** @var ContextStub $context */
        $context = $this->get(ContextInterface::class);
        $this->testLog = Path::join(
            Path::getDirectory($context->getLogFilePath()),
            uniqid('test.log.', true)
        );
        $context->setLogFilePath($this->testLog);
        $context->setLogLevel(LogLevel::WARNING);
    }
}
