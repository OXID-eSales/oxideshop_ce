<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration as Configuration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Form\SettingValueMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Validator\SettingValueValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class ThemeConfiguration extends AdminDetailsController
{
    public function __construct(
        private readonly ThemeConfigurationServiceInterface $themeConfigurationService,
        private readonly SettingValueMapperInterface $settingValueMapper,
        private readonly SettingValueValidatorInterface $settingValueValidator,
        private readonly Request $request,
    ) {
        parent::__construct();
    }

    public function render(): string
    {
        parent::render();

        try {
            $configuration = $this->getThemeConfiguration();

            $this->_aViewData['themeId'] = $configuration->getId();
            $this->_aViewData['themeTitle'] = $configuration->getId();
            $this->_aViewData['settingGroups'] = $this->buildSettingGroups($configuration);
        } catch (ActiveThemeNotFoundException | ThemeConfigurationNotFoundException) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        }

        return 'theme_config';
    }

    public function save(): void
    {
        $this->resetContentCache();

        try {
            $configuration = $this->getThemeConfiguration();

            $this->themeConfigurationService->updateSettings(
                $configuration,
                $this->settingValueMapper->fromFormValues($configuration, $this->getValidSettingValues())
            );
        } catch (ActiveThemeNotFoundException | ThemeConfigurationNotFoundException) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        }
    }

    private function getThemeConfiguration(): Configuration
    {
        $themeId = $this->getEditObjectId();

        return $themeId
            ? $this->themeConfigurationService->getConfiguration($themeId)
            : $this->themeConfigurationService->getActiveConfiguration();
    }

    private function buildSettingGroups(Configuration $configuration): array
    {
        $groups = [];

        foreach ($configuration->getThemeSettings() as $setting) {
            if ($setting->getGroupName() !== '') {
                $groups[$setting->getGroupName()][] = $setting;
            }
        }

        return array_map(fn(array $settings): array => $this->buildGroupData($settings), $groups);
    }

    /** @param Setting[] $settings */
    private function buildGroupData(array $settings): array
    {
        usort(
            $settings,
            fn(Setting $first, Setting $second): int => $first->getPositionInGroup() <=> $second->getPositionInGroup()
        );

        return array_map(fn(Setting $setting): array => $this->buildSettingData($setting), $settings);
    }

    private function buildSettingData(Setting $setting): array
    {
        return [
            'name' => $setting->getName(),
            'type' => $setting->getType(),
            'value' => $this->settingValueMapper->toFormValue($setting),
            'options' => $setting->getConstraints(),
        ];
    }

    /** @return array<string, string> */
    private function getValidSettingValues(): array
    {
        return array_filter(
            $this->request->request->all('settings'),
            fn(mixed $value): bool => is_string($value) && $this->isValidSettingValue($value)
        );
    }

    private function isValidSettingValue(string $value): bool
    {
        if (!$this->settingValueValidator->isValid($value)) {
            $this->displayInvalidValueError($value);

            return false;
        }

        return true;
    }

    private function displayInvalidValueError(string $value): void
    {
        $error = oxNew(DisplayError::class);
        $error->setFormatParameters([htmlspecialchars($value)]);
        $error->setMessage('SHOP_CONFIG_ERROR_INVALID_VALUE');

        Registry::getUtilsView()->addErrorToDisplay($error);
    }
}
