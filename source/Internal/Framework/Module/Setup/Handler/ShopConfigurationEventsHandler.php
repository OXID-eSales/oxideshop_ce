<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ShopConfigurationEventsHandler implements ModuleConfigurationHandlerInterface
{
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
     * @param string                               $shopConfigurationSettingName
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(
        string                                  $shopConfigurationSettingName,
        ShopConfigurationSettingDaoInterface    $shopConfigurationSettingDao
    ) {
        $this->shopConfigurationSettingName = $shopConfigurationSettingName;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasEvents()) {
            $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

            $events = [];

            if ($configuration->hasEvents()) {
                foreach ($configuration->getEvents() as $event) {
                    $events[$event->getAction()] = $event->getMethod();
                }
            }

            $shopSettingValue = array_merge(
                $shopConfigurationSetting->getValue(),
                [
                    $events,
                ]
            );

            $shopConfigurationSetting->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasEvents()) {
            $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

            $shopSettingValue = $shopConfigurationSetting->getValue();
            unset($shopSettingValue[$configuration->getId()]);

            $shopConfigurationSetting->setValue($shopSettingValue);

            $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
        }
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
