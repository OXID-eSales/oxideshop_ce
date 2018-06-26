<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormFactory;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormBuilder;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

class ContactFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFormGetter()
    {
        $requiredFieldsProvider = $this
            ->getMockBuilder(RequiredFieldsProviderInterface::class)
            ->getMock();

        $requiredFieldsProvider
            ->method('getRequiredFields')
            ->willReturn(['email']);

        $formFactory = new ContactFormFactory(
            $requiredFieldsProvider,
            new FormBuilder()
        );

        $this->assertInstanceOf(
            FormInterface::class,
            $formFactory->getForm()
        );
    }

    public function testRequiredFields()
    {
        $requiredFieldsProvider = $this->getMockBuilder(RequiredFieldsProviderInterface::class)->getMock();
        $requiredFieldsProvider
            ->method('getRequiredFields')
            ->willReturn(['lastName']);

        $contactFormFactory = new ContactFormFactory(
            $requiredFieldsProvider,
            new FormBuilder()
        );

        $form = $contactFormFactory->getForm();

        $this->assertTrue(
            $form->lastName->isRequired()
        );

        $this->assertFalse(
            $form->email->isRequired()
        );
    }
}
