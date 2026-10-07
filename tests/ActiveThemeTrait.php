<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ParameterDao;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

trait ActiveThemeTrait
{
    private function setActiveThemeParameter(string $themeId, int $shopId = 1): void
    {
        $this->createParameterDao()->add('oxid_esales.theme.active', $themeId, $shopId);
    }

    private function unsetActiveThemeParameter(int $shopId = 1): void
    {
        $parameterDao = $this->createParameterDao();

        if ($parameterDao->has('oxid_esales.theme.active', $shopId)) {
            $parameterDao->remove('oxid_esales.theme.active', $shopId);
        }
    }

    private function createParameterDao(): ParameterDao
    {
        return new ParameterDao(new BasicContextStub(), new Filesystem(), new EventDispatcher());
    }
}
