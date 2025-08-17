<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;

interface MediaUrlGeneratorInterface
{
    public function generateSizedImageUrl(Media $media, string $size): string;
}
