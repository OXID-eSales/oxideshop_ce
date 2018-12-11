<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

/**
 * Contains necessary methods to provide basic information about the application.
 * @internal
 */
interface FactsContextInterface
{
    /**
     * @return string
     */
    public function getContainerCacheFilePath(): string;

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
}
