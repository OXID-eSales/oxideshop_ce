<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

use OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdException;

/**
 * @internal
 */
class ProjectConfiguration
{
    /** @var array */
    private $environmentConfigurations = [];

    /**
     * @return array
     */
    public function getNamesOfEnvironmentConfigurations(): array
    {
        return array_keys($this->environmentConfigurations);
    }

    /**
     * @param string $name
     *
     * @throws InvalidObjectIdException
     *
     * @return EnvironmentConfiguration
     */
    public function getEnvironmentConfiguration(string $name): EnvironmentConfiguration
    {
        if (array_key_exists($name, $this->environmentConfigurations)) {
            return $this->environmentConfigurations[$name];
        }
        throw new InvalidObjectIdException();
    }

    /**
     * @param string                   $name
     * @param EnvironmentConfiguration $environmentConfiguration
     */
    public function setEnvironmentConfiguration(string $name, EnvironmentConfiguration $environmentConfiguration)
    {
        $this->environmentConfigurations[$name] = $environmentConfiguration;
    }

    /**
     * @param string $name
     *
     * @throws InvalidObjectIdException
     */
    public function deleteEnvironmentConfiguration(string $name)
    {
        if (array_key_exists($name, $this->environmentConfigurations)) {
            unset($this->environmentConfigurations[$name]);
        } else {
            throw new InvalidObjectIdException();
        }
    }
}
