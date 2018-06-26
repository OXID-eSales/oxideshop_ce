<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form;

use OxidEsales\EshopCommunity\Internal\Common\Form\FormBuilder;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormFieldInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

class FromBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testFormGetter()
    {
        $formBuilder = new FormBuilder();

        $this->assertInstanceOf(
            FormInterface::class,
            $formBuilder->getForm()
        );
    }

    public function testAddFormField()
    {
        $formBuilder = new FormBuilder();

        $form = $formBuilder
            ->add('testField')
            ->getForm();

        $this->assertInstanceOf(
            FormFieldInterface::class,
            $form->testField
        );
    }

    public function testAddFormFieldWithLabel()
    {
        $formBuilder = new FormBuilder();

        $form = $formBuilder
            ->add('testField', [
                'label' => 'superField!',
            ])
            ->getForm();

        $this->assertSame(
            'superField!',
            $form->testField->getLabel()
        );
    }

    public function testAddRequiredFormField()
    {
        $formBuilder = new FormBuilder();

        $form = $formBuilder
            ->add('testField', [
                'required' => true,
            ])
            ->getForm();

        $this->assertSame(
            true,
            $form->testField->isRequired()
        );
    }
}
