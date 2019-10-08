<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Storage;

interface ArrayStorageInterface
{
    /**
     * @return array
     */
    public function get(): array;

    /**
     * @param array $data
     */
    public function save(array $data): void;
}
