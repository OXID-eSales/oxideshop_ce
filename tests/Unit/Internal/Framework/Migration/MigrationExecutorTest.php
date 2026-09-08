<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class MigrationExecutorTest extends TestCase
{
    public function testExecuteForwardsWithDefaultOptionsAndOutput(): void
    {
        $innerExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $innerExecutor->expects($this->once())->method('executeWithOptions')->with([], null)->willReturn(0);

        (new MigrationExecutor($innerExecutor))->execute();
    }

    public function testExecuteWithOptionsForwardsOptionsOutputAndStatus(): void
    {
        $options = ['--dry-run' => true];
        $output = new NullOutput();
        $status = 1;

        $innerExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $innerExecutor->expects($this->once())->method('executeWithOptions')->with($options, $output)->willReturn($status);

        $this->assertSame($status, (new MigrationExecutor($innerExecutor))->executeWithOptions($options, $output));
    }
}
