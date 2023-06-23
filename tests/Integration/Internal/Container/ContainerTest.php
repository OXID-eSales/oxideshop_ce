<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Container\ContainerTrait;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContainerTest extends IntegrationTestCase
{
    use ContainerTrait;
    private ContainerInterface $container;
    private string $testServicesYml = '../../tests/Integration/Internal/Container/Fixtures/Project/services.yaml';

    public function setUp(): void
    {
        parent::setUp();

        ContainerFactory::resetContainer();

        $this->container = $this->getContainer();
    }

    public function tearDown(): void
    {
        ContainerFactory::resetContainer();

        $this->cleanUpGeneratedServices();
        parent::tearDown();
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            $this->container
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAllServicesAreCorrectlyConfigured(): void
    {
        $testContainer = (new TestContainerFactory())->create();
        $this->generateShopConfigurationStubForVfs();
        $testContainer->compile();
        foreach ($testContainer->getDefinitions() as $key => $definition) {
            if ($definition->isPublic()) {
                $testContainer->get($key);
            }
        }
    }

    /**
     * Checks that a private service may not be accessed
     */
    public function testPrivateServices(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $this->get(Logger::class);
    }

    public function testCacheIsUsed(): void
    {
        $projectYamlDao = $this->getProjectYmlDao();

        $projectConfigurationFile = $projectYamlDao->loadProjectConfigFile();
        $projectConfigurationFile->addImport($this->testServicesYml);

        $projectYamlDao->saveProjectConfigFile($projectConfigurationFile);

        $this->expectException(ServiceNotFoundException::class);
        $this->get('test_service');
    }

    public function testResetCacheWorks(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->get('test_service');

        $projectYamlDao = $this->getProjectYmlDao();

        $projectConfigurationFile = $projectYamlDao->loadProjectConfigFile();
        $projectConfigurationFile->addImport($this->testServicesYml);

        $projectYamlDao->saveProjectConfigFile($projectConfigurationFile);

        ContainerFactory::resetContainer();

        $this->assertIsObject(
            $this->get('test_service')
        );
    }

    /**
     * Checks that the cachefile has been created
     */
    public function testCacheIsCreated(): void
    {
        $this->assertFileExists($this->getCacheFilePath());
    }

    public function testEventDispatcher(): void
    {
        $this->assertInstanceOf(
            EventDispatcher::class,
            $this->get(EventDispatcherInterface::class)
        );
    }

    private function getCacheFilePath(): string
    {
        return $this
            ->container
            ->get(BasicContextInterface::class)
            ->getContainerCacheFilePath(1);
    }

    private function cleanUpGeneratedServices(): void
    {
        $projectYamlDao = $this->getProjectYmlDao();

        $projectConfigurationFile = $projectYamlDao->loadProjectConfigFile();
        $projectConfigurationFile->removeImport($this->testServicesYml);
        $projectYamlDao->saveProjectConfigFile($projectConfigurationFile);
    }

    private function getProjectYmlDao(): ProjectYamlDaoInterface
    {
        return new ProjectYamlDao(
            $this->get(ContextInterface::class),
            $this->get('oxid_esales.symfony.file_system')
        );
    }

    private function generateShopConfigurationStubForVfs(): void
    {
        vfsStream::setup(
            'configuration',
            null,
            [
                'shops' => [
                    '1' => [],
                ],
            ]
        );
    }
}
