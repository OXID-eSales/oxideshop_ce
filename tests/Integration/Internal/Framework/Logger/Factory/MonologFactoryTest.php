<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Logger\Factory;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Validator\PsrLoggerConfigurationValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class MonologFactoryTest extends TestCase
{
    public function testCreation(): void
    {
        $context = new ContextStub();

        $configuration = new MonologConfiguration(
            'testLogger',
            $context->getLogFilePath(),
            $context->getLogLevel()
        );

        $validator = new PsrLoggerConfigurationValidator();

        $loggerFactory = new MonologLoggerFactory(
            $configuration,
            $validator
        );

        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerFactory->create()
        );
    }
}
