<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class ProductMediaService implements ProductMediaServiceInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private MediaDaoInterface $mediaDao,
    ) {
    }

    public function add(ProductMedia $productMedia): void
    {
        $this->mediaDao
            ->add(
                $productMedia->getMedia()
            );
        $this->productMediaDao
            ->add($productMedia);
    }

    public function get(Id $mediaId): ProductMedia
    {
        return $this->productMediaDao->get($mediaId);
    }

    public function remove(Id $mediaId): void
    {
        $this->productMediaDao->delete($mediaId);
    }

    public function sort(array $idsSorted): void
    {
        $this->productMediaDao->sort(
            new ProductMediaSorting($idsSorted)
        );
    }

    public function activate(ProductMedia $productMedia): void
    {
        if (!$productMedia->isActive()) {
            $productMedia->activate();
            $this->productMediaDao->update(
                $productMedia
            );
        }
    }

    public function deactivate(ProductMedia $productMedia): void
    {
        if ($productMedia->isActive()) {
            $productMedia->deactivate();
            $this->productMediaDao->update(
                $productMedia
            );
        }
    }
}
