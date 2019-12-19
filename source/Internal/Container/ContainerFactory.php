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
     * ContainerFactory constructor.
     *
     * Make the constructor private
     */
    private function __construct()
    {
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

    public function getContainer(?BasicContextInterface $context = null): ContainerInterface
    {
        if ($this->container === null) {
            $this->container = $this->initializeContainer(
                $context ?? new BasicContext()
            );
        }

        return $this->container;
    }

    private function initializeContainer(BasicContextInterface $context): ContainerInterface
    {
        startProfile('ContainerFactory::initializeContainer()');

        $containerBuilder = (new ContainerBuilder())
            ->setDumpDir($context->getContainerCacheFilePath())
            ->setGenerator(new YamlGenerator());

        $paths = $this->getEditionServicePaths($context);
        foreach ($paths as $path) {
            $containerBuilder->addPath($path)
                             ->addFile('services.yaml');
        }

        $containerBuilder->addPass(new RegisterListenersPass(EventDispatcherInterface::class))
                         ->addPass(new AddConsoleCommandPass());

        if (is_file($context->getGeneratedServicesFilePath())) {
            $containerBuilder->addFile($context->getGeneratedServicesFilePath());
        }

        if (is_file($context->getConfigurableServicesFilePath())) {
            $containerBuilder->addFile($context->getConfigurableServicesFilePath());
        }

        $container = $containerBuilder->getContainer();

        stopProfile('ContainerFactory::initializeContainer()');
        return $container;
    }

    /**
     * @return array<int, string>
     */
    private function getEditionServicePaths(BasicContextInterface $context): array
    {
        $paths = [
            $context->getCommunityEditionSourcePath(),
            $context->getProfessionalEditionRootPath(),
            $context->getEnterpriseEditionRootPath()
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
        $containerFiles = \glob((new BasicContext())->getContainerCacheFilePath() . '/Container*php*');
        array_walk(
            $containerFiles,
            function ($file) {
                unlink($file);
            }
        );
        self::$instance = null;
    }
}
