<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;

/**
 * Class ContactFormEmailValidator
 */
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
        $email = $form->email->getValue();
        $isValid = $this->shopAdapter->isValidEmail($email);

        if ($isValid !== true) {
            $this->errors[] = 'ERROR_MESSAGE_INPUT_NOVALIDEMAIL';
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
}
