<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\FileSystem\FileSystem;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Theme;

/**
 * Forms path to module template.
 *
 * @deprecated v6.6.0
 * @see        'Internal\Framework\Module' package
 *
 * @internal Do not make a module extension for this class.
 */
class ModuleTemplatePathCalculator
{
    /** @var string Path to modules directory inside the shop. */
    private $modulesPath = '';
    /** @var Theme */
    private $theme;
    /** @var FileSystem */
    private $fileSystem;
    /** @var array */
    private array $activeThemes;

    public function __construct($moduleList = null, $theme = null, $fileSystem = null)
    {
        $this->theme = $theme ?? oxNew(Theme::class);
        $this->fileSystem = $fileSystem ?? oxNew(FileSystem::class);
    }

    /** @param string $modulesPath */
    public function setModulesPath($modulesPath)
    {
        $this->modulesPath = $modulesPath;
    }

    /** @return string */
    protected function getModulesPath()
    {
        return $this->modulesPath;
    }

    /**
     * Finds the template by name in modules
     * @param string $templateName
     * @return string
     */
    public function calculateModuleTemplatePath($templateName)
    {
        $moduleTemplates = Registry::getConfig()->getConfigParam('aModuleTemplates');
        if (!is_array($moduleTemplates)) {
            throw oxNew('oxException', sprintf('Cannot find template "%s" in modules configuration.', $templateName));
        }
        $this->activeThemes = array_reverse(
            (array)$this->theme->getActiveThemesList()
        );
        foreach ($moduleTemplates as $moduleId => $templatesConfiguration) {
            if (!$this->moduleIsActive($moduleId) || !$this->moduleExtendsTemplate($templatesConfiguration, $templateName)) {
                continue;
            }
            $moduleTemplatePath = $this->fileSystem->combinePaths(
                $this->getModulesPath(),
                $this->geModuleTemplateExtension($templatesConfiguration, $templateName)
            );
            if (!$this->fileSystem->isReadable($moduleTemplatePath)) {
                throw oxNew('oxException', sprintf('Cannot find template file "%s".', $moduleTemplatePath));
            }
            return $moduleTemplatePath;
        }
        throw oxNew('oxException', sprintf('Cannot find template "%s" in modules configuration.', $templateName));
    }

    private function moduleIsActive(string $moduleId): bool
    {
        $activeModules = (array)Registry::getConfig()->getConfigParam('aModulePaths');
        return isset($activeModules[$moduleId]);
    }

    private function moduleExtendsTemplate(array $templatesConfiguration, string $templateName): bool
    {
        foreach ($this->activeThemes as $themeId) {
            if (isset($templatesConfiguration[$themeId][$templateName])) {
                return true;
            }
        }
        return isset($templatesConfiguration[$templateName]);
    }

    private function geModuleTemplateExtension(array $templatesConfiguration, string $templateName): string
    {
        foreach ($this->activeThemes as $themeId) {
            if (isset($templatesConfiguration[$themeId][$templateName])) {
                return $templatesConfiguration[$themeId][$templateName];
            }
        }
        return $templatesConfiguration[$templateName];
    }
}
