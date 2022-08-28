<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartySecuritySettingsDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use PHPUnit\Framework\TestCase;

class SmartySecuritySettingsDataProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        new \Smarty(); // Initalize constants
    }

    public function testGetSecuritySettings()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $dataProvider = new SmartySecuritySettingsDataProvider($smartyContextMock);
        $settings = [
            'php_handling' => 2,
            'security' => true,
            'secure_dir' => ['testTemplateDir'],
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
            ]
        ];

        $this->assertEquals($settings, $dataProvider->getSecuritySettings());
    }

    private function getSmartyContextMock(): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        return $smartyContextMock;
    }
}
