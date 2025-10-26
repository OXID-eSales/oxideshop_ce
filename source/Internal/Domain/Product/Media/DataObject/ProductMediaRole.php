<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

class ProductMediaRole
{
    public const ICON = 'icon';
    public const THUMBNAIL = 'thumbnail';
    public const DETAIL = 'detail';

    public static function from(string $role): ProductMediaRole
    {
        return new self($role);
    }

    public function value(): string
    {
        return $this->role;
    }

    private function __construct(private readonly string $role)
    {
    }
}
