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
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormMessageBuilder;

class ContactFormMessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider contactFormRequestProvider
     */
    public function testContentGetter($contactFormRequest)
    {
        $form = $this->getContactFormFactory()->getForm();
        $form->handleRequest($contactFormRequest);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('translateString')
            ->will(
                $this->returnCallback(function ($arg) {
                    return $arg;
                })
            );
        $contactFormMessageBuilder = new ContactFormMessageBuilder($shopAdapter);

        $this->assertContains(
            current($contactFormRequest),
            $contactFormMessageBuilder->getContent($form)
        );
    }

    public function contactFormRequestProvider()
    {
        return [
            [['email'       => 'marina.ginesta@bcn.cat']],
            [['firstName'   => 'Marina']],
            [['lastName'    => 'Ginestà']],
            [['salutation'  => 'MRS']],
            [['message'     => 'I\'m standing on the rooftop']],
        ];
    }

    private function getContactFormFactory()
    {
        $requiredFieldsProvider = $this->getMockBuilder(RequiredFieldsProviderInterface::class)->getMock();
        $requiredFieldsProvider
            ->method('getRequiredFields')
            ->willReturn([]);

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
