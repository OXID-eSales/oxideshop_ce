<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

/**
 * Class ContactFormBridge
 * @package OxidEsales\EshopCommunity\Internal\Form\ContactForm
 */
class ContactFormBridge implements ContactFormBridgeInterface
{
    /**
     * @var ContactFormFactory
     */
    private $contactFormFactory;

    /**
     * ContactFormBridge constructor.
     * @param ContactFormFactory $contactFormFactory
     */
    public function __construct(ContactFormFactory $contactFormFactory)
    {
        $this->contactFormFactory = $contactFormFactory;
    }

    /**
     * @inheritDoc
     */
    public function getContactForm()
    {
        return $this->contactFormFactory->getForm();
    }
}
