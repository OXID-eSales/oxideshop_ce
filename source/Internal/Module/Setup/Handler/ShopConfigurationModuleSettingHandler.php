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
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException;

/**
 * @internal
 */
class ShopConfigurationModuleSettingHandler implements ModuleSettingHandlerInterface
{
    /**
     * @var string
     */
    private $settingName;

    /**
     * @var string
     */
    private $shopConfigurationSettingName;

    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ShopConfigurationModuleSettingHandler constructor.
     * @param string                               $settingName
     * @param string                               $shopConfigurationSettingName
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(
        string                                  $settingName,
        string                                  $shopConfigurationSettingName,
        ShopConfigurationSettingDaoInterface    $shopConfigurationSettingDao
    ) {
        $this->settingName = $settingName;
        $this->shopConfigurationSettingName = $shopConfigurationSettingName;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
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

        $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

        $shopSettingValue = array_merge(
            $shopConfigurationSetting->getValue(),
            [
                $moduleId => $moduleSetting->getValue(),
            ]
        );

        $shopConfigurationSetting->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
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

        $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

        $shopSettingValue = $shopConfigurationSetting->getValue();
        unset($shopSettingValue[$moduleId]);

        $shopConfigurationSetting->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canHandle(ModuleSetting $moduleSetting): bool
    {
        return $this->settingName === $moduleSetting->getName();
    }

    /**
     * @param int $shopId
     * @return ShopConfigurationSetting
     */
    private function getShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                $this->shopConfigurationSettingName,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName($this->shopConfigurationSettingName)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $shopConfigurationSetting;
    }
}
