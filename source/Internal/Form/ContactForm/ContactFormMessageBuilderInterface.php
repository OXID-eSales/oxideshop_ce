<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

/**
 * Class ContactFormMessageInterface
 */
interface ContactFormMessageBuilderInterface
{
    /**
     * @param FormInterface $form
     * @return string
     */
    public function getContent(FormInterface $form);
}
