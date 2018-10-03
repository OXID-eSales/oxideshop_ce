<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Console\CommandsCollectionBuilder;
use OxidEsales\EshopCommunity\Internal\Console\CommandsProvider\CommandsProviderInterface;
use PHPUnit\Framework\TestCase;

class CommandsCollectionBuilderTest extends TestCase
{
    public function testAddCommand()
    {

        $command1 = $this->getMockBuilder(CommandsProviderInterface::class)->getMock();
        $command1->method('getCommands')->willReturn(['test1', 'test2']);

        $commandsCollection = new CommandsCollectionBuilder($command1);

        $this->assertSame(['test1', 'test2'], $commandsCollection->build()->toArray());
    }

    public function testAddMultipleCommands()
    {
        $command1 = $this->getMockBuilder(CommandsProviderInterface::class)->getMock();
        $command1->method('getCommands')->willReturn(['test1', 'test2']);
        $command2 = $this->getMockBuilder(CommandsProviderInterface::class)->getMock();
        $command2->method('getCommands')->willReturn(['test3', 'test4']);

        $commandsCollection = new CommandsCollectionBuilder($command1, $command2);

        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $commandsCollection->build()->toArray());
    }
}
