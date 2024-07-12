<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

/**
 * Testing oxRequiredFieldsValidator class.
 */
class RequiredAddressFieldsTest extends \OxidTestCase
{
    public function testGetRequiredFieldsWhenNoFieldsAreSetInConfig()
    {
        $aRequiredFields = ['oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxstreetnr', 'oxuser__oxstreet', 'oxuser__oxzip', 'oxuser__oxcity'];

        $this->getConfig()->setConfigParam('aMustFillFields', '');

        $oxRequiredAddressFields = oxNew('oxRequiredAddressFields');

        $this->assertSame($aRequiredFields, $oxRequiredAddressFields->getRequiredFields());
    }

    public function testGetRequiredFieldsWhenFieldsAreSetInConfig()
    {
        $aRequiredFields = ['oxuser__oxfname'];

        $this->getConfig()->setConfigParam('aMustFillFields', $aRequiredFields);

        $oAddressValidator = oxNew('oxRequiredAddressFields');

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    public function testGetRequiredFieldsWhenFieldsAreSetBySetter()
    {
        $aRequiredFields = ['oxuser__oxfname'];
        $this->getConfig()->setConfigParam('aMustFillFields', ['someField']);

        $oAddressValidator = oxNew('oxRequiredAddressFields');
        $oAddressValidator->setRequiredFields($aRequiredFields);

        $this->assertSame($aRequiredFields, $oAddressValidator->getRequiredFields());
    }

    public function testGetUserAddressRequiredFields()
    {
        $aAllRequiredFields = ['oxuser__oxfname', 'oxuser__oxlname', 'oxaddress__oxfname', 'oxaddress__oxlname', 'oxsomeother__sname'];
        $aUserRequiredFields = ['oxuser__oxfname', 'oxuser__oxlname'];

        $oAddressValidator = oxNew('oxRequiredAddressFields');
        $oAddressValidator->setRequiredFields($aAllRequiredFields);

        $this->assertSame($aUserRequiredFields, $oAddressValidator->getBillingFields());
    }

    public function testGetDeliveryAddressRequiredFields()
    {
        $aAllRequiredFields = ['oxuser__oxfname', 'oxuser__oxlname', 'oxaddress__oxfname', 'oxaddress__oxlname', 'oxsomeother__sname'];
        $aUserRequiredFields = ['oxaddress__oxfname', 'oxaddress__oxlname'];

        $oAddressValidator = oxNew('oxRequiredAddressFields');
        $oAddressValidator->setRequiredFields($aAllRequiredFields);

        $this->assertSame($aUserRequiredFields, $oAddressValidator->getDeliveryFields());
    }
}
