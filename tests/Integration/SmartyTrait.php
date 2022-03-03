<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Psr\Container\ContainerInterface;

trait SmartyTrait
{
    private string $templateFileExtension;

    public function getSmartyFileExtension(): string
    {
        return $this->getContainer()->getParameter('oxid_esales.templating.engine_template_extension');
    }

    public function skipIfNotSmarty(): void
    {
        if ($this->getSmartyFileExtension() !== 'tpl') {
            $this->markTestSkipped('This test work only with Smarty templating engine!');
        }
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
