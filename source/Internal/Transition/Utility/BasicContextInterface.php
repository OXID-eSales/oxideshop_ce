<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;

interface BasicContextInterface
{
    public function getActiveModuleServicesFilePath(int $shopId): string;

    public function getAllShopIds(): array;

    public function getCacheDirectory(): string;

    public function getComposerVendorName(): string;

    public function getConfigTableName(): string;

    public function getContainerCacheFilePath(int $shopId): string;

    public function getDatabaseUrl(): string;

    public function getDefaultShopId(): int;

    public function getEdition(): Edition;

    /**
     * @throws DirectoryNotExistentException
     */
    public function getEditionSourcePath(Edition $edition): string;

    public function getGeneratedServicesFilePath(): string;

    public function getModuleCacheDirectory(): string;

    public function getOutPath(): string;

    public function getProjectConfigurationDirectory(): string;

    public function getShopBaseUrl(): string;

    public function getShopConfigurationDirectory(int $shopId): string;

    public function getShopRootPath(): string;

    public function getSourcePath(): string;

    public function getVendorPath(): string;
}
