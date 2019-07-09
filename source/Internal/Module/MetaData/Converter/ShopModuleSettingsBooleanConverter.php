<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;

/**
 * @internal
 */
class ShopModuleSettingsBooleanConverter implements MetaDataConverterInterface
{
    private const CONVERSION_MAP = [
        'true' => true,
        '1' => true,
        'false' => false,
        '0' => false,
    ];

    public function convert(array $metaData): array
    {
        $convertedMetaData = $metaData;
        if (isset($metaData[ModuleSetting::SHOP_MODULE_SETTING])) {
            $settings = $metaData[ModuleSetting::SHOP_MODULE_SETTING];
            foreach ($settings as $key => $setting) {
                $convertedMetaData[ModuleSetting::SHOP_MODULE_SETTING][$key] = $this->updateValue($setting);
            }
        }

        return $convertedMetaData;
    }

    /**
     * @param $setting
     * @return mixed
     */
    private function updateValue($setting)
    {
        if (isset($setting['type']) && $setting['type'] === 'bool') {
            $value = is_string($setting['value']) ? strtolower($setting['value']) : $setting['value'];
            $setting['value'] = self::CONVERSION_MAP[$value] ?? $setting['value'];
        }
        return $setting;
    }
}
