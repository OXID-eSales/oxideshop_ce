<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Filesystem\Filesystem;

class ShopTemplateCacheService implements ShopTemplateCacheServiceInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly Filesystem $filesystem,
        private readonly string $compilationDirectory
    ) {
    }

    public function getCacheDirectory(int $shopId): string
    {
        return Path::join(
            $this->compilationDirectory,
            'template_cache',
            'shops',
            (string) $shopId
        );
    }

    public function invalidateCache(int $shopId): void
    {
        if ($this->filesystem->exists($this->getCacheDirectory($shopId))) {
            $this->filesystem->remove($this->getCacheDirectory($shopId));
        }
    }

    public function invalidateAllShopsCache(): void
    {
        $shops = $this->context->getAllShopIds();

        foreach ($shops as $shop) {
            $this->invalidateCache($shop);
        }
    }
}
