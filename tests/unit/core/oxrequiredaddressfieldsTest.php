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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Testing oxRequiredFieldsValidator class.
 */
class Unit_Core_oxRequiredAddressFieldsTest extends OxidTestCase
{

    public function testGetRequiredFieldsWhenNoFieldsAreSetInConfig()
    {
        $aRequiredFields = array(
            'oxuser__oxfname',
            'oxuser__oxlname',
            'oxuser__oxstreetnr',
            'oxuser__oxstreet',
            'oxuser__oxzip',
            'oxuser__oxcity'
        );

        $this->getConfig()->setConfigParam('aMustFillFields', '');

        $oxRequiredAddressFields = new oxRequiredAddressFields();

        $this->assertSame($aRequiredFields, $oxRequiredAddressFields->getRequiredFields());
    }

    public function testGetRequiredFieldsWhenFieldsAreSetInConfig()
    {
        $aRequiredFields = array('oxuser__oxfname');

        $this->getConfig()->setConfigParam('aMustFillFields', $aRequiredFields);

        $oAddressValidator = new oxRequiredAddressFields();

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    public function testGetRequiredFieldsWhenFieldsAreSetBySetter()
    {
        $aRequiredFields = array('oxuser__oxfname');
        $this->getConfig()->setConfigParam('aMustFillFields', array('someField'));

        $oAddressValidator = new oxRequiredAddressFields();
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    public function testGetUserAddressRequiredFields()
    {
        $aAllRequiredFields = array('oxuser__oxfname', 'oxuser__oxlname', 'oxaddress__oxfname', 'oxaddress__oxlname', 'oxsomeother__sname');
        $aUserRequiredFields = array('oxuser__oxfname', 'oxuser__oxlname');

        $oAddressValidator = new oxRequiredAddressFields();
        $oAddressValidator->setRequiredFields($aAllRequiredFields);

        $this->assertSame($aUserRequiredFields, $oAddressValidator->getBillingFields());
    }

    public function testGetDeliveryAddressRequiredFields()
    {
        $aAllRequiredFields = array('oxuser__oxfname', 'oxuser__oxlname', 'oxaddress__oxfname', 'oxaddress__oxlname', 'oxsomeother__sname');
        $aUserRequiredFields = array('oxaddress__oxfname', 'oxaddress__oxlname');

        $oAddressValidator = new oxRequiredAddressFields();
        $oAddressValidator->setRequiredFields($aAllRequiredFields);

        $this->assertSame($aUserRequiredFields, $oAddressValidator->getDeliveryFields());
    }
}