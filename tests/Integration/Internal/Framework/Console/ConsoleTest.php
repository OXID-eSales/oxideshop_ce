<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Tests\ConsoleRunnerTrait;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\EnvTrait;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class ConsoleTest extends TestCase
{
    use ConsoleRunnerTrait;
    use EnvTrait;
    use ContainerTrait;

    public function testConsoleWithEmptyInput(): void
    {
        $process = $this->runInConsoleAndAssertSuccess('');

        $this->assertNotEmpty($process->getOutput());
    }

    public function testConsoleWillParseEnvVariables(): void
    {
        $envValue = 123;
        $this->loadEnvFixture(__DIR__ . '/Fixtures', ["OXID_VALUE='$envValue'"]);
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();

        $this->runInConsoleAndAssertSuccess('');

        $this->assertEquals($envValue, $this->getParameter('oxid_value'));
    }
}
