<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 22.05.18
 * Time: 15:03
 */

namespace OxidEsales\EshopCommunity\Internal\Application;


use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContainerFactory
{

    /**
     * @var string
     */
    private static $containerCache = __DIR__ .'/containercache.php';

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private static $symfonyContainer = null;

    /**
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        if (ContainerFactory::$symfonyContainer === null) {
            ContainerFactory::initializeContainer();
        }

        return new ContainerWrapper(ContainerFactory::$symfonyContainer);
    }

    private static function initializeContainer() {

        if (file_exists(ContainerFactory::$containerCache)) {
            ContainerFactory::loadContainerFromCache();
        } else {
            ContainerFactory::createAndCompileSymfonyContainer();
            ContainerFactory::saveContainerToCache();
        }

    }

    private static function loadContainerFromCache() {

        require_once ContainerFactory::$containerCache;
        ContainerFactory::$symfonyContainer = new \ProjectServiceContainer();

    }

    private static function createAndCompileSymfonyContainer() {

        ContainerFactory::$symfonyContainer = new ContainerBuilder();
        $loader = new YamlFileLoader(ContainerFactory::$symfonyContainer, new FileLocator(__DIR__));
        $loader->load('services.yaml');
        ContainerFactory::$symfonyContainer->compile();

    }

    private static function saveContainerToCache() {

        $dumper = new PhpDumper(ContainerFactory::$symfonyContainer);
        file_put_contents(ContainerFactory::$containerCache, $dumper->dump());

    }

}