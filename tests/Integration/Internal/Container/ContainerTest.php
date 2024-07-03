<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class ContainerTest extends IntegrationTestCase
{
    public function testCompileContainerWillCreateCacheFile(): void
    {
        $cacheFile = $this->getContainer()->get(BasicContextInterface::class)->getContainerCacheFilePath(
            $this->getContainer()->get(BasicContextInterface::class)->getDefaultShopId()
        );
        $this->assertFileExists($cacheFile);
        unlink($cacheFile);
        $this->assertFileDoesNotExist($cacheFile);

        ContainerFactory::resetContainer();
        $this->getContainer()->get(BasicContextInterface::class);

        $this->assertFileExists($cacheFile);
    }

    public function testResetCacheWorks(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        ContainerFactory::getInstance()->getContainer()->get('test_service');

        $projectYamlDao = new ProjectYamlDao(
            $this->getContainer()->get(ContextInterface::class),
            $this->getContainer()->get('oxid_esales.symfony.file_system')
        );

        $projectConfigurationFile = $projectYamlDao->loadProjectConfigFile();
        $projectConfigurationFile->addImport(__DIR__ . '/Fixtures/Project/services.yaml');

        $projectYamlDao->saveProjectConfigFile($projectConfigurationFile);

        ContainerFactory::resetContainer();

        $this->assertIsObject(
            ContainerFactory::getInstance()->getContainer()->get('test_service')
        );
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
