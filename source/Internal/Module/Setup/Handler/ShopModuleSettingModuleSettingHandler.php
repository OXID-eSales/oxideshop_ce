<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;

/**
 * @internal
 */
class ShopModuleSettingModuleSettingHandler implements ModuleSettingHandlerInterface
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
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     */
    public function handle(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        foreach ($moduleSetting->getValue() as $shopModuleSettingData) {
            $shopModuleSetting = new ShopModuleSetting();
            $shopModuleSetting
                ->setShopId($shopId)
                ->setModuleId($moduleId)
                ->setName($shopModuleSettingData['name'])
                ->setType($shopModuleSettingData['type'])
                ->setValue($shopModuleSettingData['value']);

            if (isset($shopModuleSettingData['constraints'])) {
                $shopModuleSetting->setConstraints(
                    explode('|', $shopModuleSettingData['constraints'])
                );
            }

            if (isset($shopModuleSettingData['group'])) {
                $shopModuleSetting->setGroupName($shopModuleSettingData['group']);
            }

            if (isset($shopModuleSettingData['position'])) {
                $shopModuleSetting->setPositionInGroup($shopModuleSettingData['position']);
            }

            $this->shopModuleSettingDao->save($shopModuleSetting);
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canHandle(ModuleSetting $moduleSetting): bool
    {
        return $moduleSetting->getName() === ModuleSetting::SHOP_MODULE_SETTING;
    }
}
