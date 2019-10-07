<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

class ControllersModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ShopConfigurationModuleSettingHandler constructor
     *
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(
        ShopConfigurationSettingDaoInterface    $shopConfigurationSettingDao
    ) {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasControllers()) {
            $shopControllers = $this->getShopControllers($shopId);

            $shopSettingValue = array_merge(
                $shopControllers->getValue(),
                [
                    strtolower($configuration->getId()) => $this->controllerKeysToLowercase($configuration->getControllers()),
                ]
            );

            $shopControllers->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopControllers);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        $shopControllers = $this->getShopControllers($shopId);

        $shopSettingValue = $shopControllers->getValue();
        unset($shopSettingValue[strtolower($configuration->getId())]);

        $shopControllers->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopControllers);
    }

    /**
     * @param int $shopId
     * @return ShopConfigurationSetting
     */
    private function getShopControllers(int $shopId): ShopConfigurationSetting
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

    /**
     * Change the controller keys to lower case.
     *
     * @param array $controllers
     *
     * @return array The given controllers array with the controller keys in lower case.
     */
    private function controllerKeysToLowercase(array $controllers) : array
    {
        $result = [];

        foreach ($controllers as $controller) {
            $result[strtolower($controller->getId())] = $controller->getControllerClassNameSpace();
        }

        return $result;
    }
}
