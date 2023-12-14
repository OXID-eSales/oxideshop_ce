<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\Facts\Facts;
use RuntimeException;
use Symfony\Component\Process\Process;

trait ConsoleRunnerTrait
{
    public function runInConsole(string $command): Process
    {
        $process = Process::fromShellCommandline(
            "{$this->getPathToConsoleScript()} {$command}"
        );
        $process->run();

        return $process;
    }

    public function runInConsoleAndAssertSuccess(string $command): Process
    {
        $process = $this->runInConsole($command);
        if (!$process->isSuccessful()) {
            $this->fail(
                sprintf(
                    'Execution of `oe-console` failed unexpectedly! The error output was: %s %s.',
                    $process->getOutput(),
                    $process->getErrorOutput()
                )
            );
        }
        return $process;
    }

    private function getPathToConsoleScript(): string
    {
        $scriptPath = 'bin/oe-console';
        $shopRootPath = (new Facts())->getShopRootPath();
        if (is_file("$shopRootPath/vendor/$scriptPath")) {
            return "$shopRootPath/vendor/$scriptPath";
        }
        if (is_file("$shopRootPath/$scriptPath")) {
            return "$shopRootPath/$scriptPath";
        }
        throw new RuntimeException("Error: $scriptPath is not accessible!");
    }
}
