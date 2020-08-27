<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Bridge;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface AdminUserServiceBridgeInterface
{
    /**
     * @param string $email
     * @param string $password
     * @param string $rights
     * @param int    $shopId
     */
    public function createAdmin(
        string $email,
        string $password,
        string $rights,
        int $shopId
    ): void;
}
