<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormBridgeInterface;

class ContactFormBridgeTest extends \PHPUnit_Framework_TestCase
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
