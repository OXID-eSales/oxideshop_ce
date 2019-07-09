<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Console\ExecutorInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ExecutorTest extends TestCase
{
    use ConsoleTrait;
    use ContainerTrait;

    public function testIfRegisteredCommandInList()
    {
        $output = $this->executeCommand('list');

        $this->assertRegexp('/oe:tests:test-command/', $this->getOutputFromStream($output));
    }

    public function testCommandExecution()
    {
        $output = $this->executeCommand('oe:tests:test-command');

        $this->assertSame('Command have been executed!'.PHP_EOL, $this->getOutputFromStream($output));
    }

    public function testCommandWithChangedNameExecution()
    {
        $output = $this->executeCommand('oe:tests:test-command-changed-name');

        $this->assertSame('Command have been executed!'.PHP_EOL, $this->getOutputFromStream($output));
    }

    /**
     * @return ExecutorInterface
     */
    private function makeExecutor(): ExecutorInterface
    {
        $context = $this
            ->getMockBuilder(BasicContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGeneratedServicesFilePath'])
            ->getMock();
        $context->method('getGeneratedServicesFilePath')->willReturn(__DIR__ . '/Fixtures/generated_project.yaml');

        $containerBuilder = new ContainerBuilder($context);

        $container = $containerBuilder->getContainer();
        $definition = $container->getDefinition('oxid_esales.console.symfony.component.console.application');
        $definition->addMethodCall('setAutoExit', [false]);

        $container->compile();

        return $container->get(ExecutorInterface::class);
    }

    /**
     * @param StreamOutput $output
     * @return bool|string
     */
    private function getOutputFromStream($output)
    {
        $stream = $output->getStream();
        rewind($stream);
        $display = stream_get_contents($stream);
        return $display;
    }

    /**
     * @param string $command
     * @return StreamOutput
     */
    private function executeCommand(string $command): StreamOutput
    {
        $executor = $this->makeExecutor();
        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $executor->execute(new ArrayInput(['command' => $command]), $output);

        return $output;
    }
}
