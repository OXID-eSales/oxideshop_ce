<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Hasher abstract class
 *
 * @deprecated since v6.4.0 (2019-03-15); This class will be removed completely.
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
