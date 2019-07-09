<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Exception\ShopModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;

/**
 * @internal
 */
class ShopModuleSettingBooleanValidator implements MetaDataValidatorInterface
{
    private const ALLOWED_VALUES = [
        0,
        1,
        '0',
        '1',
        'true',
        'false',
        true,
        false,
    ];

    /**
     * @param array $metaData
     *
     * @throws ShopModuleSettingNotValidException
     */
    public function validate(array $metaData): void
    {
        if (isset($metaData[ModuleSetting::SHOP_MODULE_SETTING])) {
            $settings = $metaData[ModuleSetting::SHOP_MODULE_SETTING];
            foreach ($settings as $setting) {
                $this->validateSetting($metaData, $setting);
            }
        }
    }

    /**
     * @param array $metaData
     * @param mixed $setting
     * @throws ShopModuleSettingNotValidException
     */
    private function validateSetting(array $metaData, $setting): void
    {
        if (isset($setting['type']) && $setting['type'] === 'bool') {
            $value = is_string($setting['value']) ? strtolower($setting['value']) : $setting['value'];
            if (!in_array($value, self::ALLOWED_VALUES, true)) {
                throw new ShopModuleSettingNotValidException(
                    'Invalid boolean value- "' . $setting['value'] . '" was used for module setting. '
                    . 'Please update setting value in module "' . $metaData[MetaDataProvider::METADATA_ID] . '".'
                );
            }
        }
    }
}
