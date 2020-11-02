<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\Facts\Facts;

/**
 * Contains necessary methods to provide basic information about the application.
 */
interface BasicContextInterface
{
    public function getContainerCacheFilePath(): string;

    public function getGeneratedServicesFilePath(): string;

    public function getConfigurableServicesFilePath(): string;

    public function getSourcePath(): string;

    public function getModulesPath(): string;

    public function getEdition(): string;

    public function getCommunityEditionSourcePath(): string;

    public function getProfessionalEditionRootPath(): string;

    public function getEnterpriseEditionRootPath(): string;

    public function getOutPath(): string;

    public function getDefaultShopId(): int;

    public function getAllShopIds(): array;

    public function getBackwardsCompatibilityClassMap(): array;

    public function getProjectConfigurationDirectory(): string;

    public function getConfigurationDirectoryPath(): string;

    public function getShopRootPath(): string;

    public function getVendorPath(): string;

    public function getComposerVendorName(): string;

    public function getConfigFilePath(): string;

    public function getConfigTableName(): string;

    public function getFacts(): Facts;

    public function getCacheDirectory(): string;
}
