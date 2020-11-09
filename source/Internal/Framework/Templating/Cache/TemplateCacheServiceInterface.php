<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

/**
 * Interface TemplateCacheServiceInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache
 */
interface TemplateCacheServiceInterface
{
    public function invalidateTemplateCache(): void;
}
