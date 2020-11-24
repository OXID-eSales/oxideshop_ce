<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\MasterImageHandlerInterface;

class MasterImageHandlerBridge implements MasterImageHandlerBridgeInterface
{
    /** @var MasterImageHandlerInterface */
    private $masterFileHandler;

    public function __construct(
        MasterImageHandlerInterface $filesystem
    ) {
        $this->masterFileHandler = $filesystem;
    }

    /** @inheritdoc */
    public function copy(string $source, string $destination): void
    {
        $this->masterFileHandler->copy($source, $destination);
    }

    /** @inheritdoc */
    public function upload(string $source, string $destination): void
    {
        $this->masterFileHandler->upload($source, $destination);
    }

    /** @inheritdoc */
    public function remove(string $path): void
    {
        $this->masterFileHandler->remove($path);
    }

    /** @inheritdoc */
    public function exists(string $path): bool
    {
        return $this->masterFileHandler->exists($path);
    }
}
