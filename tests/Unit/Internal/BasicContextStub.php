<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Application\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;

/**
 * @internal
 */
class BasicContextStub implements BasicContextInterface
{
    private $communityEditionSourcePath;
    private $containerCacheFilePath;
    private $edition;
    private $enterpriseEditionRootPath;
    private $generatedProjectFilePath;
    private $professionalEditionRootPath;
    private $sourcePath;
    private $modulesPath;

    public function __construct()
    {
        $basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);

        $this->communityEditionSourcePath = $basicContext->getCommunityEditionSourcePath();
        $this->containerCacheFilePath = $basicContext->getContainerCacheFilePath();
        $this->edition = $basicContext->getEdition();
        $this->enterpriseEditionRootPath = $basicContext->getEnterpriseEditionRootPath();
        $this->generatedProjectFilePath = $basicContext->getGeneratedProjectFilePath();
        $this->professionalEditionRootPath = $basicContext->getProfessionalEditionRootPath();
        $this->sourcePath = $basicContext->getSourcePath();
        $this->modulesPath = $basicContext->getModulesPath();
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return 'dev';
    }

    /**
     * @return string
     */
    public function getCommunityEditionSourcePath(): string
    {
        return $this->communityEditionSourcePath;
    }

    /**
     * @param string $communityEditionSourcePath
     */
    public function setCommunityEditionSourcePath(string $communityEditionSourcePath)
    {
        $this->communityEditionSourcePath = $communityEditionSourcePath;
    }

    /**
     * @return string
     */
    public function getContainerCacheFilePath(): string
    {
        return $this->containerCacheFilePath;
    }

    /**
     * @param string $containerCacheFilePath
     */
    public function setContainerCacheFilePath(string $containerCacheFilePath)
    {
        $this->containerCacheFilePath = $containerCacheFilePath;
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->edition;
    }

    /**
     * @param string $edition
     */
    public function setEdition(string $edition)
    {
        $this->edition = $edition;
    }

    /**
     * @return string
     */
    public function getEnterpriseEditionRootPath(): string
    {
        return $this->enterpriseEditionRootPath;
    }

    /**
     * @param string $enterpriseEditionRootPath
     */
    public function setEnterpriseEditionRootPath(string $enterpriseEditionRootPath)
    {
        $this->enterpriseEditionRootPath = $enterpriseEditionRootPath;
    }

    /**
     * @return string
     */
    public function getGeneratedProjectFilePath(): string
    {
        return $this->generatedProjectFilePath;
    }

    /**
     * @param string $generatedProjectFilePath
     */
    public function setGeneratedProjectFilePath(string $generatedProjectFilePath)
    {
        $this->generatedProjectFilePath = $generatedProjectFilePath;
    }

    /**
     * @return string
     */
    public function getProfessionalEditionRootPath(): string
    {
        return $this->professionalEditionRootPath;
    }

    /**
     * @param string $professionalEditionRootPath
     */
    public function setProfessionalEditionRootPath(string $professionalEditionRootPath)
    {
        $this->professionalEditionRootPath = $professionalEditionRootPath;
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    /**
     * @param string $sourcePath
     */
    public function setSourcePath(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return bool
     */
    public function isShopLaunched(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return [$this->getDefaultShopId()];
    }

    /**
     * @return string
     */
    public function getModulesPath(): string
    {
        return $this->modulesPath;
    }

    /**
     * @return array
     */
    public function getBackwardsCompatibilityClassMap(): array
    {
        return [];
    }
}
