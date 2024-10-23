<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Console\CommandsProvider;

use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\ServicesCommandsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;

class ServicesCommandsProviderTest extends TestCase
{
    public function testGetCommandsWhenServiceExtendsSymfonyCommandClass()
    {
        $container = new Container();
        $container->setParameter('console.command.ids', ['test.service']);
        $testableCommand = $this->createMock(Command::class);
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
