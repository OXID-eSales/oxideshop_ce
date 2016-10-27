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

namespace OxidEsales\EshopCommunity\Core\Module;

use oxException;
use OxidEsales\Eshop\Core\FileSystem\FileSystem;
use oxModuleList;
use oxTheme;

/**
 * Forms path to module template.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTemplatePathCalculator
{
    /** @var string Path to modules directory inside the shop. */
    private $modulesPath = '';

    /** @var oxTheme */
    private $theme;

    /** @var oxModuleList */
    private $moduleList;

    /** @var FileSystem */
    private $fileSystem;

    /**
     * Sets required dependencies
     *
     * @param oxModuleList $moduleList
     * @param oxTheme      $theme
     * @param FileSystem   $fileSystem
     */
    public function __construct($moduleList = null, $theme = null, $fileSystem = null)
    {
        if (is_null($moduleList)) {
            $moduleList = oxNew('oxModuleList');
        }
        if (is_null($theme)) {
            $theme = oxNew('oxTheme');
        }
        if (is_null($fileSystem)) {
            $fileSystem = oxNew(FileSystem::class);
        }

        $this->theme = $theme;
        $this->moduleList = $moduleList;
        $this->fileSystem = $fileSystem;
    }

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
        $moduleList = $this->getModuleList();
        $theme = $this->getTheme();

        $moduleTemplates = $moduleList->getModuleTemplates();
        $activeModules = $moduleList->getActiveModuleInfo();
        $finalTemplatePath = '';

        if (is_array($moduleTemplates) && is_array($activeModules)) {
            foreach ($moduleTemplates as $sModuleId => $aTemplates) {
                // check if module is active
                if (isset($activeModules[$sModuleId])) {
                    $foundTemplate = null;
                    $fileSystem = $this->getFileSystem();

                    // check if template for our active themes exists
                    foreach ((array) $theme->getActiveThemesList() as $oneActiveThemeId) {
                        if (isset($aTemplates[$oneActiveThemeId], $aTemplates[$oneActiveThemeId][$templateName])) {
                            $foundTemplate = $fileSystem->combinePaths($this->getModulesPath(), $aTemplates[$oneActiveThemeId][$templateName]);
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

    /**
     * @return oxTheme
     */
    protected function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return oxModuleList
     */
    protected function getModuleList()
    {
        return $this->moduleList;
    }

    /**
     * @return FileSystem
     */
    protected function getFileSystem()
    {
        return $this->fileSystem;
    }
}
