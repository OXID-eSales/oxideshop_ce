<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration;

interface FieldConfigurationInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return FieldConfigurationInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return FieldConfigurationInterface
     */
    public function setLabel($label);

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @param bool $isRequired
     * @return FieldConfigurationInterface
     */
    public function setIsRequired($isRequired);
}
