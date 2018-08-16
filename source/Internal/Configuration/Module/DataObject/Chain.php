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
class Chain
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $chain;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Chain
     */
    public function setId(string $id): Chain
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * @param array $chain
     * @return Chain
     */
    public function setChain(array $chain): Chain
    {
        $this->chain = $chain;
        return $this;
    }
}
