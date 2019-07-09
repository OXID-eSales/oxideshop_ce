<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Application\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Application\Utility\GraphQlTypePass;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class ContainerBuilder
{

    /**
     * @var BasicContextInterface
     */
    private $context;

    private $serviceFilePaths = [
        'services.yaml', '..' . DIRECTORY_SEPARATOR . 'services.yaml'
    ];

    /**
     * @param BasicContextInterface $context
     */
    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return SymfonyContainerBuilder
     */
    public function getContainer(): SymfonyContainerBuilder
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass(EventDispatcherInterface::class));
        $symfonyContainer->addCompilerPass(new GraphQlTypePass());
        $symfonyContainer->addCompilerPass(new AddConsoleCommandPass());
        $this->loadServiceFiles($symfonyContainer);
        $this->loadEditionServices($symfonyContainer);
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    /**
     * @param SymfonyContainerBuilder $symfonyContainer
     */
    private function loadServiceFiles(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->serviceFilePaths as $partialPath) {
            $fullPath = Path::join($this->context->getCommunityEditionSourcePath(), 'Internal/Application/' . $partialPath);
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator(Path::getDirectory($fullPath)));
            $loader->load(Path::getFilename($fullPath));
        }
    }

    /**
     * Loads a 'project.yaml' file if it can be found in the shop directory.
     *
     * @param SymfonyContainerBuilder $symfonyContainer
     */
    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer)
    {
        try {
            $this->cleanupProjectYaml();
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
            $loader->load($this->context->getGeneratedServicesFilePath());
        } catch (FileLocatorFileNotFoundException $exception) {
            // In case project file not found, do nothing.
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
     */
    private function loadEditionServices(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->getEditionsRootPaths() as $path) {
            $servicesLoader = new YamlFileLoader($symfonyContainer, new FileLocator($path));
            $servicesLoader->load('Internal/Application/services.yaml');
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
