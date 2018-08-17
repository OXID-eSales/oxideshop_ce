<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

/**
 * @internal
 */
class ChainGroup
{
    /**
     * @var array
     */
    private $chains;

    /**
     * @param string $id
     * @return Chain
     */
    public function getChain(string $id): Chain
    {
        return $this->chains[$id];
    }

    /**
     * @param Chain $chain
     */
    public function setChain(Chain $chain)
    {
        $this->chains[$chain->getName()] = $chain;
    }
}
