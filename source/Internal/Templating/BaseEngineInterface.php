<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

use Symfony\Component\Templating\EngineInterface;

/**
 * Interface BaseEngineInterface
 */
interface BaseEngineInterface extends EngineInterface
{
    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value);

    /**
     * Returns the assigned globals.
     *
     * @return array
     */
    public function getGlobals();

    /**
     * @param string $cacheId
     */
    public function setCacheId($cacheId);
}
