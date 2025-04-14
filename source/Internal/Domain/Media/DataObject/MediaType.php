<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

readonly class MediaType
{
    public function __construct(
        private string $type
    ) {
        $this->validate($type);
    }

    private function validate(string $type): void
    {
        // Basic validation for MIME type format (e.g., image/png)
        if (!preg_match('#^[\w.\-]+/[\w.\-+]+$#', $type)) {
            throw new \InvalidArgumentException('Invalid MIME type format.');
        }
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
