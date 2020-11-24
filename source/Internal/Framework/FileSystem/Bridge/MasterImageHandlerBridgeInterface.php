<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Bridge;

interface MasterImageHandlerBridgeInterface
{
    /**
     * @param string $source
     * @param string $destination
     */
    public function copy(string $source, string $destination): void;

    /**
     * @param string $source
     * @param string $destination
     */
    public function upload(string $source, string $destination): void;

    /** @param string $path */
    public function remove(string $path): void;

    /**
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;
}
