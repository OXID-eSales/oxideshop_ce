<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\OxidKernel;
use Psr\Container\ContainerInterface;

/**
 * @deprecated use OxidEsales\EshopCommunity\Core\Di\ContainerFacade
 */
class ContainerFactory implements ContainerProviderInterface
{
    private static $instance;
    private ?OxidKernel $kernel = null;

    private function __construct()
    {
    }

    public static function get(): ContainerInterface
    {
        return self::getInstance()->getContainer();
    }

    public function getContainer(): ContainerInterface
    {
        $customContainerProvider = getenv('OXID_CONTAINER_PROVIDER');
        if ($customContainerProvider) {
           return $customContainerProvider::get();
        }

        $kernel = $this->getKernel();
        $kernel->boot();
        return $kernel->getContainer();
    }

    public function getKernel(): OxidKernel
    {
        if ($this->kernel === null) {
            $debug = filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN);
            $env = getenv('OXID_ENV') ?: 'prod';
            $this->kernel = new OxidKernel($env, $debug);
        }
        return $this->kernel;
    }

    public static function getInstance(): ContainerFactory
    {
        if (self::$instance === null) {
            self::$instance = new ContainerFactory();
        }
        return self::$instance;
    }

    public static function resetContainer(): void
    {
        $customContainerProvider = getenv('OXID_CONTAINER_PROVIDER');
        if ($customContainerProvider) {
            $customContainerProvider::resetContainer();
        }

        $factory = self::getInstance();
        $factory->kernel?->shutdown();
        $factory->kernel = null;
        self::$instance = null;
    }
}
