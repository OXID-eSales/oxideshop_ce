<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

/**
 * @internal
 */
interface ShopStateServiceInterface
{
    /**
     * @return bool
     */
    public function isLaunched(): bool;

    /**
     * @param string $dbHost
     * @param int    $dbPort
     * @param string $dbUser
     * @param string $dbPwd
     * @param string $dbName
     *
     * @return bool
     */
    public function checkIfDbExistsAndNotEmpty(string $dbHost, int $dbPort, string $dbUser, string $dbPwd, string $dbName): bool;
}
