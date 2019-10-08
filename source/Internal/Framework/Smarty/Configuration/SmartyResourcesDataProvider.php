<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

class SmartyResourcesDataProvider implements SmartyResourcesDataProviderInterface
{
    /**
     * Returns an array of resources.
     *
     * @return array
     */
    public function getResources(): array
    {
        return [
            'ox' => [
                'ox_get_template',
                'ox_get_timestamp',
                'ox_get_secure',
                'ox_get_trusted'

            ]
        ];
    }
}
