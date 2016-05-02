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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

/**
 * Testing oxRequiredFieldValidator class.
 */
class RequiredFieldValidatorTest extends \OxidTestCase
{

    public function providerValidateFieldValue()
    {
        return array(
            array('value1', true),
            array(' value1 ', true),
            array(null, false),
            array('', false),
            array(' ', false),
            array('    ', false),
            array(array(), true),
            array(array('value1'), true),
            array(array('value1', 'value2'), true),
            array(array(null), false),
            array(array(''), false),
            array(array('', 'value2'), false),
            array(array('value1', ''), false),
        );
    }

    /**
     * @param string $sString
     * @param bool   $blResult
     *
     * @dataProvider providerValidateFieldValue
     */
    public function testValidateFieldValue($sString, $blResult)
    {
        $oAddressValidator = oxNew('oxRequiredFieldValidator');
        $this->assertSame($blResult, $oAddressValidator->validateFieldValue($sString));
    }
}