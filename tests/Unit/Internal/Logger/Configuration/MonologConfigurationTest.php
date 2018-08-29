<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfiguration;
use \OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Psr\Log\LogLevel;

class MonologConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultLogLevel()
    {
        $stub = new ContextStub();
        $stub->setLogLevel(null);

        $configuration = new MonologConfiguration(
            "OXID Logger",
            $stub
        );

        $this->assertSame(
            LogLevel::ERROR,
            $configuration->getLogLevel()
        );
    }
}
