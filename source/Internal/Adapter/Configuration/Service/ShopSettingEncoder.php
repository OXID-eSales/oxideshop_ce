<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Service;

use function is_int;
use function is_array;
use function is_bool;
use function unserialize;
use function serialize;

/**
 * @internal
 */
class ShopSettingEncoder implements ShopSettingEncoderInterface
{
    const ARRAY = 'arr';
    const ASSOCIATIVE_ARRAY = 'aarr';
    const INTEGER = 'num';
    const BOOLEAN = 'bool';
    const STRING = 'str';

    /**
     * @param mixed $value
     * @return string
     */
    public function encode($value): string
    {
        $encodingType = $this->getEncodingType($value);

        switch ($encodingType) {
            case self::ARRAY:
                $encodedValue = serialize($value);
                break;
            case self::INTEGER:
                $encodedValue = (string) $value;
                break;
            case self::BOOLEAN:
                $encodedValue = $value === true ? '1' : '';
                break;
            default:
                $encodedValue = $value;
        }

        return $encodedValue;
    }

    /**
     * @param string $encodingType
     * @param string $value
     * @return mixed
     */
    public function decode(string $encodingType, string $value)
    {
        switch ($encodingType) {
            case self::ARRAY:
            case self::ASSOCIATIVE_ARRAY:
                $decodedValue = unserialize($value);
                break;
            case self::BOOLEAN:
                $decodedValue = ($value === 'true' || $value === '1');
                break;
            case self::INTEGER:
                $decodedValue = (int) $value;
                break;
            default:
                $decodedValue = $value;
        }

        return $decodedValue;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getEncodingType($value): string
    {
        $type = self::STRING;

        if (is_array($value)) {
            $type = self::ARRAY;
        }

        if (is_bool($value)) {
            $type = self::BOOLEAN;
        }

        if (is_int($value)) {
            $type = self::INTEGER;
        }

        return $type;
    }
}
