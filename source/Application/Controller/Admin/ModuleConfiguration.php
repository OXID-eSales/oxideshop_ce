<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;

/**
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 */
class ModuleConfiguration extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    /** @var string Template name. */
    protected $_sModule = 'shop_config.tpl';

    /**
     * Add additional config type for modules.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_aConfParams['password'] = 'confpassword';
    }

    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $this->_sModuleId = $this->getSelectedModuleId();
        $moduleId = $this->_sModuleId;

        try {
            $moduleConfiguration = $this->getContainer()->get(ModuleConfigurationDaoBridgeInterface::class)->get($moduleId);
            if (!empty($moduleConfiguration->getModuleSettings())) {
                $formatModuleSettings = $this->formatModuleSettingsForTemplate($moduleConfiguration->getModuleSettings());

                $this->_aViewData["var_constraints"] = $formatModuleSettings['constraints'];
                $this->_aViewData["var_grouping"] = $formatModuleSettings['grouping'];

                foreach ($this->_aConfParams as $sType => $sParam) {
                    $this->_aViewData[$sParam] = $formatModuleSettings['vars'][$sType];
                }
            }
        } catch (\Throwable $throwable) {
            Registry::getUtilsView()->addErrorToDisplay($throwable);
            Registry::getLogger()->error($throwable->getMessage());
        }

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->_aViewData['oModule'] = $module;

        return 'module_config.tpl';
    }

    /**
     * return module filter for config variables
     *
     * @deprecated since v6.4.0 (2019-04-08); it moved to Internal\Framework\Module package
     *
     * @return string
     */
    protected function _getModuleForConfigVars() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return \OxidEsales\Eshop\Core\Config::OXMODULE_MODULE_PREFIX . $this->_sModuleId;
    }

    /**
     * Load and parse config vars from metadata.
     * Return value is a map:
     *      'vars'        => config variable values as array[type][name] = value
     *      'constraints' => constraints list as array[name] = constraint
     *      'grouping'    => grouping info as array[name] = grouping
     *
     * @deprecated since v6.4.0 (2019-04-08); it moved to Internal\Framework\Module package
     *
     * @param array $aModuleSettings settings array from module metadata
     *
     * @return array
     */
    public function _loadMetadataConfVars($aModuleSettings) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $aConfVars = [
            "bool"     => [],
            "str"      => [],
            "arr"      => [],
            "aarr"     => [],
            "select"   => [],
            "password" => [],
        ];
        $aVarConstraints = [];
        $aGrouping = [];

        $aDbVariables = $this->loadConfVars($oConfig->getShopId(), $this->_getModuleForConfigVars());

        if (is_array($aModuleSettings)) {
            foreach ($aModuleSettings as $aValue) {
                $sName = $aValue["name"];
                $sType = $aValue["type"];
                $sValue = null;
                if (is_null($oConfig->getConfigParam($sName))) {
                    switch ($aValue["type"]) {
                        case "arr":
                            $sValue = $this->_arrayToMultiline($aValue["value"]);
                            break;
                        case "aarr":
                            $sValue = $this->_aarrayToMultiline($aValue["value"]);
                            break;
                        case "bool":
                            $sValue = filter_var($aValue["value"], FILTER_VALIDATE_BOOLEAN);
                            break;
                        default:
                            $sValue = $aValue["value"];
                            break;
                    }
                    $sValue = Str::getStr()->htmlentities($sValue);
                } else {
                    $sDbType = $this->_getDbConfigTypeName($sType);
                    $sValue = $aDbVariables['vars'][$sDbType][$sName];
                }

                $sGroup = $aValue["group"];

                $sConstraints = "";
                if ($aValue["constraints"]) {
                    $sConstraints = $aValue["constraints"];
                } elseif ($aValue["constrains"]) {
                    $sConstraints = $aValue["constrains"];
                }

                $aConfVars[$sType][$sName] = $sValue;
                $aVarConstraints[$sName] = $sConstraints;
                if ($sGroup) {
                    if (!isset($aGrouping[$sGroup])) {
                        $aGrouping[$sGroup] = [$sName => $sType];
                    } else {
                        $aGrouping[$sGroup][$sName] = $sType;
                    }
                }
            }
        }

        return [
            'vars'        => $aConfVars,
            'constraints' => $aVarConstraints,
            'grouping'    => $aGrouping,
        ];
    }

    /**
     * Saves shop configuration variables
     */
    public function saveConfVars()
    {
        $this->resetContentCache();

        $moduleId = $this->getSelectedModuleId();
        $shopId = Registry::getConfig()->getShopId();
        $this->_sModuleId = $moduleId;

        try {
            $moduleActivationBridge = $this->getContainer()->get(ModuleActivationBridgeInterface::class);
            $moduleWasActiveBeforeSaving = $moduleActivationBridge->isActive($moduleId, $shopId);

            if ($moduleWasActiveBeforeSaving) {
                $moduleActivationBridge->deactivate($moduleId, $shopId);
            }

            $this->saveModuleConfigVariables($moduleId, $this->getConfigVariablesFromRequest());

            if ($moduleWasActiveBeforeSaving) {
                $moduleActivationBridge->activate($moduleId, $shopId);
            }
        } catch (\Throwable $throwable) {
            Registry::getUtilsView()->addErrorToDisplay($throwable);
            Registry::getLogger()->error($throwable->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getSelectedModuleId(): string
    {
        $moduleId = $this->_sEditObjectId
            ?? Registry::getRequest()->getRequestEscapedParameter('oxid')
            ?? Registry::getSession()->getVariable('saved_oxid');

        if ($moduleId === null) {
            throw new \InvalidArgumentException('Module id not found.');
        }

        return $moduleId;
    }

    /**
     * @param string $moduleId
     * @param array  $variables
     */
    private function saveModuleConfigVariables(string $moduleId, array $variables)
    {
        $moduleConfigurationDaoBridge = $this->getContainer()->get(ModuleConfigurationDaoBridgeInterface::class);
        $moduleConfiguration = $moduleConfigurationDaoBridge->get($moduleId);

        if (!empty($moduleConfiguration->getModuleSettings())) {
            foreach ($variables as $name => $value) {
                foreach ($moduleConfiguration->getModuleSettings() as $moduleSetting) {
                    if ($moduleSetting->getName() === $name) {
                        if ($moduleSetting->getType() === 'aarr') {
                            $value = $this->_multilineToAarray($value);
                        }
                        if ($moduleSetting->getType() === 'arr') {
                            $value = $this->_multilineToArray($value);
                        }
                        if ($moduleSetting->getType() === 'bool') {
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        }
                        $moduleSetting->setValue($value);
                    }
                }
            }

            $moduleConfigurationDaoBridge->save($moduleConfiguration);
        }
    }

    /**
     * @return array
     */
    private function getConfigVariablesFromRequest(): array
    {
        $settings = [];

        foreach ($this->_aConfParams as $requestParameterKey) {
            $settingsFromRequest = Registry::getRequest()->getRequestEscapedParameter($requestParameterKey);

            if (\is_array($settingsFromRequest)) {
                foreach ($settingsFromRequest as $name => $value) {
                    $settings[$name] = $value;
                }
            }
        }

        return $settings;
    }

    /**
     * @param Setting[] $moduleSettings
     * @return array
     */
    private function formatModuleSettingsForTemplate(array $moduleSettings): array
    {
        $confVars = [
            'bool'     => [],
            'str'      => [],
            'arr'      => [],
            'aarr'     => [],
            'select'   => [],
            'password' => [],
        ];
        $constraints = [];
        $grouping = [];

        foreach ($moduleSettings as $setting) {
            $name = $setting->getName();
            $valueType = $setting->getType();
            $value = null;

            if ($setting->getValue() !== null) {
                switch ($setting->getType()) {
                    case 'arr':
                        $value = $this->_arrayToMultiline($setting->getValue());
                        break;
                    case 'aarr':
                        $value = $this->_aarrayToMultiline($setting->getValue());
                        break;
                    case 'bool':
                        $value = filter_var($setting->getValue(), FILTER_VALIDATE_BOOLEAN);
                        break;
                    default:
                        $value = $setting->getValue();
                        break;
                }
                $value = Str::getStr()->htmlentities($value);
            }

            $group = $setting->getGroupName();


            $confVars[$valueType][$name] = $value;
            $constraints[$name] = $setting->getConstraints() ?? '';

            if ($group) {
                if (!isset($grouping[$group])) {
                    $grouping[$group] = [$name => $valueType];
                } else {
                    $grouping[$group][$name] = $valueType;
                }
            }
        }

        return [
            'vars'        => $confVars,
            'constraints' => $constraints,
            'grouping'    => $grouping,
        ];
    }

    /**
     * Convert metadata type to DB type.
     *
     * @param string $type Metadata type.
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getDbConfigTypeName" in next major
     */
    private function _getDbConfigTypeName($type) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $type === 'password' ? 'str' : $type;
    }
}
