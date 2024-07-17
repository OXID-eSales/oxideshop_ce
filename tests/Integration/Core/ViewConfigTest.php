<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\PictureHandler;
use OxidEsales\EshopCommunity\Core\ViewConfig;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ViewConfigTest extends IntegrationTestCase
{
    use ContainerTrait;

    private ViewConfig $viewConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->viewConfig = new ViewConfig();
    }

    public function testIsAltImageServerConfiguredWithEmptyParameter(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_alternative_image_url', '');
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $altImageServerConfigured = $this->viewConfig->isAltImageServerConfigured();

        $this->assertFalse($altImageServerConfigured);
    }

    public function testIsAltImageServerConfiguredWithNotEmptyParameter(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_alternative_image_url', 'someValue');
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $altImageServerConfigured = $this->viewConfig->isAltImageServerConfigured();

        $this->assertTrue($altImageServerConfigured);
    }
}
