<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsValidator;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfiguration;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormFieldsConfigurationDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormEmailValidator;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormFactory;
use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;

class ContactFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFormGetter()
    {
        $formConfiguration = new FormConfiguration();

        $contactFormFactory = $this->getContactFormFactory($formConfiguration);

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

        $contactFormFactory = $this->getContactFormFactory($requiredFieldsProvider);

        $form = $contactFormFactory->getForm();

        $this->assertTrue(
            $form->lastName->isRequired()
        );

        $this->assertFalse(
            $form->email->isRequired()
        );
    }

    private function getContactFormFactory(FormConfigurationInterface $formConfiguration)
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();

        $contactFormFactory = new ContactFormFactory(
            $formConfiguration,
            new RequiredFieldsValidator(),
            new ContactFormEmailValidator($shopAdapter)
        );

        return $contactFormFactory;
    }
}
