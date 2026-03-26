<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject;

readonly class Locale
{
    public function __construct(
        private string $code,
        private string $name,
        private string $fallbackCode,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFallbackCode(): string
    {
        return $this->fallbackCode;
    }
}
