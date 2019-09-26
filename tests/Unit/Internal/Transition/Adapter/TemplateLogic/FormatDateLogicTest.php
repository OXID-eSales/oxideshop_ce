<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatDateLogic;
use PHPUnit\Framework\TestCase;

/**
 * Class FormatDateLogicTest
 */
class FormatDateLogicTest extends TestCase
{

    /** @var FormatDateLogic */
    private $formDateLogic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formDateLogic = new FormatDateLogic();
    }

    /**
     * @covers FormatDateLogic::formdate
     */
    public function testFormdateWithDatetime(): void
    {
        $input = '01.08.2007 11.56.25';
        $expected = "2007-08-01 11:56:25";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'datetime', true));
    }

    /**
     * @covers FormatDateLogic::formdate
     */
    public function testFormdateWithTimestamp(): void
    {
        $input = '20070801115625';
        $expected = "2007-08-01 11:56:25";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'timestamp', true));
    }

    /**
     * @covers FormatDateLogic::formdate
     */
    public function testFormdateWithDate(): void
    {
        $input = '2007-08-01 11:56:25';
        $expected = "2007-08-01";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'date', true));
    }

    /**
     * @covers FormatDateLogic::formdate
     */
    public function testFormdateUsingObject(): void
    {
        $expected = "2007-08-01 11:56:25";

        $field = new Field();
        $field->fldmax_length = "0";
        $field->fldtype = 'datetime';
        $field->setValue('01.08.2007 11.56.25');

        $this->assertEquals($expected, $this->formDateLogic->formdate($field, 'datetime'));
    }
}
