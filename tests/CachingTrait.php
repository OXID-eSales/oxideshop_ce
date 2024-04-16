<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use ReflectionClass;
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
    public function resetRegistry(): void
    {
        $configFile = Registry::get(ConfigFile::class);
        $this->removeClassCache(Registry::class, 'instances', []);
        Registry::set(ConfigFile::class, $configFile);
    }

    public function cleanupCaching(): void
    {
        $this->cleanUpCompilationDirectory();
        $this->resetRegistry();
        $this->removeClassCaches();
    }

    private function cleanUpCompilationDirectory(): void
    {
        $basicContext = new BasicContext();
        $this->cleanUpDirectory($basicContext->getCacheDirectory());
    }

    private function removeClassCaches(): void
    {
        $this->removeClassCache(ModuleVariablesLocator::class, 'moduleVariables', []);
        $this->removeClassCache(DatabaseProvider::class, 'db', null);
    }

    private function removeClassCache(string $class, string $property, $default): void
    {
        $reflectionClass = new ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($default);
    }

    private function cleanUpDirectory($directory): void
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
