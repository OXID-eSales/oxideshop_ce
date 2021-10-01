<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ShopAdapterTest extends UnitTestCase
{
    use ContainerTrait;

    public function testIsoDateToAdminFormatWithDefaultConfig(): void
    {
        $isoDate = '2021-01-31';

        $adminDate = $this->get(ShopAdapterInterface::class)->isoDateToAdminFormat($isoDate);

        $this->assertSame($isoDate, $adminDate);
    }

    public function testIsoDateToAdminFormat(): void
    {
        $isoDate = '2021-01-31';
        $eurFormattedDate = '31.01.2021';
        $adminFormat = 'EUR';
        Registry::getConfig()->setConfigParam('sLocalDateFormat', $adminFormat);

        $adminDate = $this->get(ShopAdapterInterface::class)->isoDateToAdminFormat($isoDate);

        $this->assertSame($eurFormattedDate, $adminDate);
    }
}
