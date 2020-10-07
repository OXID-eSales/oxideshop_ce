<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Traits;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Module\ModuleVariablesLocator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Webmozart\PathUtil\Path;

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
        $this->removeTmpDir();
        $this->resetRegistry();
        $this->removeClassCaches();
    }

    private function removeTmpDir()
    {
        $basicContext = new BasicContext();
        $tmpDir = Path::join($basicContext->getSourcePath(), 'tmp');
        $this->rrmdir($tmpDir);
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

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $files = glob($dir.'/*'); // get all file names
            foreach($files as $file){ // iterate files
                if (is_dir($file)) {
                    $this->rrmdir($file);
                }
                if(is_file($file)) {
                    unlink($file); // delete file
                }
            }

        }
    }
}