<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\Eshop\Application\Model\Payment;
use \oxPayment;
use \oxField;
use \oxDb;
use \oxRegistry;

class testPayment extends oxPayment
{
    public function unsetGroup($id)
    {
        unset($this->_groups[$id]);
    }
}

class PaymentTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown(): void
    {
        $this->cleanUpTable('oxobject2group', 'oxgroupsid');
        parent::tearDown();
    }

    /**
     * Get default dynamic data
     */
    protected function getDynValues()
    {
        $dynvalue['lsbankname'] = 'Bank name';
        $dynvalue['lsblz'] = '12345678';
        $dynvalue['lsktonr'] = '123456';
        $dynvalue['lsktoinhaber'] = 'Hans Mustermann';

        return $dynvalue;
    }

    public function testGetDynValuesIfAlwaysArrayIsReturned()
    {
        $payment = oxNew(Payment::class);
        $payment->load("oxiddebitnote");

        $this->assertTrue(is_array($payment->getDynValues()));

        $payment = oxNew(Payment::class);
        $payment->oxpayments__oxvaldesc = new oxField('some unknown format value');

        $this->assertTrue(is_array($payment->getDynValues()));

        $payment = oxNew(Payment::class);
        $this->assertTrue(is_array($payment->getDynValues()));
    }

    public function testPaymentGetGroupsReturnCorrectAssignData()
    {
        $payment = oxNew(Payment::class);
        $payment->load('oxiddebitnote');

        $array = array('oxidsmallcust',
                        'oxidnewcustomer',
                        'oxidnewsletter',
                        'oxidadmin');

        $this->assertEquals(
            $array,
            $payment->getGroups()->arrayKeys(),
            "Groups are not as expected.",
            0.0,
            10,
            true
        );
    }

    public function testGetterSetterDynValues()
    {
        $payment = oxNew(Payment::class);
        $payment->setDynValues(array('field0' => 'val0'));
        $payment->setDynValue('field1', 'val1');

        $this->assertEquals(array('field0' => 'val0', 'field1' => 'val1'), $payment->getDynValues());
    }

    public function testGetDynValues()
    {
        $payment = oxNew(Payment::class);
        $payment->load("oxiddebitnote");

        $this->assertEquals(oxRegistry::getUtils()->assignValuesFromText($payment->oxpayments__oxvaldesc->value), $payment->getDynValues());
    }

    public function testGettingPaymentValue()
    {
        $payment = oxNew(Payment::class);

        $payment->load('oxidpayadvance');
        $basePrice = 100.0;
        $this->assertEquals(0, $payment->getPaymentValue($basePrice));

        $payment->load('oxidcashondel');
        $this->assertEquals(7.5, $payment->getPaymentValue($basePrice));

        $payment->oxpayments__oxaddsum = new oxField(-105, oxField::T_RAW);
        $this->assertEquals(100, $payment->getPaymentValue($basePrice));
    }

    public function testGettingPaymentValueInSpecialCurrency()
    {
        $this->getConfig()->setActShopCurrency(2);
        $payment = oxNew(Payment::class);

        $payment->load('oxidpayadvance');
        $basePrice = 100.0;
        $this->assertEquals(0, $payment->getPaymentValue($basePrice));

        $payment->load('oxidcashondel');
        $this->assertEquals(10.7445, $payment->getPaymentValue($basePrice));

        $payment->oxpayments__oxaddsum = new oxField(-105, oxField::T_RAW);
        $this->assertEquals(100, $payment->getPaymentValue($basePrice));
    }

    public function testGetPaymentCountries()
    {
        $payment = oxNew(Payment::class);
        $payment->load('oxiddebitnote');
        $this->assertEquals(3, count($payment->getCountries()), "Failed getting countries list");
    }

    public function testPaymentDeleteFromDb()
    {
        $payment = oxNew(Payment::class);

        $db = oxDb::getDb();
        $q = "insert into oxpayments (oxid, oxactive, oxaddsum, oxaddsumtype) values ('oxpaymenttest', 1, '5', 'abs')";
        $db->execute($q);

        $q = "insert into oxobject2payment (oxid, oxpaymentid, oxobjectid, oxtype) values ('oxob2p_testid', 'oxpaymenttest', 'testid', 'oxdelset')";
        $db->execute($q);

        $payment->load('oxpaymenttest');
        $payment->delete('oxpaymenttest');

        $q = "select count(oxid) from oxpayments where oxid = 'oxpaymenttest' ";
        $this->assertEquals(0, $db->getOne($q), "Failed deleting payment items from oxpayments table");

        $q = "select count(oxid) from oxobject2payment where oxid = 'oxob2p_testid' ";
        $this->assertEquals(0, $db->getOne($q), "Failed deleting items from oxobject2payment table");
    }

    public function testPaymentDeleteNotSetObject()
    {
        $payment = oxNew(Payment::class);

        $db = oxDb::getDb();
        $q = "insert into oxpayments (oxid, oxactive, oxaddsum, oxaddsumtype) values ('oxpaymenttest', 1, '5', 'abs')";
        $db->execute($q);

        $q = "insert into oxobject2payment (oxid, oxpaymentid, oxobjectid, oxtype) values ('oxob2p_testid', 'oxpaymenttest', 'testid', 'oxdelset')";
        $db->execute($q);

        $payment->delete();

        $q = "select count(oxid) from oxpayments where oxid = 'oxpaymenttest' ";
        $this->assertEquals(1, $db->getOne($q));

        $q = "select count(oxid) from oxobject2payment where oxid = 'oxob2p_testid' ";
        $this->assertEquals(1, $db->getOne($q));
    }

    public function testIsValidPaymentEmptyPaymentButThereAreCountries()
    {
        $payment = oxNew(Payment::class);
        $payment->oxpayments__oxid = new oxField('oxempty', oxField::T_RAW);
        $payment->oxpayments__oxactive = new oxField(1);

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment(array(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard');
        $this->assertFalse($isValidPayment);
        $this->assertEquals(-3, $payment->getPaymentErrorNumber());
    }

    public function testIsValidPaymentEmptyPaymentAndThereAreNoCountries()
    {
        $payment = oxNew(Payment::class);
        $payment->oxpayments__oxid = new oxField('oxempty', oxField::T_RAW);
        $payment->oxpayments__oxactive = new oxField(1);

        $user = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getActiveCountry'));
        $user->expects($this->once())->method('getActiveCountry')->will($this->returnValue('otherCountry'));
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment(array(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard');
        $this->assertTrue($isValidPayment);
    }

    public function testIsValidPaymentBlOtherCountryOrderIfFalse()
    {
        $this->getConfig()->setConfigParam('blOtherCountryOrder', false);

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $payment = oxNew(Payment::class);
        $payment->oxpayments__oxid = new oxField('oxempty');
        $payment->oxpayments__oxactive = new oxField(1);
        $this->assertFalse($payment->isValidPayment(array(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard'));
        $this->assertEquals(-2, $payment->getPaymentErrorNumber());
    }

    public function testIsValidPaymentOxemptyIsInActive()
    {
        $payment = oxNew(Payment::class);
        $payment->oxpayments__oxid = new oxField('oxempty');
        $payment->oxpayments__oxactive = new oxField(0);

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $this->assertFalse($payment->isValidPayment(array(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard'));
        $this->assertEquals(-2, $payment->getPaymentErrorNumber());
    }

    public function testIsValidPaymentDebitNoteChecking()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $dynvalue = $this->getDynValues();
        $dynvalue['kknumber'] = ''; //wrong number
        $isValidPayment = $payment->isValidPayment($dynvalue, $this->getConfig()->getBaseShopId(), null, 0.0, 'oxidstandard');
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPaymentWithNotValidShippingSetId()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'nosuchvalue');
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPaymentWithoutShippingSetId()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 5.0, null);
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPayment_FromBoni()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $user->oxuser__oxboni = new oxField($payment->oxpayments__oxfromboni->value - 1, oxField::T_RAW);
        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard');
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPayment_FromAmount()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, -1, 'oxidstandard');
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPayment_ToAmount()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 1000001, 'oxidstandard');
        $this->assertFalse($isValidPayment);
    }

    public function testIsValidPaymentInGroup()
    {
        $payment = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\testPayment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');
        $user->addToGroup('_testGroupId');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 0.0, 'oxidstandard');
        $this->assertTrue($isValidPayment);
    }

    public function testIsValidPayment()
    {
        $payment = oxNew(Payment::class);
        $payment->Load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->Load('oxdefaultadmin');
        $user->oxuser__oxboni = new oxField($payment->oxpayments__oxfromboni->value + 1, oxField::T_RAW);

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 5.0, 'oxidstandard');
        $this->assertTrue($isValidPayment);
    }

    public function testIsValidPayment_settingErrorNumber()
    {
        $payment = $this->getProxyClass("oxPayment");
        $payment->load('oxiddebitnote');

        $user = oxNew('oxuser');
        $user->load('oxdefaultadmin');

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 1000001, 'oxidstandard');
        $this->assertEquals(-3, $payment->getNonPublicVar('_iPaymentError'));

        $isValidPayment = $payment->isValidPayment($this->getDynValues(), $this->getConfig()->getBaseShopId(), $user, 1000001, null);
        $this->assertEquals(-2, $payment->getNonPublicVar('_iPaymentError'));

        $isValidPayment = $payment->isValidPayment(null, $this->getConfig()->getBaseShopId(), $user, 1000001, null);
        $this->assertEquals(1, $payment->getNonPublicVar('_iPaymentError'));
    }

    public function testGetPaymentErrorNumber()
    {
        $payment = $this->getProxyClass("oxPayment");
        $payment->setNonPublicVar('_iPaymentError', 2);
        $this->assertEquals(2, $payment->getPaymentErrorNumber());
    }

    public function testSetPaymentVatOnTop()
    {
        $payment = $this->getProxyClass("oxPayment");
        $payment->setPaymentVatOnTop(true);
        $this->assertTrue($payment->getNonPublicVar("_blPaymentVatOnTop"));
    }
}
