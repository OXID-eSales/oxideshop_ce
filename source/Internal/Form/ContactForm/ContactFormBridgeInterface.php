<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

/**
 * Interface ContactFormBridgeInterface
 */
interface ContactFormBridgeInterface
{
    /**
     * @return FormInterface
     */
    public function getContactForm();
}
