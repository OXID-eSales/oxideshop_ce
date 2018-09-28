<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AssignAdvancedLogic;
use OxidEsales\EshopCommunity\Internal\Twig\Extensions\AssignAdvancedExtension;
use \PHPUnit\Framework\TestCase;

class AssignAdvancedExtensionTest extends TestCase
{

    /**
     * @var AssignAdvancedExtension
     */
    private $assignAdvancedExtension;

    protected function setUp()
    {
        parent::setUp();
        $assignAdvancedLogic = new AssignAdvancedLogic();
        $this->assignAdvancedExtension = new AssignAdvancedExtension($assignAdvancedLogic);
    }

    public function testAssignAdvanced()
    {
        $a = $this->assignAdvancedExtension->assignAdvanced('foo');
        $this->assertEquals('foo', $a);
    }
}
