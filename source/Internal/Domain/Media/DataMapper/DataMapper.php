<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

class DataMapper implements DataMapperInterface
{
    public function toData(Media $media): array
    {
        return [
            'id'   => (string) $media->getId(),
            'path' => (string) $media->getMediaPath(),
            'type' => (string) $media->getMediaType(),
        ];
    }

    public function fromData(array $data): Media
    {
        return new Media(
            Id::fromString($data['id']),
            new MediaPath($data['path']),
            new MediaType($data['type']),
        );
    }
}
