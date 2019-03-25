<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

use ArrayAccess;

/**
 * Class PasswordHashStrategiesArray
 *
 * @package OxidEsales\EshopCommunity\Internal\Password\Service
 */
class PasswordHashStrategiesArray implements ArrayAccess
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
        if (!$value instanceof PasswordHashStrategyInterface) {
            throw new \RuntimeException('The array value must be an instance of PasswordHashStrategyInterface');
        }

        if ($offset === null) {
            throw new \RuntimeException('The array key must be set');
        }

        $this->container[$offset] = $value;
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
     * @return PasswordHashStrategyInterface
     */
    public function offsetGet($offset): PasswordHashStrategyInterface
    {
        if (!isset($this->container[$offset])) {
            throw new \RuntimeException('The requested password hash strategy is not available: ' . $offset);
        }
        return $this->container[$offset] ?? null;
    }
}
