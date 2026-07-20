<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

readonly class ThemeConfigurationMerger implements ThemeConfigurationMergerInterface
{
    public function merge(ThemeConfiguration $incoming, ThemeConfiguration $existing): ThemeConfiguration
    {
        $incoming->setActivated($existing->isActivated());

        foreach ($incoming->getThemeSettings() as $setting) {
            $existingSetting = $existing->getSettingByName($setting->getName());
            if ($existingSetting !== null) {
                $setting->setValue($existingSetting->getValue());
            }
        }

        return $incoming;
    }
}
