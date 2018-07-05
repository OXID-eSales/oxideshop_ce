<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\Form;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormEmailValidator;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;

class ContactFormEmailValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidEmailValidation()
    {
        $validator = $this->getContactFormEmailValidator();

        $invalidEmailField = new FormField();
        $invalidEmailField->setName('email');
        $invalidEmailField->setValue('ImSoInvalid');

        $form = new Form();
        $form->add($invalidEmailField);

        $this->assertFalse(
            $validator->isValid($form)
        );

        $this->assertSame(
            ['ERROR_MESSAGE_INPUT_NOVALIDEMAIL'],
            $validator->getErrors()
        );
    }

    public function testValidEmailValidation()
    {
        $validator = $this->getContactFormEmailValidator();

        $validEmailField = new FormField();
        $validEmailField->setName('email');
        $validEmailField->setValue('someemail@validEmailsClub.com');

        $form = new Form();
        $form->add($validEmailField);

        $this->assertTrue(
            $validator->isValid($form)
        );
    }

    public function testEmptyEmailIsNotValidIfEmailIsRequired()
    {
        $validator = $this->getContactFormEmailValidator();

        $emailField = new FormField();
        $emailField
            ->setName('email')
            ->setValue('')
            ->setIsRequired(true);

        $form = new Form();
        $form->add($emailField);

        $this->assertFalse(
            $validator->isValid($form)
        );
    }

    public function testEmptyEmailIsValidIfEmailIsRequired()
    {
        $validator = $this->getContactFormEmailValidator();

        $emailField = new FormField();
        $emailField
            ->setName('email')
            ->setValue('');

        $form = new Form();
        $form->add($emailField);

        $this->assertTrue(
            $validator->isValid($form)
        );
    }

    private function getContactFormEmailValidator()
    {
        return new ContactFormEmailValidator(
            new ShopAdapter()
        );
    }
}
