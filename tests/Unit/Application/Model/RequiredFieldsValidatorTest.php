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
class RequiredFieldsValidatorTest extends \OxidTestCase
{
    public function testSetGetRequiredFields()
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    /**
     * Returns address fields and invalid fields array when required fields are field1 and field2.
     *
     * @return array
     */
    public function providerValidateFields()
    {
        return array(
            array(array('field1' => 'value1', 'field2' => 'value2'), array(), true),
            array(array('field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'), array(), true),
            array(array('field1' => 'value1', 'field2' => 'value2', 'field3' => ''), array(), true),
            array(array('field1' => 'value1', 'field2' => ''), array('field2'), false),
            array(array('field1' => '', 'field2' => 'value2'), array('field1'), false),
            array(array('field1' => '', 'field2' => ''), array('field1', 'field2'), false),
            array(array('field1' => '', 'field2' => '', 'field3' => 'value3'), array('field1', 'field2'), false),
            array(array('field1' => 'value1'), array('field2'), false),
            array(array('field2' => 'value2'), array('field1'), false),
            array(array('field2' => 'value2', 'field3' => 'value3'), array('field1'), false),
            array(array(), array('field1', 'field2'), false),
        );
    }

    /**
     * @param $aFields
     * @param $aInvalidFields
     *
     * @dataProvider providerValidateFields
     */
    public function testValidateFieldsWhenRequiredFieldsExists($aFields, $aInvalidFields, $blResult)
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($blResult, $oAddressValidator->validateFields($this->_createObject($aFields)));
    }

    /**
     * @param $aFields
     *
     * @dataProvider providerValidateFields
     */
    public function testValidateFieldsWithNoRequiredFields($aFields)
    {
        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields(array());

        $this->assertTrue($oAddressValidator->validateFields($this->_createObject($aFields)));
    }

    /**
     * @param array $aFields
     * @param array $aInvalidFields
     *
     * @dataProvider providerValidateFields
     */
    public function testGetInvalidFields($aFields, $aInvalidFields)
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = oxNew('oxRequiredFieldsValidator');
        $oAddressValidator->setRequiredFields($aRequiredFields);
        $oAddressValidator->validateFields($this->_createObject($aFields));

        $this->assertEquals($aInvalidFields, $oAddressValidator->getInvalidFields());
    }

    /**
     * @param $aData
     *
     * @return oxBase
     */
    private function _createObject($aData)
    {
        $oObject = oxNew('oxBase');
        foreach ($aData as $sKey => $sValue) {
            $sKey = "__" . $sKey;
            $oObject->$sKey = new oxField($sValue);
        }

        return $oObject;
    }
}
