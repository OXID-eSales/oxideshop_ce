<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ShopConfigurationWithModuleLoadSequenceTest extends TestCase
{
    private ShopConfigurationDaoInterface $shopConfigurationDao;
    private int $shopId = 1;
    private string $projectConfiguration = __DIR__ . '/Fixtures/project_configuration/';
    private string $moduleLineupFile = '/shops/1.module_load_sequence.yaml';

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopConfigurationDao = $this->getContainer()->get(ShopConfigurationDaoInterface::class);
    }

    protected function tearDown(): void
    {
        $this->writeToModuleLineupFile('');

        parent::tearDown();
    }

    /** @dataProvider moduleLineupDataProvider */
    public function testGet(string $fileContents, array $expectedModuleChain, string $testCase): void
    {
        $this->writeToModuleLineupFile($fileContents);

        $modulesConfigurationIds = $this->shopConfigurationDao
            ->get($this->shopId)
            ->getModuleIdsOfModuleConfigurations();

        $this->assertEquals($expectedModuleChain, $modulesConfigurationIds, "Failed for test case: '$testCase'");
    }

    public function testGetWithNonExistingModuleId(): void
    {
        $fileContents = 'moduleChains:
        loadSequence:
            - moduleId3
            - moduleId-not-found
            - moduleId2
        ';
        $this->writeToModuleLineupFile($fileContents);

        $this->expectException(ModuleConfigurationNotFoundException::class);

        $this->shopConfigurationDao->get($this->shopId);
    }

    public function moduleLineupDataProvider(): array
    {
        return [
            [
                'lineupFileContents' => '',
                'expectedModuleChain' => [
                    'moduleId1',
                    'moduleId2',
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                ],
                'testCase' => 'with empty file',
            ],
            [
                'lineupFileContents' => 'moduleChains:
        loadSequence:
            - moduleId1',
                'expectedModuleChain' => [
                    'moduleId2',
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                    'moduleId1',
                ],
                'testCase' => 'with single file entry',
            ],
            [
                'lineupFileContents' => 'moduleChains:
        loadSequence:
            - moduleId2
            - moduleId1
        ',
                'expectedModuleChain' => [
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                    'moduleId1',
                    'moduleId2',
                ],
                'testCase' => 'with 2 first entries reversed',
            ],
            [
                'lineupFileContents' => 'moduleChains:
        loadSequence:
            - moduleId5
            - moduleId4
        ',
                'expectedModuleChain' => [
                    'moduleId1',
                    'moduleId2',
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                ],
                'testCase' => 'with 2 last entries reversed',
            ],
            [
                'lineupFileContents' => 'moduleChains:
        loadSequence:
            - moduleId2
            - moduleId1
            - moduleId5
            - moduleId4
        ',
                'expectedModuleChain' => [
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                    'moduleId1',
                    'moduleId2',
                ],
                'testCase' => 'with 2 first and 2 last entries reversed',
            ],
            [
                'lineupFileContents' => 'moduleChains:
        loadSequence:
            - moduleId5
            - moduleId4
            - moduleId3
            - moduleId2
            - moduleId1
        ',
                'expectedModuleChain' => [
                    'moduleId1',
                    'moduleId2',
                    'moduleId3',
                    'moduleId4',
                    'moduleId5',
                ],
                'testCase' => 'with all entries reversed',
            ],
        ];
    }

    private function writeToModuleLineupFile(string $contents): void
    {
        file_put_contents($this->getModuleLineupFilePath(), $contents);
    }

    private function getModuleLineupFilePath(): string
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
