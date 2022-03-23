<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use chainGeneration\namespaced\Model\Product as ProductModule;
use module_article as ProductLegacyModule;
use OxidEsales\Eshop\Application\Model\Article as ProductUnifiedNamespace;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\ModuleInstallerTrait;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ModuleChainGenerationNamespacedBeforeLegacyTest extends UnitTestCase
{
    use ModuleInstallerTrait;
    use ContainerTrait;

    private array $moduleSequence = [
        'chainGeneration/namespaced',
        'chainGeneration/legacy',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockModuleAutoload();
        $this->addModules($this->moduleSequence, __DIR__ . '/Fixtures/');
    }

    protected function tearDown(): void
    {
        $this->removeModules($this->moduleSequence);

        parent::tearDown();
    }

    public function testChain(): void
    {
        $product = oxNew(ProductUnifiedNamespace::class);

        $parent = get_parent_class($product);
        $this->assertInstanceOf(ProductLegacyModule::class, $product);
        $this->assertEquals(ProductModule::class, $parent);
    }

    private function mockModuleAutoload(): void
    {
        $modulesPath = $this->getContainer()->get(ContextInterface::class)->getModulesPath();
        require_once __DIR__ . '/Fixtures/chainGeneration/namespaced/module_autoload.php';
    }
}
