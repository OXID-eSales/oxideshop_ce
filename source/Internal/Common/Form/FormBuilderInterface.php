<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Form;

/**
 * Interface FormBuilderInterface
 */
interface FormBuilderInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * @param string $fieldName
     * @param array  $options
     */
    public function add($fieldName, $options = []);
}
