<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class VariantMediaCopier implements VariantMediaCopierInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
    ) {
    }

    public function copyMediaFromParentToVariant(Id $parentProductId, Id $variantProductId): void
    {
        $parentMediaCollection = $this->productMediaDao->getAll($parentProductId);

        foreach ($parentMediaCollection as $parentMedia) {
            $variantMedia = $this->createVariantMedia($parentMedia, $variantProductId);
            $this->productMediaDao->add($variantMedia);
        }
    }

    private function createVariantMedia(ProductMedia $parentMedia, Id $variantProductId): ProductMedia
    {
        $variantMedia = new ProductMedia(
            Id::generate(),
            $variantProductId,
            $parentMedia->getMedia(),
            new ProductMediaRoleSet(...$parentMedia->getRoleSet()->getRoles()->getValues()),
        );

        if ($parentMedia->hasPosition()) {
            $variantMedia->setPosition($parentMedia->getPosition());
        }

        if (!$parentMedia->isActive()) {
            $variantMedia->deactivate();
        }

        return $variantMedia;
    }
}
