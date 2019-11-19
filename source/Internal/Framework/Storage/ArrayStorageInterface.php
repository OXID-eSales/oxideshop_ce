<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

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
