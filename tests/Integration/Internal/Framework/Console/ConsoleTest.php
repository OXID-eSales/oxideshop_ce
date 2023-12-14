<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Tests\ConsoleRunnerTrait;
use PHPUnit\Framework\TestCase;

final class ConsoleTest extends TestCase
{
    use ConsoleRunnerTrait;

    public function testConsoleWithEmptyInput(): void
    {
        $process = $this->runInConsoleAndAssertSuccess('');

        $this->assertNotEmpty($process->getOutput());
    }
}
