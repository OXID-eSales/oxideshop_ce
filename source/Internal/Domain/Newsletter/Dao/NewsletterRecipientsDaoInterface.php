<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao;

interface NewsletterRecipientsDaoInterface
{
    /**
     * @param int $shopId
     *
     * @return array
     */
    public function get(int $shopId): array;
}
