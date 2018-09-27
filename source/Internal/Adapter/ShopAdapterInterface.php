<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter;

/**
 * @internal
 */
interface ShopAdapterInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function isValidEmail($email): bool;

    /**
     * @param string $string
     *
     * @return string
     */
    public function translateString($string): string;

    /**
     * @param string $moduleId
     */
    public function invalidateModuleCache(string $moduleId);

    /**
     * @return string
     */
    public function generateUniqueId(): string;

    /**
     * @return array
     */
    public function getModules();
}
