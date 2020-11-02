<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

class SmartyResourcesDataProvider implements SmartyResourcesDataProviderInterface
{
    /**
     * Returns an array of resources.
     */
    public function getResources(): array
    {
        return [
            'ox' => [
                'ox_get_template',
                'ox_get_timestamp',
                'ox_get_secure',
                'ox_get_trusted',
            ],
        ];
    }
}
