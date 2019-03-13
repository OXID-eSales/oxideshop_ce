<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use ArrayAccess;

/**
 * Class PasswordHashServiceArray
 *
 * @package OxidEsales\EshopCommunity\Internal\Password\Service
 */
class PasswordHashServiceArray implements ArrayAccess
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof PasswordHashServiceInterface) {
            throw new \RuntimeException('value must be an instance of PasswordHashServiceInterface');
        }

        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return PasswordHashServiceInterface
     */
    public function offsetGet($offset): PasswordHashServiceInterface
    {
        return $this->container[$offset] ?? null;
    }
}
