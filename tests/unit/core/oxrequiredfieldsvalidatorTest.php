<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Testing oxRequiredFieldsValidator class.
 */
class Unit_Core_oxRequiredFieldsValidatorTest extends OxidTestCase
{

    public function testSetGetRequiredFields()
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = new oxRequiredFieldsValidator();
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    /**
     * Returns address fields and invalid fields array when required fields are field1 and field2.
     *
     * @return array
     */
    public function providerValidateAddress()
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
     * @param $aAddressFields
     * @param $aInvalidFields
     * @dataProvider providerValidateAddress
     */
    public function testValidateAddressWhenRequiredFieldsExists($aAddressFields, $aInvalidFields, $blResult)
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = new oxRequiredFieldsValidator();
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($blResult, $oAddressValidator->validateFields($aAddressFields));
    }

    /**
     * @param $aAddressFields
     * @dataProvider providerValidateAddress
     */
    public function testValidateAddressWithNoRequiredFields($aAddressFields)
    {
        $oAddressValidator = new oxRequiredFieldsValidator();
        $oAddressValidator->setRequiredFields(array());

        $this->assertTrue($oAddressValidator->validateFields($aAddressFields));
    }

    /**
     * @param array $aAddressFields
     * @param array $aInvalidFields
     * @dataProvider providerValidateAddress
     */
    public function testGetInvalidFields($aAddressFields, $aInvalidFields)
    {
        $aRequiredFields = array('field1', 'field2');

        $oAddressValidator = new oxRequiredFieldsValidator();
        $oAddressValidator->setRequiredFields($aRequiredFields);
        $oAddressValidator->validateFields($aAddressFields);

        $this->assertEquals($aInvalidFields, $oAddressValidator->getInvalidFields());
    }
}