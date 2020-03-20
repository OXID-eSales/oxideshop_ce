<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartyResourcesDataProviderInterface
{
    /**
     * Returns an array of resources.
     *
     * @return array
     */
    public function getResources(): array;
}
