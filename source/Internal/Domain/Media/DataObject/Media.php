<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class Media
{
    public function __construct(
        private Id $id,
        private MediaPath $mediaPath,
        private MediaType $mediaType,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getMediaPath(): MediaPath
    {
        return $this->mediaPath;
    }

    public function getMediaType(): MediaType
    {
        return $this->mediaType;
    }
}
