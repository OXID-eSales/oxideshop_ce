<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Storage;

/**
 * @internal
 */
interface FileStorageFactoryInterface
{
    public function create(string $filePath): ArrayStorageInterface;
}
