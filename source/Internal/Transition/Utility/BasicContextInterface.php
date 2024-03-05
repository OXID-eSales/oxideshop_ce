<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\Facts\Facts;

interface BasicContextInterface
{
    public function getActiveModuleServicesFilePath(int $shopId): string;

    public function getAllShopIds(): array;

    public function getBackwardsCompatibilityClassMap(): array;

    public function getCacheDirectory(): string;

    public function getCommunityEditionSourcePath(): string;

    public function getComposerVendorName(): string;

    public function getConfigFilePath(): string;

    public function getConfigTableName(): string;

    public function getConfigurableServicesFilePath(): string;

    public function getConfigurationDirectoryPath(): string;

    public function getContainerCacheFilePath(int $shopId): string;

    public function getDatabaseUrl(): string;

    public function getDefaultShopId(): int;

    public function getEdition(): string;

    public function getEnterpriseEditionRootPath(): string;

    public function getFacts(): Facts;

    public function getGeneratedServicesFilePath(): string;

    public function getModuleCacheDirectory(): string;

    public function getOutPath(): string;

    public function getProfessionalEditionRootPath(): string;

    public function getProjectConfigurationDirectory(): string;

    public function getShopConfigurableServicesFilePath(int $shopId): string;

    public function getShopConfigurationDirectory(int $shopId): string;

    public function getShopRootPath(): string;

    public function getSourcePath(): string;

    public function getVendorPath(): string;
}
