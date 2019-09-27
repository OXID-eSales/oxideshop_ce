<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyPrefiltersDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartyPrefiltersDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSmartyPrefilters()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $dataProvider = new SmartyPrefiltersDataProvider($smartyContextMock);

        $settings = [
            'smarty_prefilter_oxblock' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxtpldebug.php',
        ];

        $this->assertEquals($settings, $dataProvider->getPrefilterPlugins());
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(true);

        $smartyContextMock
            ->method('getSourcePath')
            ->willReturn('testShopPath');

        return $smartyContextMock;
    }
}
