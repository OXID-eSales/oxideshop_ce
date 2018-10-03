<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Console\CommandsCollectionBuilder;
use OxidEsales\EshopCommunity\Internal\Console\CommandsProvider\CommandsProvidableInterface;
use OxidEsales\EshopCommunity\Internal\Console\Executor;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Console\Fixtures\TestCommand;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Console\Fixtures\TestForActiveSubshopCommand;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

class ExecutorTest extends TestCase
{
    use ConsoleTrait;
    use ContainerTrait;

    public function testIfShopIdInGlobalOptionsList()
    {
        $consoleOutput = $this->execute(
            $this->getConsoleApplication(),
            new CommandsCollectionBuilder(),
            new ArrayInput(['command' => 'list'])
        );

        $this->assertRegexp('/--shop-id/', $consoleOutput);
    }

    public function testIfRegisteredCommandInList()
    {
        $commands = $this->getMockBuilder(CommandsProvidableInterface::class)->getMock();
        $commands->method('getCommands')->willReturn([new TestCommand()]);
        $commandsCollectionBuilder = new CommandsCollectionBuilder($commands);
        $consoleOutput = $this->execute($this->getConsoleApplication(), $commandsCollectionBuilder, new ArrayInput(['command' => 'list']));

        $this->assertRegexp('/oe:tests:test-command/', $consoleOutput);
    }

    public function testCommandExecution()
    {
        $commands = $this->getMockBuilder(CommandsProvidableInterface::class)->getMock();
        $commands->method('getCommands')->willReturn([new TestCommand()]);
        $commandsCollectionBuilder = new CommandsCollectionBuilder($commands);
        $consoleOutput = $this->execute(
            $this->getConsoleApplication(),
            $commandsCollectionBuilder,
            new ArrayInput(['command' => 'oe:tests:test-command'])
        );

        $this->assertSame('Command have been executed!'.PHP_EOL, $consoleOutput);
    }
}
