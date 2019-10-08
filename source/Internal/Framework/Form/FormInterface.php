<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

interface FormInterface
{
    /**
     * @param FormFieldInterface $field
     */
    public function add(FormFieldInterface $field);

    /**
     * @return array
     */
    public function getFields();

    /**
     * @param array $request
     */
    public function handleRequest($request);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function getErrors();
}
