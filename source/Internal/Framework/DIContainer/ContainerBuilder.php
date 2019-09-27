<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ContainerBuilder
{

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @param BasicContextInterface $context
     */
    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return SymfonyContainerBuilder
     * @throws \Exception
     */
    public function getContainer(): SymfonyContainerBuilder
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass(EventDispatcherInterface::class));
        $symfonyContainer->addCompilerPass(new AddConsoleCommandPass());
        $this->loadEditionServices($symfonyContainer);
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    /**
     * Loads a 'project.yaml' file if it can be found in the shop directory.
     *
     * @param SymfonyContainerBuilder $symfonyContainer
     * @throws \Exception
     */
    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer)
    {
        $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
        try {
            $this->cleanupProjectYaml();
            $loader->load($this->context->getGeneratedServicesFilePath());
        } catch (FileLocatorFileNotFoundException $exception) {
            // In case generated services file not found, do nothing.
        }
        try {
            $loader->load($this->context->getConfigurableServicesFilePath());
        } catch (FileLocatorFileNotFoundException $exception) {
            // In case manually created services file not found, do nothing.
        }
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml()
    {
        $projectYamlDao = new ProjectYamlDao($this->context, new Filesystem());
        $yamlImportService = new ProjectYamlImportService($projectYamlDao);
        $yamlImportService->removeNonExistingImports();
    }

    /**
     * @param SymfonyContainerBuilder $symfonyContainer
     * @throws \Exception
     */
    private function loadEditionServices(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->getEditionsRootPaths() as $path) {
            $servicesLoader = new YamlFileLoader($symfonyContainer, new FileLocator($path));
            $servicesLoader->load('Internal/services.yaml');
        }
    }

    /**
     * @return array
     */
    private function getEditionsRootPaths(): array
    {
        $allEditionPaths = [
            BasicContext::COMMUNITY_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
            ],
            BasicContext::PROFESSIONAL_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
            ],
            BasicContext::ENTERPRISE_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
                $this->context->getEnterpriseEditionRootPath(),
            ],
        ];

        return $allEditionPaths[$this->context->getEdition()];
    }
}
