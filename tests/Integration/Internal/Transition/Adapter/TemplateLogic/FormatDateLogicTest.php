<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatDateLogic;
use PHPUnit\Framework\TestCase;

final class FormatDateLogicTest extends TestCase
{
    private FormatDateLogic $formDateLogic;

    public function setUp(): void
    {
        $this->formDateLogic = new FormatDateLogic();
        parent::setUp();
    }

    public function testFormdateWithEmptyValue(): void
    {
        $input = '';
        $expected = "0000-00-00 00:00:00";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'datetime', true));
    }

    public function testFormdateWithNullValue(): void
    {
        $input = null;
        $expected = "0000-00-00 00:00:00";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'datetime', true));
    }

    public function testFormdateWithDatetime(): void
    {
        $input = '01.08.2007 11.56.25';
        $expected = "2007-08-01 11:56:25";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'datetime', true));
    }

    public function testFormdateWithTimestamp(): void
    {
        $input = '20070801115625';
        $expected = "2007-08-01 11:56:25";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'timestamp', true));
    }

    public function testFormdateWithDate(): void
    {
        $input = '2007-08-01 11:56:25';
        $expected = "2007-08-01";

        $this->assertEquals($expected, $this->formDateLogic->formdate($input, 'date', true));
    }

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
