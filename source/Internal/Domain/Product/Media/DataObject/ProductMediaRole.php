<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

class ProductMediaRole
{
    private bool $isSingleAssignmentRole;

    private function __construct(private readonly string $code)
    {
        $this->isSingleAssignmentRole = true;
    }

    public static function from(string $code): ProductMediaRole
    {
        return new self($code);
    }

    public function value(): string
    {
        return $this->code;
    }

    public function isSingleAssignmentRole(): bool
    {
        return $this->isSingleAssignmentRole;
    }

    public function allowMultipleAssignments(): static
    {
        $this->isSingleAssignmentRole = false;
        return $this;
    }
}
