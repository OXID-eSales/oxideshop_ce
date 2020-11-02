<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;

interface ContextInterface extends BasicContextInterface
{
    public function getCurrentShopId(): int;

    public function getLogLevel(): string;

    public function getLogFilePath(): string;

    public function getRequiredContactFormFields(): array;

    public function isEnabledAdminQueryLog(): bool;

    public function isAdmin(): bool;

    public function getAdminLogFilePath(): string;

    public function getSkipLogTags(): array;

    /**
     * @throws AdminUserNotFoundException
     */
    public function getAdminUserId(): string;
}
