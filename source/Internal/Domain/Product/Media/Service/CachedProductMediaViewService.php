<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\ProductMediaViewCacheInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class CachedProductMediaViewService implements ProductMediaViewServiceInterface
{
    public function __construct(
        private ProductMediaViewServiceInterface $productMediaViewService,
        private ProductMediaViewCacheInterface $cache,
        private ActiveLocaleProviderInterface $activeLocaleProvider,
    ) {
    }

    public function getByRole(Id $productId, ProductMediaRole $role): ProductMediaView
    {
        return $this->cache->get(
            $productId,
            $this->getViewIdentifier('role_' . $role->value()),
            fn (): ProductMediaView => $this->productMediaViewService->getByRole($productId, $role)
        );
    }

    /** @return array<string, ProductMediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array
    {
        return $this->cache->getAll(
            $productId,
            $this->getViewIdentifier('all_' . $role->value()),
            fn (): array => $this->productMediaViewService->getAllByRole($productId, $role)
        );
    }

    public function getByPosition(Id $productId, int $position): ProductMediaView
    {
        return $this->cache->get(
            $productId,
            $this->getViewIdentifier('position_' . $position),
            fn (): ProductMediaView => $this->productMediaViewService->getByPosition($productId, $position)
        );
    }

    private function getViewIdentifier(string $viewName): string
    {
        return $viewName . '_' . $this->activeLocaleProvider->getActiveLocale()->getCode();
    }
}
