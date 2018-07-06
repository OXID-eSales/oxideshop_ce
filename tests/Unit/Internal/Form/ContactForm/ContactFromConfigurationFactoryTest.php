<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormFieldsConfigurationDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormConfigurationFactory;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

class ContactFromConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigurationGetter()
    {
        $context = $this->getMock(ContextInterface::class);

        $formFieldsConfigurationDataProvider = $this->getMock(FormFieldsConfigurationDataProviderInterface::class);
        $formFieldsConfigurationDataProvider
            ->method('getFormFieldsConfiguration')
            ->willReturn([]);

        $formConfigurationFactory = new ContactFormConfigurationFactory(
            $formFieldsConfigurationDataProvider,
            $context
        );

        $this->assertInstanceOf(
            FormConfigurationInterface::class,
            $formConfigurationFactory->getFormConfiguration()
        );
    }

    public function testFormFieldsConfiguration()
    {
        $context = $this->getMock(ContextInterface::class);
        $context
            ->method('getRequiredContactFormFields')
            ->willReturn([
                'name',
            ]);

        $formFieldsConfigurationDataProvider = $this->getMock(FormFieldsConfigurationDataProviderInterface::class);
        $formFieldsConfigurationDataProvider
            ->method('getFormFieldsConfiguration')
            ->willReturn([
                [
                    'name'              => 'email',
                    'label'             => 'EMAIL',
                ],
                [
                    'name'              => 'firstName',
                    'label'             => 'FIRST_NAME',
                    'required'          => true,
                ],
            ]);

        $formConfigurationFactory = new ContactFormConfigurationFactory(
            $formFieldsConfigurationDataProvider,
            $context
        );

        $contactFormConfiguration = $formConfigurationFactory->getFormConfiguration();

        $emailConfiguration = new FieldConfiguration();
        $emailConfiguration
            ->setName('email')
            ->setLabel('EMAIL');

        $firstNameConfiguration = new FieldConfiguration();
        $firstNameConfiguration
            ->setName('firstName')
            ->setLabel('FIRST_NAME')
            ->isRequired();

        $this->assertEquals(
            [
                $emailConfiguration,
                $firstNameConfiguration,
            ],
            $contactFormConfiguration->getFieldConfigurations()
        );
    }
}
