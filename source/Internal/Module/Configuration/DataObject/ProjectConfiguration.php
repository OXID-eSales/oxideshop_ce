<?php declare(strict_types = 1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

use DomainException;

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
     * @return EnvironmentConfiguration[]
     */
    public function getEnvironmentConfigurations(): array
    {
        return $this->environmentConfigurations;
    }

    /**
     * @param string $name
     *
     * @throws DomainException
     *
     * @return EnvironmentConfiguration
     */
    public function getEnvironmentConfiguration(string $name): EnvironmentConfiguration
    {
        if (array_key_exists($name, $this->environmentConfigurations)) {
            return $this->environmentConfigurations[$name];
        }
        throw new DomainException('There is no environment configuration with name ' . $name);
    }

    /**
     * @param string                   $name
     * @param EnvironmentConfiguration $environmentConfiguration
     */
    public function addEnvironmentConfiguration(string $name, EnvironmentConfiguration $environmentConfiguration)
    {
        $this->environmentConfigurations[$name] = $environmentConfiguration;
    }

    /**
     * @param string $name
     *
     * @throws DomainException
     */
    public function deleteEnvironmentConfiguration(string $name)
    {
        if (array_key_exists($name, $this->environmentConfigurations)) {
            unset($this->environmentConfigurations[$name]);
        } else {
            throw new DomainException('There is no environment configuration with name ' . $name);
        }
    }
}
