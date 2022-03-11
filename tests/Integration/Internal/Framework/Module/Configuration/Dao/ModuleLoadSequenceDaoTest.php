<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleLoadSequenceDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ModuleLoadSequenceDaoTest extends TestCase
{
    private ModuleLoadSequenceDaoInterface $moduleLoadSequenceDao;
    private int $shopId = 1;
    private string $projectConfiguration = __DIR__ . '/Fixtures/project_configuration/';
    private string $moduleLineupFile = '/shops/1.module_load_sequence.yaml';

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleLoadSequenceDao = $this->getContainer()->get(ModuleLoadSequenceDaoInterface::class);
    }

    protected function tearDown(): void
    {
        $this->writeToModuleLoadSequenceFile('');

        parent::tearDown();
    }

    public function testGetWillReturnExpectedData(): void
    {
        $moduleId1 = uniqid('module-', true);
        $moduleId2 = uniqid('module-', true);
        $moduleId3 = uniqid('module-', true);
        $contents = "moduleChains:
        loadSequence:
            - $moduleId1
            - $moduleId2
            - $moduleId3
        ";
        $this->writeToModuleLoadSequenceFile($contents);

        $data = $this->moduleLoadSequenceDao->get($this->shopId)->getConfiguredModulesIds();

        $this->assertEquals([
            $moduleId1,
            $moduleId2,
            $moduleId3,
        ], $data);
    }

    public function testGetWithInvalidConfiguration(): void
    {
        $contents = "moduleChains:
        wrong-key:
        - 123";
        $this->writeToModuleLoadSequenceFile($contents);
        $this->expectException(InvalidConfigurationException::class);
        $this->moduleLoadSequenceDao->get($this->shopId);
    }

    public function testGetWithNonExistingFile(): void
    {
        unlink($this->getModuleLoadSequenceFilePath());

        $modules = $this->moduleLoadSequenceDao->get($this->shopId)->getConfiguredModulesIds();

        $this->assertEmpty($modules);
    }

    private function writeToModuleLoadSequenceFile(string $contents): void
    {
        file_put_contents($this->getModuleLoadSequenceFilePath(), $contents);
    }

    private function getModuleLoadSequenceFilePath(): string
    {
        return "$this->projectConfiguration/$this->moduleLineupFile";
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
