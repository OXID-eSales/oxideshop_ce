<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use Lcobucci\DependencyInjection\ContainerBuilder;
use Lcobucci\DependencyInjection\Generators\Yaml as YamlGenerator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Facts\Facts;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

class ContainerFactory
{
    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * BasicContextInterface
     */
    private $context = null;

    /**
     * ContainerFactory constructor.
     *
     * Make the constructor private
     */
    private function __construct()
    {
        $this->context = new BasicContext();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new ContainerFactory();
        }
        return self::$instance;
    }

    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $this->container = $this->initializeContainer();
        }

        return $this->container;
    }

    private function initializeContainer(): ContainerInterface
    {
        startProfile('ContainerFactory::initializeContainer()');

        $containerBuilder = (new ContainerBuilder())
            ->setDumpDir($this->context->getContainerCacheFilePath())
            ->setGenerator(new YamlGenerator());

        $paths = $this->getEditionServicePaths();
        foreach ($paths as $path) {
            $containerBuilder->addPath($path)
                             ->addFile('services.yaml');
        }

        $containerBuilder->addPass(new RegisterListenersPass(EventDispatcherInterface::class))
                         ->addPass(new AddConsoleCommandPass());

        if (is_file($this->context->getGeneratedServicesFilePath())) {
            $containerBuilder->addFile($this->context->getGeneratedServicesFilePath());
        }

        if (is_file($this->context->getConfigurableServicesFilePath())) {
            $containerBuilder->addFile($this->context->getConfigurableServicesFilePath());
        }

        $container = $containerBuilder->getContainer();

        stopProfile('ContainerFactory::initializeContainer()');
        return $container;
    }

    /**
     * @return array<int, string>
     */
    private function getEditionServicePaths(): array
    {
        $paths = [
            $this->context->getCommunityEditionSourcePath(),
            $this->context->getProfessionalEditionRootPath(),
            $this->context->getEnterpriseEditionRootPath()
        ];
        $paths = array_filter($paths, 'is_dir');
        $paths = array_map(
            function($path) {
                return $path . '/Internal/';
            },
            $paths
        );
        return $paths;
    }

    /**
     * Forces reload of the ContainerFactory on next request.
     */
    public static function resetContainer()
    {
        $containerFiles = \glob(self::CONTAINER_CACHE_DIR . 'Container*php*');
        array_walk(
            $containerFiles,
            'unlink'
        );
        self::$instance = null;
    }
}
