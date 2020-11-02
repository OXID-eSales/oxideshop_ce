<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;

interface ContactFormBridgeInterface
{
    /**
     * @return FormInterface
     */
    public function getContactForm();

    /**
     * @return string
     */
    public function getContactFormMessage(FormInterface $form);

    /**
     * @return FormConfigurationInterface
     */
    public function getContactFormConfiguration();
}
