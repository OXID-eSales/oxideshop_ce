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
    protected $_sModule = 'shop_config';

    /**
     * Add additional config type for modules.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_aConfParams['password'] = 'confpassword';
    }

    /** @inheritdoc */
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
                    $this->_aViewData[$sParam] = $formatModuleSettings['vars'][$sType] ?? null;
                }
            }
        } catch (\Throwable $throwable) {
            Registry::getUtilsView()->addErrorToDisplay($throwable);
            Registry::getLogger()->error($throwable->getMessage());
        }

        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->_aViewData['oModule'] = $module;

        return 'module_config';
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
            $moduleWasActiveBeforeSaving = $this->getContainer()->get(ModuleActivationBridgeInterface::class)->isActive($moduleId, $shopId);

            if ($moduleWasActiveBeforeSaving) {
                $this->getContainer()->get(ModuleActivationBridgeInterface::class)->deactivate($moduleId, $shopId);
            }

            $this->saveModuleConfigVariables($moduleId, $this->getConfigVariablesFromRequest());

            if ($moduleWasActiveBeforeSaving) {
                $this->getContainer()->get(ModuleActivationBridgeInterface::class)->activate($moduleId, $shopId);
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
                            $value = $this->multilineToAarray($value);
                        }
                        if ($moduleSetting->getType() === 'arr') {
                            $value = $this->multilineToArray($value);
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
                        $value = $this->arrayToMultiline($setting->getValue());
                        break;
                    case 'aarr':
                        $value = $this->aarrayToMultiline($setting->getValue());
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
}
