<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\FormConfiguration;

/**
 * @internal
 */
interface FieldConfigurationInterface
{
    public function getName();

    /**
     * @param string $name
     * @return FieldConfiguration
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return FieldConfiguration
     */
    public function setLabel($label);

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @param bool $isRequired
     * @return FieldConfiguration
     */
    public function setIsRequired($isRequired);

    /**
     * @return bool
     */
    public function isAlwaysRequired();

    /**
     * @param bool $isAlwaysRequired
     * @return FieldConfiguration
     */
    public function setIsAlwaysRequired($isAlwaysRequired);
}
