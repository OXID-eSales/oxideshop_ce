<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ImageHandlerInterface;

class MasterImageHandlerBridge implements MasterImageHandlerBridgeInterface
{
    public function __construct(private ImageHandlerInterface $masterImageHandler)
    {
    }

    /** @inheritdoc */
    public function copy(string $source, string $destination): void
    {
        $this->masterImageHandler->copy($source, $destination);
    }

    /** @inheritdoc */
    public function upload(string $source, string $destination): void
    {
        $this->masterImageHandler->upload($source, $destination);
    }

    /** @inheritdoc */
    public function remove(string $path): void
    {
        $this->masterImageHandler->remove($path);
    }

    /** @inheritdoc */
    public function exists(string $path): bool
    {
        return $this->masterImageHandler->exists($path);
    }
}
