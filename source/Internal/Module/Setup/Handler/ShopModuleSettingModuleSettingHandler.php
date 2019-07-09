<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ShopModuleSettingModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var ShopModuleSettingDaoInterface
     */
    private $shopModuleSettingDao;

    /**
     * ShopModuleSettingModuleSettingHandler constructor.
     * @param ShopModuleSettingDaoInterface $shopModuleSettingDao
     */
    public function __construct(ShopModuleSettingDaoInterface $shopModuleSettingDao)
    {
        $this->shopModuleSettingDao = $shopModuleSettingDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $setting = $configuration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);

            foreach ($setting->getValue() as $shopModuleSettingData) {
                $shopModuleSetting = new ShopModuleSetting();
                $shopModuleSetting
                    ->setShopId($shopId)
                    ->setModuleId($configuration->getId());

                $shopModuleSetting = $this->mapDataToShopModuleSetting($shopModuleSetting, $shopModuleSettingData);

                $this->shopModuleSettingDao->save($shopModuleSetting);
            }
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $setting = $configuration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);

            foreach ($setting->getValue() as $shopModuleSettingData) {
                $shopModuleSetting = new ShopModuleSetting();
                $shopModuleSetting
                    ->setShopId($shopId)
                    ->setModuleId($configuration->getId());

                $shopModuleSetting = $this->mapDataToShopModuleSetting($shopModuleSetting, $shopModuleSettingData);

                $this->shopModuleSettingDao->delete($shopModuleSetting);
            }
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return bool
     */
    private function canHandle(ModuleConfiguration $configuration): bool
    {
        return $configuration->hasSetting(ModuleSetting::SHOP_MODULE_SETTING);
    }

    /**
     * @param ShopModuleSetting $shopModuleSetting
     * @param array             $data
     * @return ShopModuleSetting
     */
    private function mapDataToShopModuleSetting(ShopModuleSetting $shopModuleSetting, array $data): ShopModuleSetting
    {
        $shopModuleSetting
            ->setName($data['name'])
            ->setType($data['type'])
            ->setValue($data['value']);

        if (isset($data['constraints'])) {
            $shopModuleSetting->setConstraints($data['constraints']);
        }

        if (isset($data['group'])) {
            $shopModuleSetting->setGroupName($data['group']);
        }

        if (isset($data['position'])) {
            $shopModuleSetting->setPositionInGroup($data['position']);
        }

        return $shopModuleSetting;
    }
}
