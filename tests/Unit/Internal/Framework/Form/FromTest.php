<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form;

use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Form\Form;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormValidatorInterface;

final class FromTest extends TestCase
{
    public function testAddField(): void
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('testField');

        $form->add($field);

        $this->assertSame($field, $form->testField);
    }

    public function testValidation(): void
    {
        $validator = $this->getMockBuilder(FormValidatorInterface::class)->getMock();
        $validator
            ->method('isValid')
            ->willReturn(false);

        $validator
            ->method('getErrors')
            ->willReturn([]);

        $form = new Form();
        $form->addValidator($validator);

        $this->assertFalse($form->isValid());
    }

    public function testValidationErrors(): void
    {
        $validator = $this->getMockBuilder(FormValidatorInterface::class)->getMock();
        $validator
            ->method('isValid')
            ->willReturn(false);

        $validator
            ->method('getErrors')
            ->willReturn([
                'something is wrong',
                'alles ist kaput',
            ]);

        $anotherValidator = $this->getMockBuilder(FormValidatorInterface::class)->getMock();
        $anotherValidator
            ->method('isValid')
            ->willReturn(false);

        $anotherValidator
            ->method('getErrors')
            ->willReturn([
                'everything is wrong',
                'etwas ist kaput',
            ]);

        $form = new Form();
        $form->addValidator($validator);
        $form->addValidator($anotherValidator);

        $form->isValid();

        $this->assertSame(
            [
                'something is wrong',
                'alles ist kaput',
                'everything is wrong',
                'etwas ist kaput',
            ],
            $form->getErrors()
        );
    }

    public function testFieldsGetter(): void
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('testField');

        $anotherField = new FormField();
        $anotherField->setName('anotherTestField');

        $form->add($field);
        $form->add($anotherField);

        $this->assertEquals(
            $form->getFields(),
            [
                'testField'         => $field,
                'anotherTestField'  => $anotherField,
            ]
        );
    }

    public function testRequestHandling(): void
    {
        $form = new Form();

        $field = new FormField();
        $field->setName('testField');

        $form->add($field);
        $form->handleRequest([
            'testField' => 'testValue',
        ]);

        $this->assertSame(
            'testValue',
            $form->testField->getValue()
        );
    }
}
