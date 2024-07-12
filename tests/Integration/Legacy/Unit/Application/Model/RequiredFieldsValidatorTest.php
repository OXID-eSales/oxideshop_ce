<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;

/**
 * Testing oxRequiredFieldsValidator class.
 */
class RequiredFieldsValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testSetGetRequiredFields()
    {
        $aRequiredFields = ['field1', 'field2'];

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    /**
     * Returns address fields and invalid fields array when required fields are field1 and field2.
     *
     * @return array
     */
    public function providerValidateFields(): \Iterator
    {
        yield [['field1' => 'value1', 'field2' => 'value2'], [], true];
        yield [['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'], [], true];
        yield [['field1' => 'value1', 'field2' => 'value2', 'field3' => ''], [], true];
        yield [['field1' => 'value1', 'field2' => ''], ['field2'], false];
        yield [['field1' => '', 'field2' => 'value2'], ['field1'], false];
        yield [['field1' => '', 'field2' => ''], ['field1', 'field2'], false];
        yield [['field1' => '', 'field2' => '', 'field3' => 'value3'], ['field1', 'field2'], false];
        yield [['field1' => 'value1'], ['field2'], false];
        yield [['field2' => 'value2'], ['field1'], false];
        yield [['field2' => 'value2', 'field3' => 'value3'], ['field1'], false];
        yield [[], ['field1', 'field2'], false];
    }

    /**
     * @param $aFields
     * @param $aInvalidFields
     *
     * @dataProvider providerValidateFields
     */
    public function testValidateFieldsWhenRequiredFieldsExists($aFields, $aInvalidFields, $blResult)
    {
        $aRequiredFields = ['field1', 'field2'];

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($blResult, $oAddressValidator->validateFields($this->createObject($aFields)));
    }

    /**
     * @param $aFields
     *
     * @dataProvider providerValidateFields
     */
    public function testValidateFieldsWithNoRequiredFields($aFields)
    {
        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields([]);

        $this->assertTrue($oAddressValidator->validateFields($this->createObject($aFields)));
    }

    /**
     * @param array $aFields
     * @param array $aInvalidFields
     *
     * @dataProvider providerValidateFields
     */
    public function testGetInvalidFields($aFields, $aInvalidFields)
    {
        $aRequiredFields = ['field1', 'field2'];

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);
        $oAddressValidator->validateFields($this->createObject($aFields));

        $this->assertEquals($aInvalidFields, $oAddressValidator->getInvalidFields());
    }

    /**
     * @param $aData
     *
     * @return oxBase
     */
    private function createObject($aData)
    {
        $oObject = oxNew('oxBase');
        foreach ($aData as $sKey => $sValue) {
            $sKey = "__" . $sKey;
            $oObject->$sKey = new oxField($sValue);
        }

        return $oObject;
    }
}
