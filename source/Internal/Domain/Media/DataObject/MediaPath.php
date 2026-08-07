<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

readonly class MediaPath
{
    public function __construct(
        private string $path
    ) {
        $this->validate($path);
    }

    public function __toString(): string
    {
        return $this->path;
    }

    private function validate(string $path): void
    {
        if ($path === '') {
            throw new \InvalidArgumentException('Media path cannot be empty.');
        }
        if (preg_match('/[<>:"|?*]/', $path)) {
            throw new \InvalidArgumentException('Media path contains invalid characters.');
        }
        if (str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            throw new \InvalidArgumentException('Media path must be relative, not absolute.');
        }
        if (preg_match('~(^|[\\\\/])\.\.([\\\\/]|$)~', $path)) {
            throw new \InvalidArgumentException('Media path must not contain parent directory segments.');
        }
    }
}
