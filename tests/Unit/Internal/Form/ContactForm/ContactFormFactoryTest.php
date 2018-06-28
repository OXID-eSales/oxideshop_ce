<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsValidator;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormEmailValidator;
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

        $contactFormFactory = $this->getContactFormFactoryWithRequiredFields($requiredFieldsProvider);

        $this->assertInstanceOf(
            FormInterface::class,
            $contactFormFactory->getForm()
        );
    }

    public function testRequiredFields()
    {
        $requiredFieldsProvider = $this->getMockBuilder(RequiredFieldsProviderInterface::class)->getMock();
        $requiredFieldsProvider
            ->method('getRequiredFields')
            ->willReturn(['lastName']);

        $contactFormFactory = $this->getContactFormFactoryWithRequiredFields($requiredFieldsProvider);

        $form = $contactFormFactory->getForm();

        $this->assertTrue(
            $form->lastName->isRequired()
        );

        $this->assertFalse(
            $form->email->isRequired()
        );
    }

    private function getContactFormFactoryWithRequiredFields($requiredFieldsProvider)
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();

        $contactFormFactory = new ContactFormFactory(
            $requiredFieldsProvider,
            new FormBuilder(),
            new RequiredFieldsValidator(),
            new ContactFormEmailValidator($shopAdapter)
        );

        return $contactFormFactory;
    }
}
