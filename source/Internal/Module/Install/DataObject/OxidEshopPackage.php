<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\DataObject;

/**
 * @internal
 */
class OxidEshopPackage
{
    /** @var string $name */
    private $name;

    /**
     * @var array
     */
    private $blackListFilters;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @param string $name
     * @param array  $extraParameters
     */
    public function __construct(string $name, array $extraParameters)
    {
        $this->name = $name;

        $this->blackListFilters = $extraParameters['oxideshop']['blacklist-filter'] ?? [];
        $this->sourceDirectory  = $extraParameters['oxideshop']['source-directory'] ?? '';
        $this->targetDirectory  = $extraParameters['oxideshop']['target-directory'] ?? $this->name;
    }

    /**
     * @return array
     */
    public function getBlackListFilters(): array
    {
        return $this->blackListFilters;
    }

    /**
     * @return string
     */
    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
