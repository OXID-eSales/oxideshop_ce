<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Language;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class LanguageInstaller implements LanguageInstallerInterface
{
    public function __construct(
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private BasicContextInterface $context
    ) {
    }

    public function install(DefaultLanguage $language): void
    {
        $this->setDefaultLanguage($language);
        $this->updateActiveLanguage($language);
    }

    private function setDefaultLanguage(DefaultLanguage $language): void
    {
        $setting = new ShopConfigurationSetting();
        $setting
            ->setName('sDefaultLang')
            ->setValue($language->getCode())
            ->setType(ShopSettingType::STRING)
            ->setShopId($this->context->getDefaultShopId());

        $this->shopConfigurationSettingDao->save($setting);
    }

    private function updateActiveLanguage(DefaultLanguage $language): void
    {
        try {
            $languagesSetting = $this->shopConfigurationSettingDao->get(
                'aLanguageParams',
                $this->context->getDefaultShopId()
            );
            $settingValue = $languagesSetting->getValue();

            foreach ($settingValue as $languageCode => $settingLanguage) {
                $settingValue[$languageCode]['active'] = $languageCode === $language->getCode() ? 1 : 0;
            }

            $languagesSetting->setValue($settingValue);
            $this->shopConfigurationSettingDao->save($languagesSetting);
        } catch (EntryDoesNotExistDaoException) {
            // no setting, nothing to update
        }
    }
}
