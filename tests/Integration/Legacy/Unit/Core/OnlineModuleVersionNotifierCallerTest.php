<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Curl;
use OxidEsales\Eshop\Core\OnlineModulesNotifierRequest;
use OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\Eshop\Core\SimpleXml;
use PHPUnit\Framework\TestCase;

class OnlineModuleVersionNotifierCallerTest extends TestCase
{
    public function testGetWebServiceUrl(): void
    {
        $curl = $this->createPartialMock(Curl::class, ['execute']);
        $notifier = new OnlineModuleVersionNotifierCaller(
            $curl,
            oxNew(OnlineServerEmailBuilder::class),
            oxNew(SimpleXml::class)
        );

        $notifier->call(oxNew(OnlineModulesNotifierRequest::class));

        $this->assertSame('https://omvn.oxid-esales.com/check.php', $curl->getUrl());
    }
}
