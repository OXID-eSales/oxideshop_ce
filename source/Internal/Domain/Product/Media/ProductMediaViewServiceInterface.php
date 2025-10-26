<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaViewServiceInterface
{
    public function getMedia(Id $productId, int $position): MediaView;

    public function getIcon(Id $productId): MediaView;

    public function getThumbnail(Id $productId): MediaView;

    /**
     * @return array<string, MediaView>
     */
    public function getActiveByProductId(Id $productId): array;
}
