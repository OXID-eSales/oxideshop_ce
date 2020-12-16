<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

interface ImageHandlerInterface
{
    public function copy(string $source, string $destination): void;

    public function upload(string $source, string $destination): void;

    public function remove(string $path): void;

    public function exists(string $path): bool;
}
