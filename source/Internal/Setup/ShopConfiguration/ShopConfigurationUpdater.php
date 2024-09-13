<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\ShopConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class ShopConfigurationUpdater implements ShopConfigurationUpdaterInterface
{
    public function __construct(
        private readonly ShopConfigurationSettingDaoInterface $settingDao,
        private readonly BasicContextInterface $basicContext,
    ) {
    }

    public function saveShopSetupTime(): void
    {
        $setting = new ShopConfigurationSetting();
        $setting
            ->setName('sTagList')
            ->setValue(time())
            ->setType(ShopSettingType::STRING)
            ->setShopId($this->basicContext->getDefaultShopId());

        $this->settingDao->save($setting);
    }
}
