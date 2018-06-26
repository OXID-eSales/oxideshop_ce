<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\Form;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormBuilderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsValidator;

/**
 * Class ContactFormFactory
 */
class ContactFormFactory
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
     * ContactFormFactory constructor.
     * @param RequiredFieldsProviderInterface $requiredFieldsProvider
     * @param FormBuilderInterface            $formBuilder
     */
    public function __construct(
        RequiredFieldsProviderInterface $requiredFieldsProvider,
        FormBuilderInterface            $formBuilder
    ) {
        $this->requiredFieldsProvider = $requiredFieldsProvider;
        $this->formBuilder = $formBuilder;
    }


    /**
     * @return Form
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
        $form->addValidator(new RequiredFieldsValidator());

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
