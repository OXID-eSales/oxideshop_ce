<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class MigrationExecutorTest extends TestCase
{
    use ContainerTrait;

    public function testExecutesMigrations(): void
    {
        $status = $this->get(MigrationExecutor::class)->executeWithOptions(['--dry-run' => true], new NullOutput());

        $this->assertSame(0, $status);
    }
}
