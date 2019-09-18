<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\Form;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormMessageBuilder;

class ContactFormMessageBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider fieldsProvider
     */
    public function testContentGetter($name, $value)
    {
        $form = $this->getContactForm();
        $form->handleRequest([$name => $value]);

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
            $value,
            $contactFormMessageBuilder->getContent($form)
        );
    }

    public function fieldsProvider()
    {
        return [
            [
                'email',
                'marina.ginesta@bcn.cat'
            ],
            [
                'firstName',
                'Marina'
            ],
            [
                'lastName',
                'Ginestà'
            ],
            [
                'salutation',
                'MRS'
            ],
            [
                'message',
                'I\'m standing on the rooftop'
            ],
        ];
    }

    private function getContactForm()
    {
        $form = new Form();

        $fieldNames = [
            'email',
            'firstName',
            'lastName',
            'salutation',
            'message',
        ];

        foreach ($fieldNames as $fieldName) {
            $field = new FormField();
            $field->setName($fieldName);
            $form->add($field);
        }

        return $form;
    }
}
