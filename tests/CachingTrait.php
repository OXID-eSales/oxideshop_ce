<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Filesystem;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

trait CachingTrait
{
    /**
     * The registry is complicated, because it needs the ConfigFile
     * set. So just cleaning the $instances cache does not work.
     */
    public function resetRegistry()
    {
        $configFile = Registry::get(ConfigFile::class);
        $this->removeClassCache(Registry::class, 'instances', []);
        Registry::set(ConfigFile::class, $configFile);
    }

    public function cleanupCaching()
    {
        $this->cleanUpCompilationDirectory();
        $this->resetRegistry();
        $this->removeClassCaches();
    }

    private function cleanUpCompilationDirectory()
    {
        $basicContext = new BasicContext();
        $this->cleanUpDirectory($basicContext->getCacheDirectory());
    }

    private function removeClassCaches()
    {
        // Probably there are a lot more caches that need to be removed
        // Add them here when you find them
        $this->removeClassCache(ModuleVariablesLocator::class, 'moduleVariables', []);
        $this->removeClassCache(DatabaseProvider::class, 'db', null);
    }

    private function removeClassCache(string $class, string $property, $default)
    {
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($default);
    }

    private function cleanUpDirectory($directory)
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($directory)) {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            $fileSystem->remove($recursiveIterator);
        }
    }
}