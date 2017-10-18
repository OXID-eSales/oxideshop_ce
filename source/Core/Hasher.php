<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Hasher abstract class
 */
abstract class Hasher
{
    /**
     * Hash string.
     *
     * @param string $string string for hashing.
     *
     * @return string
     */
    abstract public function hash($string);
}
