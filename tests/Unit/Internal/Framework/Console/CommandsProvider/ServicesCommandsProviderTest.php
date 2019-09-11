<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Console\CommandsProvider;

use OxidEsales\EshopCommunity\Internal\Framework\Console\AbstractShopAwareCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\ServicesCommandsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;

class ServicesCommandsProviderTest extends TestCase
{
    public function testGetCommandsWhenServiceBelongsToCurrentShop()
    {
        $container = new Container();
        $container->setParameter('console.command.ids', ['test.service']);
        $testableCommand = $this->getMockBuilder(AbstractShopAwareCommand::class)
            ->setMethods(['isActive'])
            ->getMockForAbstractClass();
        $testableCommand->method('isActive')->will($this->returnValue(true));
        $container->set('test.service', $testableCommand);

        $provider = new ServicesCommandsProvider($container);
        $this->assertSame([$testableCommand], $provider->getCommands());
    }

    public function testGetCommandsWhenServiceDoesNotBelongToCurrentShop()
    {
        $container = new Container();
        $container->setParameter('console.command.ids', ['test.service']);
        $testableCommand = $this->getMockBuilder(AbstractShopAwareCommand::class)
            ->setMethods(['isActive'])
            ->disableOriginalClone()
            ->disableProxyingToOriginalMethods()
            ->getMockForAbstractClass();
        $testableCommand->method('isActive')->will($this->returnValue(false));
        $container->set('test.service', $testableCommand);

        $provider = new ServicesCommandsProvider($container);
        $this->assertSame([], $provider->getCommands());
    }

    public function testGetCommandsWhenServiceExtendsSymfonyCommandClass()
    {
        $container = new Container();
        $container->setParameter('console.command.ids', ['test.service']);
        $testableCommand = $this->getMockForAbstractClass(Command::class);
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
