<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\State;

use function in_array;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;

/**
 * @internal
 */
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
    public function __construct(ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao)
    {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return bool
     */
    public function isActive(string $moduleId, int $shopId): bool
    {
        $activeModuleIdsSetting = $this->shopConfigurationSettingDao->get(ShopConfigurationSetting::ACTIVE_MODULES, $shopId);

        return in_array(
            $moduleId,
            $activeModuleIdsSetting->getValue(),
            true
        );
    }

    /**
     * @param string $moduleId
     */
    public function setDeleted(string $moduleId)
    {
        // TODO: Implement setDeleted() method.
    }
}
