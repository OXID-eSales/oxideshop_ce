<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\PictureHandler;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class PictureHandlerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private PictureHandler $pictureHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->pictureHandler = new PictureHandler();
    }

    public function testGetAltImageUrlWithEmptyParameter(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_alternative_image_url', '');
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $altImageUrl = $this->pictureHandler->getAltImageUrl('random/file/path', 'file.txt');
        $this->assertEmpty($altImageUrl);
    }

    public function testGetAltImageUrlWithNullFile(): void
    {
        $altImageUrlParameter = 'somevalue';
        $this->createContainer();
        $this->container->setParameter('oxid_alternative_image_url', $altImageUrlParameter);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $altImageUrl = $this->pictureHandler->getAltImageUrl('random/file/path', null);
        $this->assertEquals($altImageUrlParameter, $altImageUrl);
    }
}
