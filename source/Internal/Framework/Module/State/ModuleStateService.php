<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\State;

use function in_array;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

class ModuleStateService implements ModuleStateServiceInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ModuleStateService constructor.
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(
        ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
    ) {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return bool
     */
    public function isActive(string $moduleId, int $shopId): bool
    {
        $activeModuleIdsSetting = $this->getActiveModulesShopConfigurationSetting($shopId);

        return in_array($moduleId, $activeModuleIdsSetting->getValue(), true);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleStateIsAlreadySetException
     */
    public function setActive(string $moduleId, int $shopId)
    {
        if ($this->isActive($moduleId, $shopId)) {
            throw new ModuleStateIsAlreadySetException(
                'Active status for module "' . $moduleId . '" and shop with id "' . $shopId . '" is already set.'
            );
        }

        $activeModuleIdsSetting = $this->getActiveModulesShopConfigurationSetting($shopId);

        $activeModuleIds = $activeModuleIdsSetting->getValue();
        $activeModuleIds[] = $moduleId;
        $activeModuleIdsSetting->setValue($activeModuleIds);

        $this->shopConfigurationSettingDao->save($activeModuleIdsSetting);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleStateIsAlreadySetException
     */
    public function setDeactivated(string $moduleId, int $shopId)
    {
        if (!$this->isActive($moduleId, $shopId)) {
            throw new ModuleStateIsAlreadySetException(
                'Deactivated status for module "' . $moduleId . '" and shop with id "' . $shopId . '" is already set.'
            );
        }

        $activeModuleIdsSetting = $this->getActiveModulesShopConfigurationSetting($shopId);

        $activeModuleIds = $activeModuleIdsSetting->getValue();

        $activeModuleIds = array_diff($activeModuleIds, [$moduleId]);
        $activeModuleIdsSetting->setValue($activeModuleIds);

        $this->shopConfigurationSettingDao->save($activeModuleIdsSetting);
    }

    /**
     * @param int $shopId
     * @return ShopConfigurationSetting
     */
    private function getActiveModulesShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $activeModuleIdsSetting = $this->shopConfigurationSettingDao->get(
                ShopConfigurationSetting::ACTIVE_MODULES,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $activeModuleIdsSetting = new ShopConfigurationSetting();
            $activeModuleIdsSetting
                ->setShopId($shopId)
                ->setName(ShopConfigurationSetting::ACTIVE_MODULES)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $activeModuleIdsSetting;
    }
}
