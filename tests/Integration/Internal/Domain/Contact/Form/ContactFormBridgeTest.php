<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Contact\Form;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormBridgeInterface;

class ContactFormBridgeTest extends \PHPUnit\Framework\TestCase
{
    public function testFormGetter()
    {
        $container = $this->getContainer();
        $bridge = $container->get(ContactFormBridgeInterface::class);

        $this->assertInstanceOf(
            FormInterface::class,
            $bridge->getContactForm()
        );
    }

    public function testFormConfigurationGetter()
    {
        $container = $this->getContainer();
        $bridge = $container->get(ContactFormBridgeInterface::class);

        $this->assertInstanceOf(
            FormConfigurationInterface::class,
            $bridge->getContactFormConfiguration()
        );
    }

    public function testFormMessageGetter()
    {
        $container = $this->getContainer();
        $bridge = $container->get(ContactFormBridgeInterface::class);

        $form = $bridge->getContactForm();
        $form->handleRequest(['email' => 'marina.ginesta@bcn.cat']);

        $message = $bridge->getContactFormMessage($form);

        $this->assertContains(
            'marina.ginesta@bcn.cat',
            $message
        );
    }

    private function getContainer()
    {
        $factory = ContainerFactory::getInstance();

        return $factory->getContainer();
    }
}
