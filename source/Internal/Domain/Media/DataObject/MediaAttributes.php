<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Exception\AttributeNotFoundException;

readonly class MediaAttributes
{
    /** @param array<string, string> $attributes */
    public function __construct(
        private array $attributes = [],
    ) {
    }

    public function has(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function get(string $name): string
    {
        if (!$this->has($name)) {
            throw new AttributeNotFoundException("Attribute '$name' does not exist.");
        }

        return $this->attributes[$name];
    }

    public function getAlt(): string
    {
        return $this->get('alt');
    }
}
