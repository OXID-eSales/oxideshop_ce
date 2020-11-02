<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Utility;

interface ShopSettingEncoderInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function encode(string $encodingType, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function decode(string $encodingType, $value);
}
