<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Factory\ProductMediaViewFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class ProductMediaViewService implements ProductMediaViewServiceInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private ProductMediaViewFactoryInterface $viewFactory,
    ) {
    }

    public function getByRole(Id $productId, ProductMediaRole $role): ProductMediaView
    {
        $productMedia = $this->productMediaDao->getActiveByRole($productId, $role)
            ?? $this->productMediaDao->getFirstActive($productId);

        return $this->createView($productMedia);
    }

    /** @return array<string, ProductMediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array
    {
        $mediaViews = [];
        foreach ($this->productMediaDao->getAllActiveByRole($productId, $role) as $productMedia) {
            $media = $productMedia->getMedia();
            $mediaViews[(string) $media->getId()] = $this->viewFactory->create($media);
        }

        return $mediaViews;
    }

    public function getByPosition(Id $productId, int $position): ProductMediaView
    {
        return $this->createView(
            $this->productMediaDao->getActiveByPosition($productId, $position)
        );
    }

    private function createView(?ProductMedia $productMedia): ProductMediaView
    {
        return $productMedia
            ? $this->viewFactory->create($productMedia->getMedia())
            : $this->viewFactory->createFallback();
    }
}
