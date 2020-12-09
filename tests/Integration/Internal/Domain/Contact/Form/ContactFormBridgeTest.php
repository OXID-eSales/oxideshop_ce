<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class ContactFormBridgeTest extends IntegrationTestCase
{
    public function testFormGetter()
    {
        $bridge = $this->get(ContactFormBridgeInterface::class);

        $this->assertInstanceOf(
            FormInterface::class,
            $bridge->getContactForm()
        );
    }

    public function testFormConfigurationGetter()
    {
        $bridge = $this->get(ContactFormBridgeInterface::class);

        $this->assertInstanceOf(
            FormConfigurationInterface::class,
            $bridge->getContactFormConfiguration()
        );
    }

    public function testFormMessageGetter()
    {
        $bridge = $this->get(ContactFormBridgeInterface::class);

        $form = $bridge->getContactForm();
        $form->handleRequest(['email' => 'marina.ginesta@bcn.cat']);

        $message = $bridge->getContactFormMessage($form);

        $this->assertStringContainsString(
            'marina.ginesta@bcn.cat',
            $message
        );
    }
}
