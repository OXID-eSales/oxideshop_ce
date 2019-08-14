<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance\ContainerWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Utility\GraphQlTypePass;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
  *
 * @internal
 */
class ContainerFactory
{
    /**
     * @var self
     */
    private static $instance = null;

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
        $cacheFilePath = $this->getCacheFilePath();

        if (file_exists($cacheFilePath)) {
            $this->loadContainerFromCache($cacheFilePath);
        } else {
            $this->createAndCompileSymfonyContainer();
            $this->saveContainerToCache($cacheFilePath);
            $this->loadContainerFromCache($cacheFilePath);
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
        $this->symfonyContainer->addCompilerPass(new GraphQlTypePass());
        $loader = new YamlFileLoader($this->symfonyContainer, new FileLocator(__DIR__));
        $loader->load('services.yaml');
        $this->symfonyContainer->compile();
    }

    /**
     * Dumps the compiled container to the cachefile.
     *
     * @param string $cachefile
     */
    private function saveContainerToCache($cachefile)
    {
        $dumper = new PhpDumper($this->symfonyContainer);
        file_put_contents($cachefile, $dumper->dump());
    }

    /**
     * @todo: move it to another place.
     *
     * @return string
     */
    private function getCacheFilePath()
    {
        $compileDir = Registry::getConfig()->getConfigParam('sCompileDir');

        return $compileDir . '/containercache.php';
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
