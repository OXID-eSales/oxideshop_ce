<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;

interface DataMapperInterface
{
    public function toData(Media $media): array;

    public function fromData(array $data): Media;
}
