<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeDynamicLogic;
use PHPUnit\Framework\TestCase;

/**
 * Class IncludeDynamicLogicTest
 */
class IncludeDynamicLogicTest extends TestCase
{

    /** @var IncludeDynamicLogic */
    private $includeDynamicLogic;

    public function setUp(): void
    {
        $this->includeDynamicLogic = new IncludeDynamicLogic();
    }

    /**
     * @param array $parameters
     * @param array $expected
     *
     * @covers       IncludeExtension::includeDynamicPrefix
     * @dataProvider getIncludeDynamicPrefixTests
     */
    public function testIncludeDynamicPrefix(array $parameters, array $expected): void
    {
        $this->assertEquals($this->includeDynamicLogic->includeDynamicPrefix($parameters), $expected);
    }

    /**
     * @param array  $parameters
     * @param string $expected
     *
     * @covers       IncludeExtension::renderForCache
     * @dataProvider getRenderForCacheTests
     */
    public function testRenderForCache(array $parameters, string $expected): void
    {
        $this->assertEquals($this->includeDynamicLogic->renderForCache($parameters), $expected);
    }

    /**
     * @return array
     */
    public function getIncludeDynamicPrefixTests(): array
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

    /**
     * @return array
     */
    public function getRenderForCacheTests(): array
    {
        return [
            [[], '<oxid_dynamic></oxid_dynamic>'],
            [['param1' => 'val1', 'param2' => 2], '<oxid_dynamic> param1=\'dmFsMQ==\' param2=\'Mg==\'</oxid_dynamic>'],
        ];
    }
}
