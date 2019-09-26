<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Utility;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class ContextTest extends TestCase
{
    use ContainerTrait;

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
