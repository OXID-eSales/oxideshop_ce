<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;

/**
 * @internal
 */
interface ContextInterface extends BasicContextInterface
{
    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @return int
     */
    public function getCurrentShopId(): int;

    /**
     * @return array
     */
    public function getAllShopIds(): array;

    /**
     * @return string
     */
    public function getContainerCacheFile(): string;

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
     * @return string
     */
    public function getConfigurationEncryptionKey(): string;
}
