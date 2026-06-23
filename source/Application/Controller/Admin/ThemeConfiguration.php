<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\ThemeViewItemFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use oxAdminDetails;

class ThemeConfiguration extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    protected $_sTheme = null;

    /** @inheritdoc */
    public function render()
    {
        $shopId = (int) Registry::getConfig()->getShopId();
        $themeId = $this->_sTheme = $this->getEditObjectId()
            ?: ContainerFacade::get(ActiveThemeServiceInterface::class)->getActiveThemeId();

        $theme = ContainerFacade::get(ThemeViewItemFactoryInterface::class)->get($themeId, $shopId);

        if ($theme !== null) {
            $this->_aViewData['oTheme'] = $theme;

            $themeVariables = $this->loadThemeConfVars($themeId, $shopId);
            $this->_aViewData['var_constraints'] = $themeVariables['constraints'];
            $this->_aViewData['var_grouping'] = $themeVariables['grouping'];
            foreach ($this->_aConfParams as $type => $param) {
                $this->_aViewData[$param] = $themeVariables['vars'][$type] ?? null;
            }
        } else {
            Registry::getUtilsView()->addErrorToDisplay(
                oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED')
            );
        }

        return 'theme_config';
    }

    /**
     * Saves theme configuration variables.
     */
    public function saveConfVars()
    {
        oxAdminDetails::save();

        $shopId = (int) Registry::getConfig()->getShopId();
        $themeId = $this->getEditObjectId() ?: $this->_sTheme;

        $dao = ContainerFacade::get(ThemeConfigurationDaoInterface::class);
        $encoder = ContainerFacade::get(ShopSettingEncoderInterface::class);
        $themeConfiguration = $dao->get($themeId, $shopId);

        foreach ($this->_aConfParams as $type => $param) {
            $postedValues = Registry::getRequest()->getRequestParameter($param);
            if (!is_array($postedValues)) {
                continue;
            }

            foreach ($postedValues as $name => $value) {
                if ($themeConfiguration->hasSetting($name)) {
                    $serializedValue = $this->serializeConfVar($type, $name, $value);
                    $themeConfiguration->getSetting($name)->setValue(
                        in_array($type, ['arr', 'aarr'], true)
                            ? $serializedValue
                            : $encoder->decode($type, $serializedValue)
                    );
                }
            }
        }

        $dao->save($themeConfiguration, $shopId);
    }

    private function loadThemeConfVars(string $themeId, int $shopId): array
    {
        $variables = ['bool' => [], 'str' => [], 'arr' => [], 'aarr' => [], 'select' => [], 'num' => []];
        $constraints = [];
        $grouping = [];

        $encoder = ContainerFacade::get(ShopSettingEncoderInterface::class);
        $themeConfiguration = ContainerFacade::get(ThemeConfigurationDaoInterface::class)->get($themeId, $shopId);

        foreach ($themeConfiguration->getSettings() as $setting) {
            $type = $setting->getType();
            $name = $setting->getName();

            $variables[$type][$name] = $this->unserializeConfVar(
                $type,
                $name,
                $encoder->encode($type, $setting->getValue())
            );
            $constraints[$name] = $this->parseConstraint($type, implode('|', $setting->getConstraints()));

            $group = $setting->getGroupName();
            if ($group) {
                $grouping[$group][$name] = $type;
            }
        }

        return ['vars' => $variables, 'constraints' => $constraints, 'grouping' => $grouping];
    }
}
