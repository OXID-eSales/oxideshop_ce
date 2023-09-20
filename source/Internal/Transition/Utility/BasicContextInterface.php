<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\Facts\Facts;

interface BasicContextInterface
{
    /**
     * @return string
     */
    public function getContainerCacheFilePath(int $shopId): string;

    /**
     * @return string
     */
    public function getGeneratedServicesFilePath(): string;

    /**
     * @return string
     */
    public function getConfigurableServicesFilePath(): string;

    public function getShopConfigurableServicesFilePath(int $shopId): string;

    /**
     * @return string
     */
    public function getActiveModuleServicesFilePath(int $shopId): string;

    /**
     * @return string
     */
    public function getSourcePath(): string;

    /**
     * @return string
     */
    public function getEdition(): string;

    /**
     * @return string
     */
    public function getCommunityEditionSourcePath(): string;

    /**
     * @return string
     */
    public function getProfessionalEditionRootPath(): string;

    /**
     * @return string
     */
    public function getEnterpriseEditionRootPath(): string;

    /**
     * @return string
     */
    public function getOutPath(): string;

    /**
     * @return int
     */
    public function getDefaultShopId(): int;

    /**
     * @return int
     */
    public function getCurrentShopId(): int;

    /**
     * @return array
     */
    public function getAllShopIds(): array;

    /**
     * @return array
     */
    public function getBackwardsCompatibilityClassMap(): array;

    /**
     * @return string
     */
    public function getProjectConfigurationDirectory(): string;

    /**
     * @param int $shopId
     * @return string
     */
    public function getShopConfigurationDirectory(int $shopId): string;

    /**
     * @return string
     */
    public function getConfigurationDirectoryPath(): string;

    /**
     * @return string
     */
    public function getShopRootPath(): string;

    /**
     * @return string
     */
    public function getVendorPath(): string;

    /**
     * @return string
     */
    public function getComposerVendorName(): string;

    /**
     * @return string
     */
    public function getConfigFilePath(): string;

    /**
     * @return string
     */
    public function getConfigTableName(): string;

    /**
     * @return Facts
     */
    public function getFacts(): Facts;

    /**
     * @return string
     */
    public function getCacheDirectory(): string;

    public function getModuleCacheDirectory(): string;

    public function getTemplateCacheDirectory(): string;
}
