<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxField;
use \oxRegistry;

class FieldTest extends \OxidTestCase
{
    public function test_construct()
    {
        $oField = new oxField('ssss<');
        $this->assertEquals('ssss<', $oField->rawValue);
        $this->assertEquals('ssss&lt;', $oField->value);
        $oField = new oxField('ssss<', oxField::T_RAW);
        $this->assertEquals('ssss<', $oField->rawValue);
        $this->assertEquals('ssss<', $oField->value);
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
        $oField = new oxField('ssss<');
        $this->assertEquals('ssss<', $oField->rawValue);
        $this->assertEquals('ssss&lt;', $oField->value);
        $oField->setValue('ssss<', oxField::T_RAW);
        $this->assertEquals('ssss<', $oField->rawValue);
        $this->assertEquals('ssss<', $oField->value);
        $this->assertNull($oField->aaa);
    }

    public function testConvertToFormattedDbDate()
    {
        $oField = new oxField();
        $oField->setValue('2008/05/05 10:2:2');
        $oField->convertToFormattedDbDate();

        $sRawValue = '05.05.2008 10:02:02';
        $sValue = '05.05.2008 10:02:02';
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sRawValue = '2008-05-05 10:02:02';
            $sValue = '2008-05-05 10:02:02';
        }

        $this->assertEquals($sRawValue, $oField->rawValue);
        $this->assertEquals($sValue, $oField->value);
    }

    public function testConvertToPseudoHtml()
    {
        $oField = new oxField();
        $oField->setValue("ssss<\n>");
        $oField->convertToPseudoHtml();
        $this->assertEquals("ssss&lt;<br />\n&gt;", $oField->rawValue);
        $this->assertEquals("ssss&lt;<br />\n&gt;", $oField->value);
    }

    public function testSetValue_resetPrev()
    {
        $oField = new oxField();

        $oField->setValue("ssss<\n>");
        $this->assertEquals("ssss&lt;\n&gt;", $oField->value);
        $oField->setValue("ssss<");
        $this->assertEquals("ssss&lt;", $oField->value);
    }

    public function testGetRawValue()
    {
        $oField = new oxField();

        $oField->setValue("ssss<\n>");
        $this->assertEquals("ssss&lt;\n&gt;", $oField->value);
        $this->assertEquals("ssss<\n>", $oField->getRawValue());
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
