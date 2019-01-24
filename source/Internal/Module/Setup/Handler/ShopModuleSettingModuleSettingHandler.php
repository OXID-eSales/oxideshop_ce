<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException;
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
     *
     * @throws WrongModuleSettingException
     */
    public function handleOnModuleActivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canHandle($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
        }

        foreach ($moduleSetting->getValue() as $shopModuleSettingData) {
            $shopModuleSetting = new ShopModuleSetting();
            $shopModuleSetting
                ->setShopId($shopId)
                ->setModuleId($moduleId);

            $shopModuleSetting = $this->mapDataToShopModuleSetting($shopModuleSetting, $shopModuleSettingData);

            $this->shopModuleSettingDao->save($shopModuleSetting);
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongModuleSettingException
     */
    public function handleOnModuleDeactivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canHandle($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
        }

        foreach ($moduleSetting->getValue() as $shopModuleSettingData) {
            $shopModuleSetting = new ShopModuleSetting();
            $shopModuleSetting
                ->setShopId($shopId)
                ->setModuleId($moduleId);

            $shopModuleSetting = $this->mapDataToShopModuleSetting($shopModuleSetting, $shopModuleSettingData);

            $this->shopModuleSettingDao->delete($shopModuleSetting);
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
