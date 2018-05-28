<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfiguration;
use Psr\Log\LogLevel;

class MonologConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultLogLevel()
    {
        $configuration = new MonologConfiguration(
            'testLogger',
            'path',
            null
        );

        $this->assertSame(
            LogLevel::ERROR,
            $configuration->getLogLevel()
        );
    }
}
