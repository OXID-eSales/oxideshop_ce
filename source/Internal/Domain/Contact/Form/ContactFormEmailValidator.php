<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormFieldInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormValidatorInterface;

class ContactFormEmailValidator implements FormValidatorInterface
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @var EmailValidatorServiceInterface
     */
    private $emailValidatorService;

    /**
     * ContactFormEmailValidator constructor.
     *
     * @param EmailValidatorServiceInterface $emailValidatorService
     */
    public function __construct(EmailValidatorServiceInterface $emailValidatorService)
    {
        $this->emailValidatorService = $emailValidatorService;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function isValid(FormInterface $form)
    {
        $isValid = true;
        $email = $form->email;

        if ($this->isValidationNeeded($email)) {
            $isValid = $this
                ->emailValidatorService
                ->isEmailValid($email->getValue());

            if ($isValid !== true) {
                $this->errors[] = 'ERROR_MESSAGE_INPUT_NOVALIDEMAIL';
            }
        }

        return $isValid;
    }

    /**
     * @param FormFieldInterface $email
     * @return bool
     */
    private function isValidationNeeded(FormFieldInterface $email)
    {
        return $this->isNotEmptyEmail($email) || $email->isRequired();
    }

    /**
     * @param FormFieldInterface $email
     * @return bool
     */
    private function isNotEmptyEmail(FormFieldInterface $email)
    {
        return $email->getValue() !== '';
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
