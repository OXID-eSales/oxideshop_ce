<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;

/**
 * Module class.
 *
 * @deprecated since v6.4.0 (2019-03-22); Use service 'OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface'.
 * @internal Do not make a module extension for this class.
 */
class Module extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Metadata version as defined in metadata.php
     */
    protected $metaDataVersion;

    /**
     * @param mixed $metaDataVersion
     */
    public function setMetaDataVersion($metaDataVersion)
    {
        $this->metaDataVersion = $metaDataVersion;
    }

    /**
     * Modules info array
     *
     * @var array
     */
    protected $_aModule = [];

    /**
     * Defines if module has metadata file or not
     *
     * @var bool
     */
    protected $_blMetadata = false;

    /**
     * Defines if module is registered in metadata or legacy storage
     *
     * @var bool
     */
    protected $_blRegistered = false;

    /**
     * Set passed module data
     *
     * @param array $aModule module data
     */
    public function setModuleData($aModule)
    {
        $this->_aModule = $aModule;
    }

    /**
     * Get the modules metadata array
     *
     * @return  array Module meta data array
     */
    public function getModuleData()
    {
        return $this->_aModule;
    }

    /**
     * Load module info
     *
     * @param string $moduleId
     *
     * @return bool
     */
    public function load($moduleId)
    {
        try {
            $this->_aModule['id'] = $moduleId;
            $moduleConfiguration = ContainerFacade::get(ModuleConfigurationDaoBridgeInterface::class)
                ->get($moduleId);

            $this->_aModule = $this->convertModuleConfigurationToArray($moduleConfiguration);
            $this->_blRegistered = true;
            $this->_blMetadata = true;
            $this->_aModule['active'] = $this->isActive();

            return true;
        } catch (ModuleConfigurationNotFoundException $e) {
            return false;
        }

        return false;
    }

    /**
     * Get module description
     *
     * @return string
     */
    public function getDescription()
    {
        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage();

        return $this->getInfo("description", $iLang);
    }

    /**
     * Get module title
     *
     * @return string
     */
    public function getTitle()
    {
        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage();

        return $this->getInfo("title", $iLang);
    }

    /**
     * Get module ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_aModule['id'];
    }

    /**
     * Returns array of module extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        $moduleConfiguration = ContainerFacade::get(ModuleConfigurationDaoBridgeInterface::class)
            ->get(
                $this->getId()
            );

        $extensions = [];

        if ($moduleConfiguration->hasClassExtensions()) {
            foreach ($moduleConfiguration->getClassExtensions() as $extension) {
                $extensions[$extension->getShopClassName()] = $extension->getModuleExtensionClassName();
            }
        }

        return $extensions;
    }

    /**
     * Returns associative array of module controller ids and corresponding classes.
     *
     * @return array
     */
    public function getControllers()
    {
        if (isset($this->_aModule['controllers']) && ! is_array($this->_aModule['controllers'])) {
            throw new \InvalidArgumentException('Value for metadata key "controllers" must be an array');
        }

        return isset($this->_aModule['controllers']) ? array_change_key_case($this->_aModule['controllers']) : [];
    }

    /**
     * Get module ID
     *
     * @param string $module extension full path
     *
     * @return string
     */
    public function getIdByPath($module)
    {
        $moduleId = null;
        $moduleFile = $module;
        $moduleId = $this->getModuleIdByClassName($module);
        if (!$moduleId) {
            $modulePaths = $this->getModulePaths();

            if (is_array($modulePaths)) {
                foreach ($modulePaths as $id => $path) {
                    if (strpos($moduleFile, $path . "/") === 0) {
                        $moduleId = $id;
                    }
                }
            }
        }
        if (!$moduleId) {
            $moduleId = substr($moduleFile, 0, strpos($moduleFile, "/"));
        }
        if (!$moduleId) {
            $moduleId = $moduleFile;
        }

        return $moduleId;
    }

    /**
     * Get the module id for a given class name. If there are duplicates, the first module id will be returned.
     *
     * @param string $className
     *
     * @return string
     */
    public function getModuleIdByClassName($className)
    {
        $moduleId = '';

        foreach ($this->getShopConfiguration()->getModuleConfigurations() as $module) {
            if ($module->hasClassExtension($className)) {
                return $module->getId();
            }
        }

        return $moduleId;
    }

    /**
     * Get module info item. If second param is passed, will try
     * to get value according selected language.
     *
     * @param string $sName name of info item to retrieve
     * @param string $iLang language ID
     *
     * @return mixed
     */
    public function getInfo($sName, $iLang = null)
    {
        if (isset($this->_aModule[$sName])) {
            if ($iLang !== null && is_array($this->_aModule[$sName])) {
                $sValue = null;

                $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageAbbr($iLang);

                if (!empty($this->_aModule[$sName])) {
                    if (!empty($this->_aModule[$sName][$sLang])) {
                        $sValue = $this->_aModule[$sName][$sLang];
                    } elseif (!empty($this->_aModule['lang'])) {
                        // trying to get value according default language
                        $sValue = $this->_aModule[$sName][$this->_aModule['lang']];
                    } else {
                        // returning first array value
                        $sValue = reset($this->_aModule[$sName]);
                    }

                    return $sValue;
                }
            } else {
                return $this->_aModule[$sName];
            }
        }
    }

    /**
     * Check if extension is active
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->getId() === null) {
            return false;
        }

        return ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->isActive(
                $this->getId(),
                Registry::getConfig()->getShopId()
            );
    }

    /**
     * Checks if has extend class.
     *
     * @return bool
     */
    public function hasExtendClass()
    {
        return !empty($this->getExtensions());
    }

    /**
     * Checks if module is registered in any way
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->_blRegistered;
    }

    /**
     * Checks if module has metadata
     *
     * @return bool
     */
    public function hasMetadata()
    {
        return $this->_blMetadata;
    }

    /**
     * Get module id's with path
     *
     * @return array
     */
    public function getModulePaths()
    {
        $moduleConfigurations = $this->getInstalledModuleConfigurations();
        $paths = [];
        foreach ($moduleConfigurations as $moduleConfiguration) {
            $paths[$moduleConfiguration->getId()] = $moduleConfiguration->getModuleSource();
        }

        return $paths;
    }

    /**
     * Include data from metadata.php
     *
     * @param string $metadataPath Path to metadata.php
     *
     */
    protected function includeModuleMetaData($metadataPath)
    {
        $sMetadataVersion = null;
        include $metadataPath;

        /**
         * metadata.php should include a variable called $sMetadataVersion
         */

        if (isset($sMetadataVersion)) {
            $this->setMetaDataVersion($sMetadataVersion);
        }
    }

    /**
     * @return array
     */
    private function getInstalledModuleConfigurations(): array
    {
        $shopConfiguration = $this->getShopConfiguration();

        return $shopConfiguration->getModuleConfigurations();
    }

    /**
     * @return ShopConfiguration
     */
    private function getShopConfiguration(): ShopConfiguration
    {
        return ContainerFacade::get(ShopConfigurationDaoBridgeInterface::class)
            ->get();
    }

    /**
     * Convert ModuleConfiguration to Array
     *
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function convertModuleConfigurationToArray(ModuleConfiguration $configuration): array
    {
        $data = [
            'id'          => $configuration->getId(),
            'version'     => $configuration->getVersion(),
            'title'       => $configuration->getTitle(),
            'description' => $configuration->getDescription(),
            'lang'        => $configuration->getLang(),
            'thumbnail'   => $configuration->getThumbnail(),
            'author'      => $configuration->getAuthor(),
            'url'         => $configuration->getUrl(),
            'email'       => $configuration->getEmail(),
        ];

        foreach ($this->convertModuleSettingsToArray($configuration) as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function convertModuleSettingsToArray(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        $data[MetaDataProvider::METADATA_EXTEND] = $this->convertClassExtensionsToArray($moduleConfiguration);
        $data[MetaDataProvider::METADATA_CONTROLLERS] = $this->convertControllersToArray($moduleConfiguration);
        $data[MetaDataProvider::METADATA_EVENTS] = $this->convertEventsToArray($moduleConfiguration);
        $data[MetaDataProvider::METADATA_SETTINGS] = $this->convertSettingsToArray($moduleConfiguration);

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function convertClassExtensionsToArray(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getClassExtensions() as $extension) {
            $data[$extension->getShopClassName()] = $extension->getModuleExtensionClassName();
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function convertControllersToArray(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getControllers() as $controller) {
            $data[$controller->getId()] = $controller->getControllerClassNameSpace();
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function convertEventsToArray(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getEvents() as $event) {
            $data[$event->getAction()] = $event->getMethod();
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function convertSettingsToArray(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getModuleSettings() as $index => $setting) {
            if ($setting->getGroupName()) {
                $data[$index]['group'] = $setting->getGroupName();
            }

            if ($setting->getName()) {
                $data[$index]['name'] = $setting->getName();
            }

            if ($setting->getType()) {
                $data[$index]['type'] = $setting->getType();
            }

            $data[$index]['value'] = $setting->getValue();

            if (!empty($setting->getConstraints())) {
                $data[$index]['constraints'] = $setting->getConstraints();
            }

            if ($setting->getPositionInGroup() > 0) {
                $data[$index]['position'] = $setting->getPositionInGroup();
            }
        }

        return $data;
    }
}
