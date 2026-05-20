<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject;

readonly class LocaleChain
{
    /** @param string[] $codes */
    public function __construct(
        private array $codes,
    ) {
    }

    /** @return string[] */
    public function getCodes(): array
    {
        return $this->codes;
    }

    public function isEmpty(): bool
    {
        return $this->codes === [];
    }
}
