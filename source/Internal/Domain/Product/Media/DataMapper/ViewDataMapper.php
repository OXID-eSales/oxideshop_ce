<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ViewDataMapper implements ViewDataMapperInterface
{
    public function __construct(
        private BasicContextInterface $context,
    ) {
    }

    /**  @param ArrayCollection<int, ProductMedia> $productMedia */
    public function toData(ArrayCollection $productMedia): array
    {
        $data = [];
        foreach ($productMedia as $media) {
            $data[] = [
                'id' => (string)$media->getId(),
                'productId' => (string)$media->getProductId(),
                'url' =>
                Path::join(
                    $this->context->getShopBaseUrl(),
                    (string)$media->getMedia()->getMediaPath()
                ),
                'position' => $media->getPosition(),
                'active' => $media->isActive(),
                'isThumbnail' => $media->getRoleSet()->is(SystemProductMediaRole::Thumb->value),
                'isIcon' => $media->getRoleSet()->is(SystemProductMediaRole::Icon->value),
            ];
        }

        return $data;
    }
}
