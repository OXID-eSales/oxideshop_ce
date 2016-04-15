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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\Eshop\Core\Module;

use oxException;
use OxidEsales\Eshop\Core\FileSystem\FileSystem;

/**
 * Class ModuleTemplatePathFormatter forms path to module template.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ModuleTemplatePathCalculator
{
    /**
     * Path to modules directory inside the shop.
     *
     * @var string
     */
    protected $modulesPath = '';

    /**
     * @param string $modulesPath
     */
    public function setModulesPath($modulesPath)
    {
        $this->modulesPath = $modulesPath;
    }

    /**
     * @return string
     */
    protected function getModulesPath()
    {
        return $this->modulesPath;
    }

    /**
     * @return oxConfig
     */
    protected function getConfig()
    {
        return oxNew('oxConfig');
    }

    /**
     * @return oxModuleList
     */
    protected function getModuleList()
    {
        return oxNew('oxModuleList');
    }

    /**
     * @return FileSystem
     */
    protected function getFileSystem()
    {
        return oxNew(FileSystem::class);
    }

    /**
     * Finds the template by name in modules
     *
     * @param string $templateName
     *
     * @return string
     *
     * @throws oxException
     */
    public function calculateModuleTemplatePath($templateName)
    {
        $config = $this->getConfig();
        $moduleList = $this->getModuleList();

        $moduleTemplates = $config->getConfigParam('aModuleTemplates');
        $activeModules = $moduleList->getActiveModuleInfo();
        $finalTemplatePath = '';

        if (is_array($moduleTemplates) && is_array($activeModules)) {
            foreach ($moduleTemplates as $sModuleId => $aTemplates) {

                // check if module is active
                if (isset($activeModules[$sModuleId])) {
                    $foundTemplate = null;
                    $fileSystem = $this->getFileSystem();

                    // check if template for our active themes exists
                    if ($activeThemes = $config->getActiveThemesList()) {
                        foreach ($activeThemes as $oneActiveThemeId) {
                            if (isset($aTemplates[$oneActiveThemeId], $aTemplates[$oneActiveThemeId][$templateName])) {
                                $foundTemplate = $fileSystem->combinePaths($this->getModulesPath(), $aTemplates[$oneActiveThemeId][$templateName]);
                            }
                        }
                    }

                    // if not found in theme specific configurations
                    if (!$foundTemplate && isset($aTemplates[$templateName])) {
                        $foundTemplate = $fileSystem->combinePaths($this->getModulesPath(), $aTemplates[$templateName]);
                    }

                    if ($foundTemplate) {
                        if ($fileSystem->isReadable($foundTemplate)) {
                            $finalTemplatePath = $foundTemplate;
                            break;
                        } else {
                            throw oxNew('oxException', sprintf('Cannot find template file "%s".', $foundTemplate));
                        }
                    }
                }
            }
        }

        if (!$finalTemplatePath) {
            throw oxNew('oxException', sprintf('Cannot find template "%s" in modules configuration.', $templateName));
        }

        return $finalTemplatePath;
    }
}
