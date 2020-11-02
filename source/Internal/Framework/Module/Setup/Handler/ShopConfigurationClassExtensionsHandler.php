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

class ShopConfigurationClassExtensionsHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    public function __construct(ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao)
    {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasClassExtensions()) {
            $classExtensions = [];

            foreach ($configuration->getClassExtensions() as $extension) {
                $classExtensions[] = $extension->getModuleExtensionClassName();
            }

            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            $shopConfigurationSettingValue[$configuration->getId()] = array_values($classExtensions);

            $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasClassExtensions()) {
            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            unset($shopConfigurationSettingValue[$configuration->getId()]);

            $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    private function getClassExtensionsShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName(ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $shopConfigurationSetting;
    }
}
