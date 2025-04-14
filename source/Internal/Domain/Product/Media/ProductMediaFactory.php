<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class ProductMediaFactory implements ProductMediaFactoryInterface
{
    public function create(Id $productId, MediaPath $path, MediaType $mimeType): ProductMedia
    {
        return new ProductMedia(
            Id::generate(),
            $productId,
            new Media(
                Id::generate(),
                $path,
                $mimeType
            ),
            new ProductMediaRoleSet(
                ProductMediaRole::from(
                    SystemProductMediaRole::Detail->value
                )
                    ->allowMultipleAssignments()
            ),
        );
    }
}
