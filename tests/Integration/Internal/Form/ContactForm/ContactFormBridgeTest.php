<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Common\Form\FormInterface;
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

    private function getContainer()
    {
        $class = new \ReflectionClass(ContainerFactory::class);
        $factory = $class->newInstanceWithoutConstructor();

        return $factory->getContainer();
    }
}
