<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ConfigAdminUiTemplateDirTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testAdminTemplatesAreNotResolved(): void
    {
        $config = Registry::getConfig();

        $this->assertFalse($config->getTemplatePath('login.html.twig', true));
        $this->assertFalse($config->getTemplateDir(true));
    }

    public function testAdminImagesAreReadFromOutDirectory(): void
    {
        $this->assertSame(
            Path::join(
                $this->get(BasicContextInterface::class)->getOutPath(),
                $this->getParameter('oxid_esales.admin_ui.name'),
                'img'
            ) . '/',
            Registry::getConfig()->getImageDir(true)
        );
    }
}
