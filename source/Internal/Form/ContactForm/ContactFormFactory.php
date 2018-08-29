<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\Form;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormFieldInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;

/**
 * Class ContactFormFactory
 * @internal
 */
class ContactFormFactory implements FormFactoryInterface
{
    /**
     * @var FormConfigurationInterface
     */
    private $contactFormConfiguration;

    /**
     * @var FormValidatorInterface
     */
    private $contactFormEmailValidator;

    /**
     * @var FormValidatorInterface
     */
    private $requiredFieldsValidator;

    /**
     * ContactFormFactory constructor.
     * @param FormConfigurationInterface $contactFormConfiguration
     * @param FormValidatorInterface     $contactFormEmailValidator
     * @param FormValidatorInterface     $requiredFieldsValidator
     */
    public function __construct(
        FormConfigurationInterface  $contactFormConfiguration,
        FormValidatorInterface      $contactFormEmailValidator,
        FormValidatorInterface      $requiredFieldsValidator
    ) {
        $this->contactFormConfiguration = $contactFormConfiguration;
        $this->contactFormEmailValidator = $contactFormEmailValidator;
        $this->requiredFieldsValidator = $requiredFieldsValidator;
    }


    /**
     * @return FormInterface
     */
    public function getForm()
    {
        $form = new Form();

        foreach ($this->contactFormConfiguration->getFieldConfigurations() as $fieldConfiguration) {
            $field = $this->getFormField($fieldConfiguration);
            $form->add($field);
        }

        $form->addValidator($this->requiredFieldsValidator);
        $form->addValidator($this->contactFormEmailValidator);

        return $form;
    }

    /**
     * @param FieldConfigurationInterface $fieldConfiguration
     * @return FormFieldInterface
     */
    private function getFormField(FieldConfigurationInterface $fieldConfiguration)
    {
        $field = new FormField();
        $field
            ->setName($fieldConfiguration->getName())
            ->setLabel($fieldConfiguration->getLabel())
            ->setIsRequired($fieldConfiguration->isRequired());

        return $field;
    }
}
