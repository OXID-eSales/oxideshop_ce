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
class ClassExtensionsModuleSettingHandler implements ModuleSettingHandlerInterface
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

        $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

        $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
        $shopConfigurationSettingValue[$moduleId] = array_values($moduleSetting->getValue());

        $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

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

        $shopConfigurationSetting = $this->getClassExtensionsShopConfigurationSetting($shopId);

        $shopConfigurationSettingValue = $shopConfigurationSetting->getValue();
        unset($shopConfigurationSettingValue[$moduleId]);

        $shopConfigurationSetting->setValue($shopConfigurationSettingValue);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canHandle(ModuleSetting $moduleSetting): bool
    {
        return $moduleSetting->getName() === ModuleSetting::CLASS_EXTENSIONS;
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
