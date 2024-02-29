<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

/**
 * @deprecated Use OxidEsales\Eshop\Internal\Framework\Templating\Cache\ShopTemplateCacheService instead
 */
class TemplateCacheService implements TemplateCacheServiceInterface
{
    public function __construct(
        private ContextInterface $context,
        private ShopTemplateCacheServiceInterface $shopTemplateCacheService
    ) {
    }

    public function invalidateTemplateCache(): void
    {
        $shops = $this->context->getAllShopIds();

        foreach ($shops as $shop) {
            $this->shopTemplateCacheService->invalidateCache($shop);
        }
    }
}
