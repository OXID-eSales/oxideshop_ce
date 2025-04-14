<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface MediaDaoInterface
{
    public function add(Media $media): void;

    /** @throws EntryDoesNotExistDaoException */
    public function get(Id $id): Media;

    public function delete(Id $id): void;
}
