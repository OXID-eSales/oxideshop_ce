<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Framework\Form\RequiredFieldsValidator;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormEmailValidator;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;

class ContactFormFactoryTest extends \PHPUnit\Framework\TestCase
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

    public function testFromConfigurationHandling()
    {
        $emailField =  new FormField();
        $emailField
            ->setName('email')
            ->setLabel('EMAIL');

        $firstNameField = new FormField();
        $firstNameField->setName('firstName');

        $lastNameField = new FormField();
        $lastNameField
            ->setName('lastName')
            ->setIsRequired(true);

        $emailConfiguration = new FieldConfiguration();
        $emailConfiguration
            ->setName('email')
            ->setLabel('EMAIL');

        $firstNameConfiguration = new FieldConfiguration();
        $firstNameConfiguration
            ->setName('firstName');

        $lastNameConfiguration = new FieldConfiguration();
        $lastNameConfiguration
            ->setName('lastName')
            ->setIsRequired(true);

        $formConfiguration = new FormConfiguration();
        $formConfiguration
            ->addFieldConfiguration($emailConfiguration)
            ->addFieldConfiguration($firstNameConfiguration)
            ->addFieldConfiguration($lastNameConfiguration);

        $contactFormFactory = $this->getContactFormFactory($formConfiguration);
        $form = $contactFormFactory->getForm();

        $this->assertEquals(
            [
                'email'     => $emailField,
                'firstName' => $firstNameField,
                'lastName'  => $lastNameField,
            ],
            $form->getFields()
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
