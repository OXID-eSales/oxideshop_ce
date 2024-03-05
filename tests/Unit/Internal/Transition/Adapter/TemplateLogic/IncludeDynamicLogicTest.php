<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeDynamicLogic;
use PHPUnit\Framework\TestCase;

#[CoversClass(IncludeDynamicLogic::class)]
final class IncludeDynamicLogicTest extends TestCase
{
    private IncludeDynamicLogic $includeDynamicLogic;

    public function setup(): void
    {
        $this->includeDynamicLogic = new IncludeDynamicLogic();
    }


    #[DataProvider('getIncludeDynamicPrefixTests')]
    public function testIncludeDynamicPrefix(array $parameters, array $expected): void
    {
        $this->assertEquals($this->includeDynamicLogic->includeDynamicPrefix($parameters), $expected);
    }


    #[DataProvider('getRenderForCacheTests')]
    public function testRenderForCache(array $parameters, string $expected): void
    {
        $this->assertEquals($this->includeDynamicLogic->renderForCache($parameters), $expected);
    }

    public static function getIncludeDynamicPrefixTests(): array
    {
        return [
            [[], []],
            [['param1' => 'val1', 'param2' => 2], ['_param1' => 'val1', '_param2' => 2]],
            [['type' => 'custom'], []],
            [['type' => 'custom', 'param1' => 'val1', 'param2' => 2], ['_custom_param1' => 'val1', '_custom_param2' => 2]],
            [['type' => 'custom', 'file' => 'file.tpl'], []],
            [['type' => 'custom', 'file' => 'file.tpl', 'param' => 'val'], ['_custom_param' => 'val']]
        ];
    }

    public static function getRenderForCacheTests(): array
    {
        return [
            [[], '<oxid_dynamic></oxid_dynamic>'],
            [['param1' => 'val1', 'param2' => 2], '<oxid_dynamic> param1=\'dmFsMQ==\' param2=\'Mg==\'</oxid_dynamic>'],
        ];
    }
}
