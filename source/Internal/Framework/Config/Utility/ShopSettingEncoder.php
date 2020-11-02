<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Utility;

use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Exception\InvalidShopSettingValueException;
use function serialize;
use function unserialize;

class ShopSettingEncoder implements ShopSettingEncoderInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function encode(string $encodingType, $value)
    {
        $this->validateSettingValue($value);

        switch ($encodingType) {
            case ShopSettingType::ARRAY:
            case ShopSettingType::ASSOCIATIVE_ARRAY:
                $encodedValue = serialize($value);
                break;
            case ShopSettingType::BOOLEAN:
                $encodedValue = true === $value ? '1' : '';
                break;
            default:
                $encodedValue = $value;
        }

        return $encodedValue;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function decode(string $encodingType, $value)
    {
        switch ($encodingType) {
            case ShopSettingType::ARRAY:
            case ShopSettingType::ASSOCIATIVE_ARRAY:
                $decodedValue = unserialize($value, [
                    'allowed_classes' => false,
                ]);
                break;
            case ShopSettingType::BOOLEAN:
                $decodedValue = ('true' === $value || '1' === $value);
                break;
            default:
                $decodedValue = $value;
        }

        return $decodedValue;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidShopSettingValueException
     */
    private function validateSettingValue($value): void
    {
        if (\is_object($value)) {
            throw new InvalidShopSettingValueException('Shop setting value must not be an object.');
        }
    }
}
