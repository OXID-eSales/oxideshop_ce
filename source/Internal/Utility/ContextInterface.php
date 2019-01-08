<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

/**
 * @internal
 */
interface ContextInterface
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
     * @return string
     */
    public function getLogLevel();

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

    /**
     * @return mixed
     */
    public function getContainerCacheFile();
}
