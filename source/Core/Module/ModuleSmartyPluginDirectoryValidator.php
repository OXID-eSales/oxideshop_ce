<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Exception\ModuleValidationException   as EshopModuleValidationException;
use OxidEsales\Eshop\Core\Module\ModuleSmartyPluginDirectories  as EshopModuleSmartyPluginDirectories;

/**
 * Class ModuleSmartyPluginDirectoryValidator
 *
 * @deprecated since v6.4.0 (2019-05-24); Validation was moved to Internal\Framework\Module package and will be executed during the module activation.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 *
 */
class ModuleSmartyPluginDirectoryValidator
{
    /**
     * @param EshopModuleSmartyPluginDirectories $moduleSmartyPluginDirectories
     *
     * @throws EshopModuleValidationException
     */
    public function validate(EshopModuleSmartyPluginDirectories $moduleSmartyPluginDirectories)
    {
        $directories = $moduleSmartyPluginDirectories->getWithFullPath();

        foreach ($directories as $directory) {
            if (!$this->doesDirectoryExist($directory)) {
                throw new EshopModuleValidationException('Smarty plugin directory does not exist ' . $directory);
            }
        }
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function doesDirectoryExist($directory)
    {
        return is_dir($directory);
    }
}
