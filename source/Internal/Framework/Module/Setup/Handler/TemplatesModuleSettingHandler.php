<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class TemplatesModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    public function __construct(
        ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
    ) {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasTemplates()) {
            $templates = [];

            foreach ($configuration->getTemplates() as $template) {
                $templates[$template->getTemplateKey()] = $template->getTemplatePath();
            }

            $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

            $shopSettingValue = array_merge(
                $shopConfigurationSetting->getValue(),
                [
                    $configuration->getId() => $templates,
                ]
            );

            $shopConfigurationSetting->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasTemplates()) {
            $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

            $shopSettingValue = $shopConfigurationSetting->getValue();
            unset($shopSettingValue[$configuration->getId()]);

            $shopConfigurationSetting->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    private function getShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                ShopConfigurationSetting::MODULE_TEMPLATES,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName(ShopConfigurationSetting::MODULE_TEMPLATES)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $shopConfigurationSetting;
    }
}
