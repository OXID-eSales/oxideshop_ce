<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;

class AdminUserServiceBridge implements AdminUserServiceBridgeInterface
{
    /**
     * @var AdminUserServiceInterface
     */
    private $adminUserService;

    /**
     * @param AdminUserServiceInterface $adminUserService
     */
    public function __construct(AdminUserServiceInterface $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

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
    ): void {
        $this->adminUserService->createAdmin($email, $password, $rights, $shopId);
    }
}
