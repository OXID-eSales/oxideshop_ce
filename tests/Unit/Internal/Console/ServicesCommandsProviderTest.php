<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Console\CommandsProvider\ServicesCommandsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class ServicesCommandsProviderTest extends TestCase
{
    public function testGetCommandsWhenExists()
    {
        $container = new Container();
        $container->setParameter('console.command.ids', ['test.service']);
        $testableCommand = new \StdClass();
        $container->set('test.service', $testableCommand);

        $provider = new ServicesCommandsProvider($container);
        $this->assertSame([$testableCommand], $provider->getCommands());
    }

    public function testGetCommandsWhenNotExists()
    {
        $container = new Container();
        $provider = new ServicesCommandsProvider($container);
        $this->assertSame([], $provider->getCommands());
    }
}
