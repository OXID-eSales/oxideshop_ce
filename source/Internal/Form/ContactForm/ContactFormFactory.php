<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormBuilderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;

/**
 * Class ContactFormFactory
 */
class ContactFormFactory implements FormFactoryInterface
{
    /**
     * @var RequiredFieldsProviderInterface
     */
    private $requiredFieldsProvider;

    /**
     * @var FormBuilderInterface
     */
    private $formBuilder;

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
     * @param RequiredFieldsProviderInterface $requiredFieldsProvider
     * @param FormBuilderInterface            $formBuilder
     * @param FormValidatorInterface          $contactFormEmailValidator
     * @param FormValidatorInterface          $requiredFieldsValidator
     */
    public function __construct(
        RequiredFieldsProviderInterface $requiredFieldsProvider,
        FormBuilderInterface            $formBuilder,
        FormValidatorInterface          $contactFormEmailValidator,
        FormValidatorInterface          $requiredFieldsValidator
    ) {
        $this->requiredFieldsProvider = $requiredFieldsProvider;
        $this->formBuilder = $formBuilder;
        $this->contactFormEmailValidator = $contactFormEmailValidator;
        $this->requiredFieldsValidator = $requiredFieldsValidator;
    }


    /**
     * @return FormInterface
     */
    public function getForm()
    {
        $this
            ->formBuilder
            ->add('email', [
                'label'     => 'EMAIL',
                'required'  => $this->isFieldRequired('email'),
            ])
            ->add('firstName', [
                'label'     => 'FIRST_NAME',
                'required'  => $this->isFieldRequired('firstName'),
            ])
            ->add('lastName', [
                'label'     => 'LAST_NAME',
                'required'  => $this->isFieldRequired('lastName'),
            ])
            ->add('salutation', [
                'label'     => 'TITLE',
                'required'  => $this->isFieldRequired('salutation'),
            ])
            ->add('subject', [
                'label'     => 'SUBJECT',
                'required'  => $this->isFieldRequired('subject'),
            ])
            ->add('message', [
                'label'     => 'MESSAGE',
                'required'  => $this->isFieldRequired('message'),
            ]);

        $form = $this->formBuilder->getForm();
        $form->addValidator($this->requiredFieldsValidator);
        $form->addValidator($this->contactFormEmailValidator);

        return $form;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    private function isFieldRequired($fieldName)
    {
        return in_array(
            $fieldName,
            $this->requiredFieldsProvider->getRequiredFields()
        );
    }
}
