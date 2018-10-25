<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

/**
 * @inheritdoc
 * @internal
 */
class FactsContext implements FactsContextInterface
{
    /**
     * @var Facts
     */
    private $facts;

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
    public function getSourcePath(): string
    {
        return $this->getFacts()->getSourcePath();
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
}
