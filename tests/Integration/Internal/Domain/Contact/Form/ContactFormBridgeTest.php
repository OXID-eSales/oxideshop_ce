<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class ContactFormBridgeTest extends TestCase
{
    public function testFormGetter(): void
    {
        $this->assertInstanceOf(
            FormInterface::class,
            ContainerFacade::get(ContactFormBridgeInterface::class)
                ->getContactForm()
        );
    }

    public function testFormConfigurationGetter(): void
    {
        $this->assertInstanceOf(
            FormConfigurationInterface::class,
            ContainerFacade::get(ContactFormBridgeInterface::class)
                ->getContactFormConfiguration()
        );
    }

    public function testFormMessageGetter(): void
    {
        $bridge = ContainerFacade::get(ContactFormBridgeInterface::class);

        $form = $bridge->getContactForm();
        $form->handleRequest(['email' => 'marina.ginesta@bcn.cat']);

        $message = $bridge->getContactFormMessage($form);

        $this->assertStringContainsString(
            'marina.ginesta@bcn.cat',
            $message
        );
    }
}
