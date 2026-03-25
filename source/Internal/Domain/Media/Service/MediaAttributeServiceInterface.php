<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;

interface MediaAttributeServiceInterface
{
    public function getAttributes(Media $media, string $localeCode): MediaAttributes;

    public function save(string $name, string $value, Media $media, string $localeCode): void;

    public function delete(string $name, Media $media, string $localeCode): void;
}
