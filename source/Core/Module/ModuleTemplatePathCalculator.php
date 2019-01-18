<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use oxException;
use OxidEsales\Eshop\Core\FileSystem\FileSystem;

/**
 * Forms path to module template.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTemplatePathCalculator
{
    /** @var string Path to modules directory inside the shop. */
    private $modulesPath = '';

    /** @var \OxidEsales\Eshop\Core\Theme */
    private $theme;

    /** @var \OxidEsales\Eshop\Core\Module\ModuleList */
    private $moduleList;

    /** @var FileSystem */
    private $fileSystem;

    /**
     * Sets required dependencies
     *
     * @param \OxidEsales\Eshop\Core\Module\ModuleList $moduleList
     * @param \OxidEsales\Eshop\Core\Theme             $theme
     * @param FileSystem                               $fileSystem
     */
    public function __construct($moduleList = null, $theme = null, $fileSystem = null)
    {
        if (is_null($moduleList)) {
            $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        }
        if (is_null($theme)) {
            $theme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
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
     * @return \OxidEsales\Eshop\Core\Theme
     */
    protected function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Module\ModuleList
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
