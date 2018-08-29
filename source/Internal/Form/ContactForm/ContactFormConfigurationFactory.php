<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfiguration;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormFieldsConfigurationDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * Class ContactFormConfigurationFactory
 * @internal
 */
class ContactFormConfigurationFactory implements FormConfigurationFactoryInterface
{
    /**
     * @var FormFieldsConfigurationDataProviderInterface
     */
    private $contactFormConfigurationDataProvider;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * ContactFormConfigurationFactory constructor.
     * @param FormFieldsConfigurationDataProviderInterface $contactFormConfigurationDataProvider
     * @param ContextInterface                             $context
     */
    public function __construct(
        FormFieldsConfigurationDataProviderInterface $contactFormConfigurationDataProvider,
        ContextInterface $context
    ) {
        $this->contactFormConfigurationDataProvider = $contactFormConfigurationDataProvider;
        $this->context = $context;
    }


    /**
     * @return FormConfigurationInterface
     */
    public function getFormConfiguration()
    {
        $formConfiguration = new FormConfiguration();

        $fieldsConfigurationData = $this
            ->contactFormConfigurationDataProvider
            ->getFormFieldsConfiguration();

        foreach ($fieldsConfigurationData as $fieldConfigurationData) {
            $fieldConfiguration = $this->getFieldConfiguration($fieldConfigurationData);
            $formConfiguration->addFieldConfiguration($fieldConfiguration);
        }

        return $formConfiguration;
    }

    /**
     * @param array $fieldConfigurationData
     * @return FieldConfiguration
     */
    private function getFieldConfiguration($fieldConfigurationData)
    {
        $fieldConfiguration = new FieldConfiguration();
        $fieldConfiguration->setName($fieldConfigurationData['name']);
        $fieldConfiguration->setLabel($fieldConfigurationData['label']);

        if ($this->isFieldRequired($fieldConfiguration)) {
            $fieldConfiguration->setIsRequired(true);
        }

        return $fieldConfiguration;
    }

    /**
     * @param FieldConfigurationInterface $fieldConfiguration
     * @return bool
     */
    private function isFieldRequired(FieldConfigurationInterface $fieldConfiguration)
    {
        return in_array(
            $fieldConfiguration->getName(),
            $this->context->getRequiredContactFormFields()
        );
    }
}
