<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;

/**
 * Find the translation files in given module.
 *
 * @package  OxidEsales\EshopCommunity\Core\Module
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
        $config = Registry::getConfig();
        $fullPath = $config->getModulesDir() . $modulePath;

        if (file_exists($fullPath . '/Application/')) {
            $fullPath .= '/Application';
        }
        $fullPath .= ($admin) ? '/views/admin/' : '/translations/';
        $fullPath .= $language;

        return $fullPath;
    }
}
