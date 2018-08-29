<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;

/**
 * Interface ContactFormBridgeInterface
 */
interface ContactFormBridgeInterface
{
    /**
     * @return FormInterface
     */
    public function getContactForm();

    /**
     * @param FormInterface $form
     * @return string
     */
    public function getContactFormMessage(FormInterface $form);

    /**
     * @return FormConfigurationInterface
     */
    public function getContactFormConfiguration();
}
