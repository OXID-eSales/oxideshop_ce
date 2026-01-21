<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

readonly class Id
{
    private string $uid;

    private function __construct(?string $uid = null)
    {
        $this->uid = $uid ?? $this->generateUid();
    }

    public static function generate(): self
    {
        return new self();
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function __toString(): string
    {
        return $this->uid;
    }

    private function generateUid(): string
    {
        return md5(uniqid('', true) . '|' . microtime());
    }
}
