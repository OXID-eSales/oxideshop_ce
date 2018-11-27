<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Application\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Utility\Context;
use OxidEsales\Facts\Facts;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Webmozart\PathUtil\Path;

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
     * @var Facts
     */
    private $facts;

    /**
     * @param Facts $facts
     */
    public function __construct(Facts $facts)
    {
        $this->facts = $facts;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());
        $this->loadServiceFiles($symfonyContainer);
        if ($this->facts->isProfessional()) {
            $this->loadEditionServices($symfonyContainer, $this->facts->getProfessionalEditionRootPath());
        }
        if ($this->facts->isEnterprise()) {
            $this->loadEditionServices($symfonyContainer, $this->facts->getProfessionalEditionRootPath());
            $this->loadEditionServices($symfonyContainer, $this->facts->getEnterpriseEditionRootPath());
        }
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    /**
     * @param SymfonyContainerBuilder $symfonyContainer
     */
    private function loadServiceFiles(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->serviceFilePaths as $path) {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator(Path::join($this->facts->getCommunityEditionSourcePath(), 'Internal/Application')));
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

        if (! file_exists($this->facts->getSourcePath() .
                          DIRECTORY_SEPARATOR . ProjectYamlDao::PROJECT_FILE_NAME)) {
            return;
        }
        $this->cleanupProjectYaml();
        $loader = new YamlFileLoader($symfonyContainer, new FileLocator($this->facts->getSourcePath()));
        $loader->load(ProjectYamlDao::PROJECT_FILE_NAME);
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
     * @param SymfonyContainerBuilder $symfonyContainer
     * @param string                  $editionPath
     */
    private function loadEditionServices(SymfonyContainerBuilder $symfonyContainer, string $editionPath)
    {
        $servicesLoader = new YamlFileLoader($symfonyContainer, new FileLocator($editionPath));
        $servicesLoader->load('Internal/Application/services.yaml');
    }
}
