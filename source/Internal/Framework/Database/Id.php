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
        if (!is_null($uid) && !$this->isValidUid($uid)) {
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

    private function isValidUid(string $id): bool
    {
        return (bool) preg_match('/^.{1,32}$/', $id);
    }

    private function generateUid(): string
    {
        return md5(uniqid('', true) . '|' . microtime());
    }
}
