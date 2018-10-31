<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Utility\Context;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @internal
 */
class ContainerBuilder
{
    /**
     * @var array
     */
    private $serviceFilePaths = [
        'services.yaml',
    ];

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());
        $this->loadServiceFiles($symfonyContainer);
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    /**
     * @param SymfonyContainerBuilder $symfonyContainer
     */
    private function loadServiceFiles(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->serviceFilePaths as $path) {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator(__DIR__));
            $loader->load($path);
        }
    }

    /**
     * Loads a 'project.yaml' file if it can be found in the shop directory.
     *
     * @param SymfonyContainerBuilder $symfonyContainer
     *
     * @return void
     */
    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer)
    {

        if (! file_exists($this->getShopSourcePath() .
                          DIRECTORY_SEPARATOR . ProjectYamlDaoInterface::PROJECT_FILE_NAME)) {
            return;
        }
        $this->cleanupProjectYaml();
        $loader = new YamlFileLoader($symfonyContainer, new FileLocator($this->getShopSourcePath()));
        $loader->load(ProjectYamlDaoInterface::PROJECT_FILE_NAME);
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml()
    {
        $context = new Context(Registry::getConfig());
        $projectYamlDao = new ProjectYamlDao($context);
        $yamlImportService = new ProjectYamlImportService($projectYamlDao);
        $yamlImportService->removeNonExistingImports();
    }

    /**
     * @return string
     */
    private function getShopSourcePath()
    {
        return Registry::getConfig()->getConfigParam('sShopDir');
    }
}
