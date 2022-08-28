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
    /**
     * @return int
     */
    public function getCurrentShopId(): int;

    /**
     * @return string
     */
    public function getLogLevel(): string;

    /**
     * @return string
     */
    public function getLogFilePath(): string;

    /**
     * @return array
     */
    public function getRequiredContactFormFields(): array;

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool;

    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return string
     */
    public function getAdminLogFilePath(): string;

    /**
     * @return array
     */
    public function getSkipLogTags(): array;

    /**
     * @return string
     *
     * @throws AdminUserNotFoundException
     */
    public function getAdminUserId(): string;

    /**
     * @return bool
     */
    public function isShopInProductiveMode(): bool;
}
