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

class ControllersModuleSettingHandler implements ModuleConfigurationHandlerInterface
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
        if ($configuration->hasControllers()) {
            $shopControllers = $this->getShopConfigurationSetting($shopId);

            $shopSettingValue = array_merge(
                $shopControllers->getValue(),
                [
                    strtolower($configuration->getId()) => $this->controllerKeysToLowercase(
                        $configuration->getControllers()
                    ),
                ]
            );

            $shopControllers->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopControllers);
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        $shopControllers = $this->getShopConfigurationSetting($shopId);

        $shopSettingValue = $shopControllers->getValue();
        unset($shopSettingValue[strtolower($configuration->getId())]);

        $shopControllers->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopControllers);
    }

    private function controllerKeysToLowercase(array $controllers): array
    {
        $result = [];

        foreach ($controllers as $controller) {
            $result[strtolower($controller->getId())] = $controller->getControllerClassNameSpace();
        }

        return $result;
    }

    private function getShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                ShopConfigurationSetting::MODULE_CONTROLLERS,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName(ShopConfigurationSetting::MODULE_CONTROLLERS)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $shopConfigurationSetting;
    }
}
