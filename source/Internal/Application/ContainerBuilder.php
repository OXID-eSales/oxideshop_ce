<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Core\Registry;
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
     * Loads a 'project.yaml' file if it can be found in the shop directory
     *
     * @param SymfonyContainerBuilder $symfonyContainer
     *
     */
    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer)
    {

        try {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator($this->getShopSourcePath()));
            $loader->load('project.yaml');
        } catch (\Exception $e) {
            // pass
        }
    }

    private function getShopSourcePath()
    {
        return Registry::getConfig()->getConfigParam('sShopDir');
    }
}
