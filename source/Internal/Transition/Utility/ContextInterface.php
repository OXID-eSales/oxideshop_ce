<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;

interface ContextInterface extends BasicContextInterface
{
    public function getAdminLogFilePath(): string;

    /** @throws AdminUserNotFoundException */
    public function getAdminUserId(): string;

    public function getCurrentShopId(): int;

    public function getLogFilePath(): string;

    public function getLogLevel(): string;

    public function getRequiredContactFormFields(): array;

    public function getSkipLogTags(): array;

    public function isAdmin(): bool;

    public function isShopInDemoMode(): bool;

    public function isShopInProductiveMode(): bool;
}
