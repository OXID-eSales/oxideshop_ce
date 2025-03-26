<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;

interface MediaDaoInterface
{
    public function create(string $path, string $type): Media;

    public function get(string $id): Media;

    public function delete(string $id): void;
}
