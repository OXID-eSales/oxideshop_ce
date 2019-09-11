<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

/**
 * Interface FormValidatorInterface
 * @internal
 */
interface FormValidatorInterface
{
    /**
     * @param FormInterface $form
     * @return bool
     */
    public function isValid(FormInterface $form);

    /**
     * @return array
     */
    public function getErrors();
}
