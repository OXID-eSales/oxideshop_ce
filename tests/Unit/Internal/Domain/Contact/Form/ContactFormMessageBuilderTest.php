<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormMessageBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Form\Form;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormField;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ContactFormMessageBuilderTest extends TestCase
{
    #[DataProvider('fieldsProvider')]
    public function testContentGetter(string $name, string $value): void
    {
        $form = $this->getContactForm();
        $form->handleRequest([$name => $value]);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('translateString')
            ->willReturnCallback(function ($arg) {
                return $arg;
            });
        $contactFormMessageBuilder = new ContactFormMessageBuilder($shopAdapter);

        $this->assertStringContainsString(
            $value,
            $contactFormMessageBuilder->getContent($form)
        );
    }

    public static function fieldsProvider(): array
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

    private function getContactForm(): Form
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
