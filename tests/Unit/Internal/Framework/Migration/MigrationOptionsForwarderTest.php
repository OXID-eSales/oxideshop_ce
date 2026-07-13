<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationOptionsForwarder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class MigrationOptionsForwarderTest extends TestCase
{
    public function testMirrorAddsForwardableOptions(): void
    {
        $definition = new InputDefinition();

        (new MigrationOptionsForwarder())->mirror($definition);

        foreach (['write-sql', 'dry-run', 'query-time', 'allow-no-migration', 'all-or-nothing'] as $option) {
            $this->assertTrue($definition->hasOption($option));
        }
    }

    public function testMirrorOmitsExcludedOptions(): void
    {
        $definition = new InputDefinition();

        (new MigrationOptionsForwarder())->mirror($definition);

        foreach (['configuration', 'db-configuration', 'em', 'conn'] as $option) {
            $this->assertFalse($definition->hasOption($option));
        }
    }

    public function testCollectReturnsOnlyProvidedFlags(): void
    {
        $forwarder = new MigrationOptionsForwarder();
        $definition = new InputDefinition();
        $forwarder->mirror($definition);

        $input = new ArrayInput(['--dry-run' => true, '--all-or-nothing' => '1'], $definition);

        $this->assertEquals(
            ['--dry-run' => true, '--all-or-nothing' => '1'],
            $forwarder->collect($input)
        );
    }

    public function testCollectReturnsEmptyArrayWhenNothingProvided(): void
    {
        $forwarder = new MigrationOptionsForwarder();
        $definition = new InputDefinition();
        $forwarder->mirror($definition);

        $this->assertSame([], $forwarder->collect(new ArrayInput([], $definition)));
    }
}
