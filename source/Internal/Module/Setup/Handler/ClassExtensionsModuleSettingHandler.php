<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ClassExtensionsModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ClassExtensionsModuleSettingHandler constructor.
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
        if ($this->canHandle($configuration)) {
            $moduleSetting = $configuration->getSetting(ModuleSetting::CLASS_EXTENSIONS);

            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            $shopConfigurationSettingValue[$configuration->getId()] = array_values($moduleSetting->getValue());

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
        if ($this->canHandle($configuration)) {
            $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

            $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
            unset($shopConfigurationSettingValue[$configuration->getId()]);

            $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return bool
     */
    private function canHandle(ModuleConfiguration $configuration): bool
    {
        return $configuration->hasSetting(ModuleSetting::CLASS_EXTENSIONS);
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
