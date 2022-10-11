<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

interface TemplateCacheServiceInterface
{
    public function invalidateTemplateCache(): void;
}
