<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Utility;

use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Facts\Edition\EditionSelector;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

/**
 * @inheritdoc
 * @internal
 */
class BasicContext implements BasicContextInterface
{
    const COMMUNITY_EDITION = EditionSelector::COMMUNITY;

    const PROFESSIONAL_EDITION = EditionSelector::PROFESSIONAL;

    const ENTERPRISE_EDITION = EditionSelector::ENTERPRISE;

    const GENERATED_PROJECT_FILE_NAME = 'generated_project.yaml';

    /**
     * @var string
     */
    private $configFileName;

    /**
     * @var Facts
     */
    private $facts;

    /**
     * BasicContext constructor.
     * @param string $configFileName
     */
    public function __construct(string $configFileName)
    {
        $this->configFileName = $configFileName;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return 'prod';
    }

    /**
     * @todo change placement of containercache.php file and move logic to Facts.
     * @return string
     */
    public function getContainerCacheFilePath(): string
    {
        return Path::join($this->getSourcePath(), 'tmp', 'containercache.php');
    }

    /**
     * @return string
     */
    public function getGeneratedProjectFilePath(): string
    {
        return Path::join($this->getSourcePath(), static::GENERATED_PROJECT_FILE_NAME);
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->getFacts()->getSourcePath();
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->getFacts()->getEdition();
    }

    /**
     * @return string
     */
    public function getCommunityEditionSourcePath(): string
    {
        return $this->getFacts()->getCommunityEditionSourcePath();
    }

    /**
     * @return string
     */
    public function getProfessionalEditionRootPath(): string
    {
        return $this->getFacts()->getProfessionalEditionRootPath();
    }

    /**
     * @return string
     */
    public function getEnterpriseEditionRootPath(): string
    {
        return $this->getFacts()->getEnterpriseEditionRootPath();
    }

    /**
     * @return bool
     */
    public function isShopLaunched(): bool
    {
        $configFilePath = $this->getSourcePath() . DIRECTORY_SEPARATOR. $this->configFileName;

        return file_exists($configFilePath);
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    /**
     * @return Facts
     */
    private function getFacts(): Facts
    {
        if ($this->facts === null) {
            $this->facts = new Facts();
        }
        return $this->facts;
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return [
            $this->getDefaultShopId(),
        ];
    }
}
