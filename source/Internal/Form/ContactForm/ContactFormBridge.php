<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;

/**
 * Class ContactFormBridge
 * @internal
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
     * @var FormConfigurationInterface
     */
    private $contactFormConfiguration;

    /**
     * ContactFormBridge constructor.
     * @param FormFactoryInterface               $contactFormFactory
     * @param ContactFormMessageBuilderInterface $contactFormMessageBuilder
     * @param FormConfigurationInterface         $contactFormConfiguration
     */
    public function __construct(
        FormFactoryInterface $contactFormFactory,
        ContactFormMessageBuilderInterface $contactFormMessageBuilder,
        FormConfigurationInterface $contactFormConfiguration
    ) {
        $this->contactFormFactory = $contactFormFactory;
        $this->contactFormMessageBuilder = $contactFormMessageBuilder;
        $this->contactFormConfiguration = $contactFormConfiguration;
    }

    /**
     * @return FormInterface
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

    /**
     * @return FormConfigurationInterface
     */
    public function getContactFormConfiguration()
    {
        return $this->contactFormConfiguration;
    }
}
