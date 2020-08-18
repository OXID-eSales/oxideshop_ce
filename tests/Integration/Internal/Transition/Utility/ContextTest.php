<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Utility;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class ContextTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        /** Unmocking the context */
       // $this->overrideService(ContextInterface::class, new Context());
        parent::setUp();
    }

    public function testGetLogLevel()
    {
        Registry::getConfig()->setConfigParam('sLogLevel', LogLevel::ALERT);
        $context = $this->get(ContextInterface::class);

        $this->assertSame(
            LogLevel::ALERT,
            $context->getLogLevel()
        );
    }

    public function testGetLogLevelReturnsDefaultLogLevel()
    {
        Registry::getConfig()->setConfigParam('sLogLevel', null);
        $context = $this->get(ContextInterface::class);

        $this->assertSame(
            LogLevel::ERROR,
            $context->getLogLevel()
        );
    }
}
