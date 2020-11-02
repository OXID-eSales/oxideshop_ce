<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Form\FormFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;

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
