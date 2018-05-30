<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance\ContainerWrapper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ContainerFactory
 *
 * Class to generate a PSR11 complient DI container
 *
 * @package OxidEsales\EshopCommunity\Internal\Application
 */
class ContainerFactory
{
    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @var string
     */
    public static $containerCache = __DIR__ .'/containercache.php';

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $symfonyContainer = null;

    /**
     * ContainerFactory constructor.
     *
     * Make the constructor private
     */
    private function __construct()
    {
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if ($this->symfonyContainer === null) {
            $this->initializeContainer();
        }

        return new ContainerWrapper($this->symfonyContainer);
    }

    /**
     * Loads container from cache if available, otherwise
     * create the container from scratch.
     */
    private function initializeContainer()
    {
        if (file_exists(ContainerFactory::$containerCache)) {
            $this->loadContainerFromCache(ContainerFactory::$containerCache);
        } else {
            $this->createAndCompileSymfonyContainer();
            $this->saveContainerToCache(ContainerFactory::$containerCache);
        }
    }

    /**
     * @param string $cachefile
     */
    private function loadContainerFromCache($cachefile)
    {
        include_once $cachefile;
        $this->symfonyContainer = new \ProjectServiceContainer();
    }

    /**
     * Builds the container from services.yaml and compiles it
     */
    private function createAndCompileSymfonyContainer()
    {
        $this->symfonyContainer = new ContainerBuilder();
        $loader = new YamlFileLoader($this->symfonyContainer, new FileLocator(__DIR__));
        $loader->load('services.yaml');
        $this->symfonyContainer->compile();
    }

    /**
     * @param string $cachefile
     *
     * Dumps the compiled container to the cachefile
     */
    private function saveContainerToCache($cachefile)
    {
        $dumper = new PhpDumper($this->symfonyContainer);
        file_put_contents($cachefile, $dumper->dump());
    }

    /**
     * @return ContainerFactory
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ContainerFactory();
        }
        return self::$instance;
    }
}
