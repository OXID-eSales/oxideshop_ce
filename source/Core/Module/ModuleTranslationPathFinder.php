<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use Psr\Container\ContainerInterface;

/**
 * Find the translation files in given module.
 *
 * @package  OxidEsales\EshopCommunity\Core\Module
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTranslationPathFinder
{
    /**
     * Find the full path of the translation in the given module.
     *
     * @param string $language   The language short form. (e.g. 'de')
     * @param bool   $admin      Are we searching for the admin files?
     * @param string $modulePath The relative (to the module directory) path to the module, in which we want to find the translations file.
     *
     * @return string
     */
    public function findTranslationPath($language, $admin, $modulePath)
    {
        $fullPath = $this->getModulesDirectory() . $modulePath;

        if ($this->hasUppercaseApplicationDirectory($fullPath)) {
            $fullPath .= DIRECTORY_SEPARATOR . 'Application';
        } else {
            if ($this->hasLowercaseApplicationDirectory($fullPath)) {
                $fullPath .= DIRECTORY_SEPARATOR . 'application';
            }
        }
        $adminThemeName = $this->getContainer()->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $languageDirectory = ($admin) ? 'views' . DIRECTORY_SEPARATOR .  $adminThemeName : 'translations';
        $fullPath .= DIRECTORY_SEPARATOR . $languageDirectory;
        $fullPath .= DIRECTORY_SEPARATOR . $language;

        return $fullPath;
    }

    /**
     * Getter for the modules directory.
     *
     * @return string The modules directory.
     */
    protected function getModulesDirectory()
    {
        $config = Registry::getConfig();

        return $config->getModulesDir();
    }

    /**
     * Check, if the module directory has an folder called 'Application'.
     *
     * @param string $pathToModule The path to the module to check.
     *
     * @return bool Has the given module a folder Application?
     */
    protected function hasUppercaseApplicationDirectory($pathToModule)
    {
        return $this->directoryExists($pathToModule . '/Application/');
    }

    /**
     * Check, if the module directory has an folder called 'application'.
     *
     * @param string $pathToModule The path to the module to check.
     *
     * @return bool Has the given module a folder 'application'?
     */
    protected function hasLowercaseApplicationDirectory($pathToModule)
    {
        return $this->directoryExists($pathToModule . '/application/');
    }

    /**
     * Does the given path points to an existing directory?
     *
     * @param string $path The path we want to check, if itexists.
     *
     * @return bool Does the given path points to an existing directory?
     */
    protected function directoryExists($path)
    {
        return file_exists($path);
    }

    /**
     * @internal
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
