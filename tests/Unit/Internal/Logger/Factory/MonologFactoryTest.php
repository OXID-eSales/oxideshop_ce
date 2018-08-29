<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\Factory;

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MonologFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $context = new ContextStub();

        $configuration = new MonologConfiguration(
            'testLogger', $context
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
