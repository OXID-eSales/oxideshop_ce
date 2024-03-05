<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Contact\Form;

use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorService;
use OxidEsales\EshopCommunity\Internal\Framework\Form\Form;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormEmailValidator;

final class ContactFormEmailValidatorTest extends TestCase
{
    public function testInvalidEmailValidation(): void
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

    public function testValidEmailValidation(): void
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

    public function testEmptyEmailIsNotValidIfEmailIsRequired(): void
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

    public function testEmptyEmailIsValidIfEmailIsRequired(): void
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

    private function getContactFormEmailValidator(): ContactFormEmailValidator
    {
        return new ContactFormEmailValidator(
            new EmailValidatorService()
        );
    }
}
