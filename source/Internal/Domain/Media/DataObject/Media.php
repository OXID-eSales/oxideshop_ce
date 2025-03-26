<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

final readonly class Media
{
    public function __construct(
        private string $id,
        private string $path,
        private string $type
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
