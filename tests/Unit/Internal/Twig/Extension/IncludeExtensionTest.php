<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\IncludeDynamicLogic;
use OxidEsales\EshopCommunity\Internal\Twig\Extensions\IncludeExtension;
use PHPUnit\Framework\TestCase;

/**
 * Class IncludeExtensionTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IncludeExtensionTest extends TestCase
{

    /** @var IncludeExtension */
    private $includeExtension;

    public function setUp()
    {
        $this->includeExtension = new IncludeExtension(new IncludeDynamicLogic());
    }

    /**
     * @param array $parameters
     * @param array $expected
     *
     * @covers       IncludeExtension::includeDynamicPrefix
     * @dataProvider getIncludeDynamicPrefixTests
     */
    public function testIncludeDynamicPrefix(array $parameters, array $expected)
    {
        $this->assertEquals($this->includeExtension->includeDynamicPrefix($parameters), $expected);
    }

    /**
     * @param array  $parameters
     * @param string $expected
     *
     * @covers       IncludeExtension::renderForCache
     * @dataProvider getRenderForCacheTests
     */
    public function testRenderForCache(array $parameters, $expected)
    {
        $this->assertEquals($this->includeExtension->renderForCache($parameters), $expected);
    }

    /**
     * @return array
     */
    public function getIncludeDynamicPrefixTests()
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
    public function getRenderForCacheTests()
    {
        return [
            [[], '<oxid_dynamic></oxid_dynamic>'],
            [['param1' => 'val1', 'param2' => 2], '<oxid_dynamic> param1=\'dmFsMQ==\' param2=\'Mg==\'</oxid_dynamic>'],
        ];
    }
}
