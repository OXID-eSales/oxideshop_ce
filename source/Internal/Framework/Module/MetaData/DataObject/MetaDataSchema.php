<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataObject;

use function in_array;

class MetaDataSchema
{
    private array $keys;
    private array $sections;

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return in_array($key, $this->getKeys(), true);
    }

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }

    /**
     * @param string $sectionName
     * @return bool
     */
    public function hasSection(string $sectionName): bool
    {
        return isset($this->getSections()[$sectionName]);
    }

    /**
     * @param string $sectionName
     * @param string $key
     * @return bool
     */
    public function hasSectionKey(string $sectionName, string $key): bool
    {
        return $this->hasSection($sectionName)
            && in_array($key, $this->getSections()[$sectionName], true);
    }
}
