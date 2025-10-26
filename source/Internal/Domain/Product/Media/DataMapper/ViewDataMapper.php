<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ViewDataMapper implements ViewDataMapperInterface
{
    public function __construct(private BasicContextInterface $context)
    {
    }

    /** @param ArrayCollection<int, ProductMedia> $productMedia */
    public function toData(ArrayCollection $productMedia): array
    {
        $data = [
            'icon' => null,
            'thumbnail' => null,
            'detailImages' => [],
        ];

        foreach ($productMedia as $media) {
            $image = $this->buildImageData($media);
            $roles = $media->getRoleSet();

            if ($roles->has(ProductMediaRole::from(ProductMediaRole::DETAIL))) {
                $data['detailImages'][] = $image;
            }
            if ($roles->has(ProductMediaRole::from(ProductMediaRole::ICON))) {
                $data['icon'] = $image;
            }
            if ($roles->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL))) {
                $data['thumbnail'] = $image;
            }
        }

        return $data;
    }

    private function buildImageData(ProductMedia $media): array
    {
        return [
            'id'        => (string) $media->getId(),
            'productId' => (string) $media->getProductId(),
            'url'       => Path::join(
                $this->context->getShopBaseUrl(),
                (string) $media->getMedia()->getMediaPath()
            ),
            'position'  => $media->getPosition(),
            'active'    => $media->isActive(),
        ];
    }
}
