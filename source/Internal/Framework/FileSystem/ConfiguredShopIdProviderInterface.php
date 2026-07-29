<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

interface ConfiguredShopIdProviderInterface
{
    /**
     * @return int[]
     */
    public function getShopIds(): array;
}
