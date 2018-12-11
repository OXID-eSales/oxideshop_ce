<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Console\ConsoleCommandPass;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Application\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Utility\FactsContext;
use OxidEsales\EshopCommunity\Internal\Utility\FactsContextInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
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
     * @var FactsContextInterface
     */
    private $context;

    /**
     * @param FactsContextInterface $context
     */
    public function __construct(FactsContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());
        $symfonyContainer->addCompilerPass(new ConsoleCommandPass());
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
        $loader = new YamlFileLoader(
            $symfonyContainer,
            new FileLocator(Path::join($this->context->getCommunityEditionSourcePath(), 'Internal/Application'))
        );
        $loader->load('services.yaml');
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
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator($this->context->getSourcePath()));
            $loader->load(ProjectYamlDao::PROJECT_FILE_NAME);
        } catch (FileLocatorFileNotFoundException $exception) {
            // In case project file not found, do nothing.
        }
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml()
    {
        $projectYamlDao = new ProjectYamlDao(new FactsContext());
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
            FactsContext::COMMUNITY_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
            ],
            FactsContext::PROFESSIONAL_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
            ],
            FactsContext::ENTERPRISE_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
                $this->context->getEnterpriseEditionRootPath(),
            ],
        ];

        return $allEditionPaths[$this->context->getEdition()];
    }
}
