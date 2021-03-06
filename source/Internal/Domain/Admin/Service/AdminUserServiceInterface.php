<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Service;

interface AdminUserServiceInterface
{
    public function createAdmin(
        string $email,
        string $password,
        string $rights,
        int $shopId
    ): void;
}
