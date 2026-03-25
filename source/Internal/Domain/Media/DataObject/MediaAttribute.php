<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class MediaAttribute
{
    public function __construct(
        private Id $id,
        private Id $mediaId,
        private string $localeCode,
        private int $shopId,
        private string $name,
        private string $value,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getMediaId(): Id
    {
        return $this->mediaId;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
