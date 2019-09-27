<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyResourcesDataProvider;

class SmartyResourcesDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSmartyResources()
    {
        $datProvider = new SmartyResourcesDataProvider();

        $settings = ['ox' => [
            'ox_get_template',
            'ox_get_timestamp',
            'ox_get_secure',
            'ox_get_trusted'
        ]
        ];

        $this->assertEquals($settings, $datProvider->getResources());
    }
}
