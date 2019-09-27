<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormFieldsConfigurationDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormConfigurationFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ContactFormConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testConfigurationGetter()
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();

        $formFieldsConfigurationDataProvider = $this->getMockBuilder(
            FormFieldsConfigurationDataProviderInterface::class
        )->getMock();
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
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getRequiredContactFormFields')
            ->willReturn([
                'name',
            ]);

        $formFieldsConfigurationDataProvider = $this->getMockBuilder(
            FormFieldsConfigurationDataProviderInterface::class
        )->getMock();
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
