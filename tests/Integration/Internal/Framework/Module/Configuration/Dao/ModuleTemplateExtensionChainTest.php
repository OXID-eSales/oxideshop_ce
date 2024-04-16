<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ModuleTemplateExtensionChainTest extends TestCase
{
    private ShopConfigurationDaoInterface $shopConfigurationDao;
    private int $shopId = 1;
    private string $projectConfiguration = __DIR__ . '/Fixtures/project_configuration/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopConfigurationDao = $this->getContainer()->get(ShopConfigurationDaoInterface::class);
    }

    public function testGetWithNonExistingTemplate(): void
    {
        $nonExistingTemplate = uniqid('template_', true);
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($nonExistingTemplate);

        $this->assertEmpty($moduleIds->getIterator()->getArrayCopy());
    }

    public function testGetWithTemplateNameWithUnderscores(): void
    {
        $existingTemplate = 'template_name_with_underscores.html.twig';
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($existingTemplate);

        $this->assertEquals(['module1'], $moduleIds->getIterator()->getArrayCopy());
    }

    public function testGetWithTemplateNameWithHyphens(): void
    {
        $existingTemplate = 'template-name-with-hyphens.html.twig';
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($existingTemplate);

        $this->assertEquals(['module1'], $moduleIds->getIterator()->getArrayCopy());
    }

    public function testGetWithTemplateNameWithUnderscoresAndHyphens(): void
    {
        $existingTemplate = 'template_name_with_underscores-and-hyphens.html.twig';
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($existingTemplate);

        $this->assertEquals(['module1'], $moduleIds->getIterator()->getArrayCopy());
    }

    public function testGetWithTemplateNameWithNamespace(): void
    {
        $existingTemplate = '@namespace/template-name.html.twig';
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($existingTemplate);

        $this->assertEquals(['module1'], $moduleIds->getIterator()->getArrayCopy());
    }

    public function testGetWithMultipleModuleIds(): void
    {
        $existingTemplate = 'template-extended-by-multiple-modules.html.twig';
        $moduleTemplateExtensions = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleTemplateExtensionChain();

        $moduleIds = $moduleTemplateExtensions->getTemplateLoadingPriority($existingTemplate);

        $this->assertEquals(['module3', 'module2', 'module1'], $moduleIds->getIterator()->getArrayCopy());
    }

    private function getContainer(): ContainerBuilder
    {
        $context = new BasicContextStub();
        $context->setProjectConfigurationDirectory($this->projectConfiguration);

        $container = (new TestContainerFactory())->create();
        $container->set(BasicContextInterface::class, $context);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);
        $container->compile();

        return $container;
    }
}
