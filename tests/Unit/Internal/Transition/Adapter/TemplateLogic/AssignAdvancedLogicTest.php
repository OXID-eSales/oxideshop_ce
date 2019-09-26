<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\AssignAdvancedLogic;
use \PHPUnit\Framework\TestCase;

class AssignAdvancedLogicTest extends TestCase
{

    /**
     * @var AssignAdvancedLogic
     */
    private $assignAdvancedLogic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assignAdvancedLogic = new AssignAdvancedLogic();
    }

    public function testFormatValueString(): void
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('foo');
        $this->assertEquals('foo', $formattedValue);
    }

    public function testFormatValueArray(): void
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('array("foo" => "bar")');
        $this->assertEquals(['foo' => 'bar'], $formattedValue);
    }

    public function testFormatValueRange(): void
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue('range(1,3)');
        $this->assertEquals([1, 2, 3], $formattedValue);
    }
}
