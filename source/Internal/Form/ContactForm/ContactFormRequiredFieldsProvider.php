<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\AlwaysRequiredFieldsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * Class ContactFormRequiredFieldsProvider
 */
class ContactFormRequiredFieldsProvider implements RequiredFieldsProviderInterface, AlwaysRequiredFieldsProviderInterface
{
    /** @var  ContextInterface */
    private $context;

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getRequiredFields()
    {
        return array_merge(
            $this->getAlwaysRequiredFields(),
            $this->getConfiguredRequiredFields()
        );
    }

    /**
     * @return array
     */
    public function getAlwaysRequiredFields()
    {
        return ['email'];
    }

    /**
     * @return array
     */
    private function getConfiguredRequiredFields()
    {
        return is_null($this->context->getRequiredContactFormFields()) ?
            [] : $this->context->getRequiredContactFormFields();
    }
}
