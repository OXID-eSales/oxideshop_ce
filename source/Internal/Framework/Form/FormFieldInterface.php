<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

interface FormFieldInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return FormFieldInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return FormFieldInterface
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return FormFieldInterface
     */
    public function setLabel($label);
    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @param bool $isRequired
     * @return FormFieldInterface
     */
    public function setIsRequired($isRequired);
}
