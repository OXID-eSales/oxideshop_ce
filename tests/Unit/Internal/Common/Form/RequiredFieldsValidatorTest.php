<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form;

use OxidEsales\EshopCommunity\Internal\Common\Form\Form;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsValidator;

class RequiredFieldsValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidFormValidation()
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('requiredField');
        $field->setIsRequired(true);

        $form->add($field);

        $requiredFieldsValidator = new RequiredFieldsValidator();

        $this->assertFalse($requiredFieldsValidator->isValid($form));
    }

    public function testValidFormValidation()
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('requiredField');
        $field->setIsRequired(true);
        $field->setValue('123');

        $form->add($field);

        $requiredFieldsValidator = new RequiredFieldsValidator();

        $this->assertTrue($requiredFieldsValidator->isValid($form));
    }

    public function testInvalidFormValidationErrors()
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('requiredField');
        $field->setIsRequired(true);

        $form->add($field);

        $requiredFieldsValidator = new RequiredFieldsValidator();
        $requiredFieldsValidator->isValid($form);

        $this->assertSame(
            ['ERROR_MESSAGE_INPUT_NOTALLFIELDS'],
            $requiredFieldsValidator->getErrors()
        );
    }
}
