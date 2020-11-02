<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

interface FormValidatorInterface
{
    /**
     * @return bool
     */
    public function isValid(FormInterface $form);

    /**
     * @return array
     */
    public function getErrors();
}
