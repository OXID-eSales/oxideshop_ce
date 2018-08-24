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
     * @return int
     */
    public function getCurrentShopId();

    /**
     * @return string
     */
    public function getLogLevel();

    /**
     * @return string
     */
    public function getLogFilePath();

    /**
     * @return array
     */
    public function getRequiredContactFormFields();

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string;

    /**
     * @return mixed
     */
    public function getContainerCacheFile();
}
