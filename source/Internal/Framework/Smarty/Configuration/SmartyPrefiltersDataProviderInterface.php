<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartyPrefiltersDataProviderInterface
{
    /**
     * @return array
     */
    public function getPrefilterPlugins(): array;
}
