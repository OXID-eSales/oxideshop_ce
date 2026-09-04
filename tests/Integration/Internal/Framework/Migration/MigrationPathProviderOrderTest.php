<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\TaggedMigrationExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\PriorityOrder\HighPriorityStubProvider;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\PriorityOrder\LowPriorityStubProvider;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\PriorityOrder\MediumPriorityStubProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class MigrationPathProviderOrderTest extends TestCase
{
    use ContainerTrait;

    public function testProvidersAreOrderedByDescendingPriority(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/PriorityOrder');
        $this->compileContainer();

        $this->assertSame(
            [HighPriorityStubProvider::class, MediumPriorityStubProvider::class, LowPriorityStubProvider::class],
            $this->getWiredProviderClasses()
        );
    }

    /**
     * @return string[]
     */
    private function getWiredProviderClasses(): array
    {
        $executor = $this->get(TaggedMigrationExecutor::class);

        $providers = (new ReflectionProperty($executor, 'providers'))->getValue($executor);

        $classes = [];
        foreach ($providers as $provider) {
            $classes[] = $provider::class;
        }

        return $classes;
    }
}
