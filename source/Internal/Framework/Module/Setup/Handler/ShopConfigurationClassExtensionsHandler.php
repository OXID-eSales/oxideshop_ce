<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

    /**
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao)
    {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasClassExtensions()) {
            $classExtensions=[];

            foreach ($configuration->getClassExtensions() as $extension) {
                $classExtensions[$extension->getShopClassName()] = $extension->getModuleExtensionClassName();
            }

            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            $shopConfigurationSettingValue[$configuration->getId()] = array_values($classExtensions);

            $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasClassExtensions()) {
            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            unset($shopConfigurationSettingValue[$configuration->getId()]);

            $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    /**
     * @param int $shopId
     * @return ShopConfigurationSetting
     */
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
