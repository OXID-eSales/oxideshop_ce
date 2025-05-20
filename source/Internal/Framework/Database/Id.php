<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use InvalidArgumentException;

readonly class Id
{
    private string $uid;

    private function __construct(?string $uid = null)
    {
        if ($uid && !$this->isMd5($uid)) {
            throw new InvalidArgumentException("Invalid ID format: $uid");
        }

        $this->uid = $uid ?? $this->generateUid();
    }

    public static function generate(): self
    {
        return new self();
    }

    public static function fromUid(string $id): self
    {
        return new self($id);
    }

    public function __toString(): string
    {
        return $this->uid;
    }

    private function isMd5(string $id): bool
    {
        return preg_match('/^[a-f0-9]{32}$/i', $id) === 1;
    }

    private function generateUid(): string
    {
        return md5(uniqid('', true) . '|' . microtime());
    }
}
