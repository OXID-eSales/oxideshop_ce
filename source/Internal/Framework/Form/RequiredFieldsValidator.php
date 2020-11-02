<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

class RequiredFieldsValidator implements FormValidatorInterface
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @return bool
     */
    public function isValid(FormInterface $form)
    {
        $isValid = true;

        foreach ($form->getFields() as $field) {
            if (true === $field->isRequired() && !$field->getValue()) {
                $this->addError();
                $isValid = false;

                break;
            }
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add error.
     */
    private function addError(): void
    {
        $this->errors[] = 'ERROR_MESSAGE_INPUT_NOTALLFIELDS';
    }
}
