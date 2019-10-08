<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Form\FormFieldInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

class ContactFormEmailValidator implements FormValidatorInterface
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * ContactFormEmailValidator constructor.
     * @param ShopAdapterInterface $shopAdapter
     */
    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        $this->shopAdapter = $shopAdapter;
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
                ->shopAdapter
                ->isValidEmail($email->getValue());

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
