<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Utility;

/**
 * Contains necessary methods to provide basic information about the application.
 * @internal
 */
interface BasicContextInterface
{
    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @return string
     */
    public function getContainerCacheFilePath(): string;

    /**
     * @return string
     */
    public function getGeneratedServicesFilePath(): string;

    /**
     * @return string
     */
    public function getSourcePath(): string;

    /**
     * @return string
     */
    public function getModulesPath(): string;

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
     * @return int
     */
    public function getDefaultShopId(): int;

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
    public function getConfigFilePath(): string;

    /**
     * @return string
     */
    public function getConfigTableName(): string;
}
