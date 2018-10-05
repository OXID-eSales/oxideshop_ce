<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AssignAdvancedLogic;
use \PHPUnit\Framework\TestCase;

class AssignAdvancedLogicTest extends TestCase
{

    /**
     * @var AssignAdvancedLogic
     */
    private $assignAdvancedLogic;

    protected function setUp()
    {
        parent::setUp();
        $this->assignAdvancedLogic = new AssignAdvancedLogic();
    }

    public function testFormatValueString()
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('foo');
        $this->assertEquals('foo', $formattedValue);
    }

    public function testFormatValueArray()
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('array("foo" => "bar")');
        $this->assertEquals(['foo' => 'bar'], $formattedValue);
    }

    public function testFormatValueRange()
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('range(1,3)');
        $this->assertEquals([1, 2, 3], $formattedValue);
    }
}
