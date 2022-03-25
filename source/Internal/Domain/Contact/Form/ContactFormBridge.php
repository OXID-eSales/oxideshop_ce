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
     * ContactFormBridge constructor.
     */
    public function __construct(private FormFactoryInterface $contactFormFactory, private ContactFormMessageBuilderInterface $contactFormMessageBuilder, private FormConfigurationInterface $contactFormConfiguration)
    {
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
