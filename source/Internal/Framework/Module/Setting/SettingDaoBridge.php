<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class SettingDaoBridge implements SettingDaoBridgeInterface
{
    private ContextInterface $context;
    private SettingDaoInterface $settingDao;

    public function __construct(ContextInterface $context, SettingDaoInterface $settingDao)
    {
        $this->context = $context;
        $this->settingDao = $settingDao;
    }

    public function save(Setting $moduleSetting, string $moduleId): void
    {
        $this->settingDao->save($moduleSetting, $moduleId, $this->context->getCurrentShopId());
    }

    public function get(string $name, string $moduleId): Setting
    {
        return $this->settingDao->get($name, $moduleId, $this->context->getCurrentShopId());
    }
}
