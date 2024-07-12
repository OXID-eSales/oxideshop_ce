<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxField;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Unit\FieldTestingTrait;

class FieldTest extends \PHPUnit\Framework\TestCase
{
    use FieldTestingTrait;

    public function test_construct()
    {
        $string = 'ssss<';
        $oField = new oxField($string);
        $this->assertEquals($string, $oField->rawValue);
        $this->assertEquals($this->encode($string), $oField->value);
        $oField = new oxField($string, oxField::T_RAW);
        $this->assertEquals($string, $oField->rawValue);
        $this->assertEquals($string, $oField->value);
    }

    public function test_isset()
    {
        $oField = new oxField('test');
        $this->assertTrue($oField->__isset('rawValue'));
        $this->assertTrue($oField->__isset('value'));
        $this->assertFalse($oField->__isset('unknown'));
        $this->assertTrue(isset($oField->rawValue));
        $this->assertTrue(isset($oField->value));
        $this->assertFalse(isset($oField->unknown));
    }

    public function test__getValue_setValue()
    {
        $string = 'ssss<';
        $oField = new oxField($string);
        $this->assertEquals($string, $oField->rawValue);
        $this->assertEquals($this->encode($string), $oField->value);
        $oField->setValue($string, oxField::T_RAW);
        $this->assertEquals($string, $oField->rawValue);
        $this->assertEquals($string, $oField->value);
        $this->assertNull($oField->aaa);
    }

    public function testSetValue_resetPrev()
    {
        $oField = new oxField();

        $string = "ssss<\n>";
        $oField->setValue($string);
        $this->assertEquals($this->encode($string), $oField->value);
        $string = 'ssss<';
        $oField->setValue($string);
        $this->assertEquals($this->encode($string), $oField->value);
    }

    public function testGetRawValue()
    {
        $oField = new oxField();

        $string = "ssss<\n>";
        $oField->setValue($string);
        $this->assertEquals($this->encode($string), $oField->value);
        $this->assertEquals($string, $oField->getRawValue());
    }

    public function testGetRawValueIfSetAsRaw()
    {
        $oField = new oxField();

        $oField->setValue("ssss<\n>", oxField::T_RAW);
        $this->assertEquals("ssss<\n>", $oField->value);
        $this->assertEquals("ssss<\n>", $oField->getRawValue());
    }

    public function testToString()
    {
        $oField = new oxField(451);
        $this->assertSame("451", (string) $oField);
    }
}
