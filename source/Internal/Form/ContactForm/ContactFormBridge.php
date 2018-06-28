<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

/**
 * Class ContactFormBridge
 * @package OxidEsales\EshopCommunity\Internal\Form\ContactForm
 */
class ContactFormBridge implements ContactFormBridgeInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $contactFormFactory;

    /**
     * @var ContactFormMessageBuilderInterface
     */
    private $contactFormMessageBuilder;

    /**
     * ContactFormBridge constructor.
     * @param FormFactoryInterface               $contactFormFactory
     * @param ContactFormMessageBuilderInterface $contactFormMessageBuilder
     */
    public function __construct(
        FormFactoryInterface $contactFormFactory,
        ContactFormMessageBuilderInterface $contactFormMessageBuilder
    ) {
        $this->contactFormFactory = $contactFormFactory;
        $this->contactFormMessageBuilder = $contactFormMessageBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getContactForm()
    {
        return $this->contactFormFactory->getForm();
    }

    /**
     * @param FormInterface $form
     * @return string
     */
    public function getContactFormMessage(FormInterface $form)
    {
        return $this->contactFormMessageBuilder->getContent($form);
    }
}
