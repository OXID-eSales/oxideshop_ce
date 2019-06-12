<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use oxRegistry;

class PaymentlistTest extends \OxidTestCase
{
    protected $_aPayList = array();
    protected $_oDefPaymentList = null;

    /**
     * Returns Unique Id with underscore as prefix
     *
     * @return string;
     */
    protected function _getUId()
    {
        $sUId = oxRegistry::getUtilsObject()->generateUId();
        $sUId[0] = '_';

        return $sUId;
    }

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setup()
    {
        parent::setUp();

        if ($this->getName() == "testGetPaymentListWithSomeWrongData") {
            return;
        }

        $this->oUser = oxNew('oxUser');
        $this->oUser->load('oxdefaultadmin');

        $this->oUser->addToGroup('oxidadmin');

        // disabling default payments
        $this->_oDefPaymentList = oxNew('oxPaymentList');
        $this->_oDefPaymentList->selectString('select * from oxpayments where oxactive = 1');
        foreach ($this->_oDefPaymentList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(0, oxField::T_RAW);
            $oPayment->save();
        }

        // creating few payments
        $this->_aPayList[0] = oxNew('oxPayment');
        $this->_aPayList[0]->oxpayments__oxdesc = new oxField('Payment for user', oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxaddsum = new oxField(1, oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxfromamount = new oxField(0, oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxtoamount = new oxField(999, oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxsort = new oxField(10, oxField::T_RAW);
        $this->_aPayList[0]->save();

        $this->_aPayList[1] = oxNew('oxPayment');
        $this->_aPayList[1]->oxpayments__oxdesc = new oxField('Payment for group', oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxaddsum = new oxField(2, oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxfromamount = new oxField(0, oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxtoamount = new oxField(999, oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxsort = new oxField(20, oxField::T_RAW);
        $this->_aPayList[1]->save();

        $this->_aPayList[2] = oxNew('oxPayment');
        $this->_aPayList[2]->oxpayments__oxdesc = new oxField('Payment for country', oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxaddsum = new oxField(3, oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxfromamount = new oxField(0, oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxtoamount = new oxField(999, oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxsort = new oxField(30, oxField::T_RAW);
        $this->_aPayList[2]->save();

        $this->_aPayList[3] = oxNew('oxPayment');
        $this->_aPayList[3]->oxpayments__oxdesc = new oxField('Plain Payment', oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxaddsum = new oxField(3, oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxfromamount = new oxField(0, oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxtoamount = new oxField(999, oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxsort = new oxField(40, oxField::T_RAW);
        $this->_aPayList[3]->save();

        // assigning payments
        // for groups
        $oO2Group = oxNew('oxObject2Group');
        $oO2Group->setId($this->_getUId());
        $oO2Group->oxobject2group__oxobjectid = new oxField($this->_aPayList[0]->getId(), oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oO2Group->save();

        $oO2Group = oxNew('oxObject2Group');
        $oO2Group->setId($this->_getUId());
        $oO2Group->oxobject2group__oxobjectid = new oxField($this->_aPayList[1]->getId(), oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oO2Group->save();

        // for country
        $oO2Pay = oxNew('oxBase');
        $oO2Pay->Init('oxobject2payment');
        $oO2Group->setId($this->_getUId());
        $oO2Pay->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[2]->getId(), oxField::T_RAW);
        $oO2Pay->oxobject2payment__oxobjectid = new oxField($this->oUser->oxuser__oxcountryid->value, oxField::T_RAW);
        $oO2Pay->oxobject2payment__oxtype = new oxField('oxcountry', oxField::T_RAW);
        $oO2Pay->save();

        $oO2Group = oxNew('oxObject2Group');
        $oO2Group->setId($this->_getUId());
        $oO2Group->oxobject2group__oxobjectid = new oxField($this->_aPayList[2]->getId(), oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oO2Group->save();

        // delivery set
        $this->oDelSet = oxNew('oxDeliverySet');
        $this->oDelSet->setId($this->_getUId());
        $this->oDelSet->oxdeliveryset__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->oDelSet->oxdeliveryset__oxactive = new oxField(1, oxField::T_RAW);
        $this->oDelSet->oxdeliveryset__oxtitle = new oxField("Test delivery set", oxField::T_RAW);
        $this->oDelSet->save();

        $oO2Group = oxNew('oxObject2Group');
        $oO2Group->setId($this->_getUId());
        $oO2Group->oxobject2group__oxobjectid = new oxField($this->_aPayList[3]->getId(), oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidadmin', oxField::T_RAW);
        $oO2Group->save();

        // assigning payments
        // user
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oObject->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[0]->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxobjectid = new oxField($this->oDelSet->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oObject->save();

        // group
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oO2Group->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[1]->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxobjectid = new oxField($this->oDelSet->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oObject->save();

        // country
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oO2Group->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[2]->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxobjectid = new oxField($this->oDelSet->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oObject->save();

        // default
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oO2Group->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[3]->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxobjectid = new oxField($this->oDelSet->getId(), oxField::T_RAW);
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset", oxField::T_RAW);
        $oObject->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        if ($this->getName() != "testGetPaymentListWithSomeWrongData") {
            // enabling default payments
            foreach ($this->_oDefPaymentList as $oPayment) {
                $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
                $oPayment->save();
            }

            // deleting demo payments
            foreach ($this->_aPayList as $oPayment) {
                $oPayment->delete();
            }
        }
        $this->cleanUpTable('oxobject2group', 'oxgroupsid');
        $this->cleanUpTable('oxobject2payment');
        $this->cleanUpTable('oxobject2group');
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxdel2delset');
        $this->cleanUpTable('oxobject2delivery');

        parent::tearDown();
    }

    /**
     * Testing if payment list will be build even some data is wrong
     */
    public function testGetPaymentListWithSomeWrongData()
    {
        $sShipSetId = "oxidstandard";

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oPaymentList = oxNew('oxPaymentList');
        $oPaymentList->getPaymentList($sShipSetId, 10, $oUser);
        $iListCount = $oPaymentList->count();

        // list must contain at least one item
        $this->assertTrue($iListCount > 0);

        $oPayment = $oPaymentList->current();

        // adding garbage
        $oGarbage = oxNew('oxBase');
        $oGarbage->init("oxobject2payment");
        $oGarbage->setId("_testoxobject2payment1");
        $oGarbage->oxobject2payment__oxpaymentid = new oxField($oPayment->getId());
        $oGarbage->oxobject2payment__oxobjectid = new oxField("yyy");
        $oGarbage->oxobject2payment__oxtype = new oxField("oxcountry");
        $oGarbage->save();

        $oGarbage = oxNew('oxBase');
        $oGarbage->init("oxobject2group");
        $oGarbage->setId("_testoxobject2group");
        $oGarbage->oxobject2payment__oxobjectid = new oxField($oPayment->getId());
        $oGarbage->oxobject2payment__oxgroupsid = new oxField("yyy");
        $oGarbage->oxobject2payment__oxshopid = new oxField($this->getConfig()->getShopId());
        $oGarbage->save();

        $oPaymentList = oxNew('oxPaymentList');
        $oPaymentList->getPaymentList($sShipSetId, 10, $oUser);
        $iNewListCount = $oPaymentList->count();

        // list must contain at least one item
        $this->assertTrue($iNewListCount > 0);
        $this->assertTrue($iNewListCount === $iListCount);

        $blFound = false;
        foreach ($oPaymentList as $oPay) {
            if ($oPayment->getId() == $oPay->getId()) {
                $blFound = true;
                break;
            }
        }
        $this->assertTrue($blFound, "Error, delivery set not found");
    }


    // just SQL cleaner ..
    protected function cleanSQL($sQ)
    {
        return preg_replace(array('/[^\w\'\:\-\.\*\<\=]/'), '', $sQ);
    }

    /**
     * Use case:
     *
     * PAYMENTS:
     * + payment Nachnahme:
     *     - price: 8.5 abs
     *     - purchase price: 0 - 1000000
     *     - sorting: 0
     *     - assigned user groups: all
     *     - assigned countries: all
     *     - OXID = oxidcashondel
     * + payment Nachnahme (COD):
     *     - price: 25 abs
     *     - purchase price: 0 - 1000000
     *     - sorting: 0
     *     - assigned user groups: all
     *     - assigned countries: all excl. Germany
     *     - OXID = dbf741c04bf63f17f5e998d41236d55e
     * + all other payments OFF
     *
     * DELIVERIES:
     * + all standard deliveries + customizations:
     *     - Versandkosten f�r Standard: Versandkostenfrei ab 80, - Germany only
     *     - Versandkosten f�r Standard: 3,90 Euro innerhalb Deutschland - Germany only
     *     - Versandkosten f�r Standard: 6,90 Rest EU - excluding Germany
     *     - Versandkosten f�r Beispiel Set1: UPS 48 Std.: 9,90. - all countries
     *     - Versandkosten f�r Beispiel Set2: UPS 24 Std. Express: 12,90. - all countries
     *
     * DELIVERY SETS:
     * + only custom;
     * + UPS Standard (CH):
     *     - sorting: 0;
     *     - countries: Schweiz only;
     *     - deliveries: Versandkosten f�r Beispiel Set1: UPS 48 Std.: 9,90.-;
     *     - payments: Nachnahme (COD), Rechnung, Vorauskasse 2% Skonto;
     *     - user groups/users assigned: none;
     *     - OXID = 1b842e732a23255b1.91207750
     * + deutschland_test:
     *     - sorting: 0
     *     - countries: Germany only;
     *     - deliveries: Versandkosten f�r Beispiel Set2: UPS 24 Std. Express: 12,90.-;
     *     - payments: all available;
     *     - user groups/users assigned: none;
     *     - OXID = 1b842e732a23255b1.91207751
     * + UPS Standard (Inland):
     *     - sorting: 1;
     *     - countries: Germany only;
     *     - deliveries: Versandkosten f�r Standard: 3,90 Euro innerhalb Deutschland, Versandkosten f�r Standard: Versandkostenfrei ab 80,-;
     *     - payments: Nachnahme, Rechnung, Vorauskasse 2% Skonto;
     *     - user groups/users assigned: none;
     *     - OXID = oxidstandard
     */
    public function testGetPaymentListforUseCase()
    {
        $oDb = $this->getDb();
        $iShopId = $this->getConfig()->getShopId();

        $sGermanyId = "a7c40f631fc920687.20179984";
        $sSchweizId = "a7c40f6321c6f6109.43859248";

        // disabling payments
        $oDb->execute('update oxpayments set oxactive = 0');

        // enabling and setupping Nachnahme payment
        $oPayment = oxNew('oxPayment');
        $oPayment->load('oxidcashondel');
        $oPayment->oxpayments__oxactive = new oxField(1);
        $oPayment->oxpayments__oxaddsum = new oxField(8.5);
        $oPayment->oxpayments__oxaddsumtype = new oxField("abs");
        $oPayment->oxpayments__oxfromamount = new oxField(0);
        $oPayment->oxpayments__oxtoamount = new oxField(1000000);
        $oPayment->oxpayments__oxsort = new oxField(0);
        $oPayment->save();

        // assigning groups
        $oObjectToGroup = oxNew('oxObject2Group');
        $oObjectToGroup->setId($this->_getUId());
        $oObjectToGroup->oxobject2group__oxshopid = new oxField($iShopId);
        $oObjectToGroup->oxobject2group__oxobjectid = new oxField($oPayment->getId());
        $oObjectToGroup->oxobject2group__oxgroupsid = new oxField('oxidadmin');
        $oObjectToGroup->save();

        // assigning coutries (Deutschland)
        $oObjectToPayment = oxNew('oxBase');
        $oObjectToPayment->init('oxobject2payment');
        $oObjectToPayment->setId($this->_getUId());
        $oObjectToPayment->oxobject2payment__oxpaymentid = new oxField($oPayment->getId());
        $oObjectToPayment->oxobject2payment__oxobjectid = new oxField($sGermanyId);
        $oObjectToPayment->oxobject2payment__oxtype = new oxField('oxcountry');
        $oObjectToPayment->save();

        // enabling and setupping Nachnahme (COD) payment
        $oPayment = oxNew('oxPayment');
        $oPayment->setId('_bf741c04bf63f17f5e998d41236d55e');
        $oPayment->oxpayments__oxdesc = new oxField("Nachnahme (COD)");
        $oPayment->oxpayments__oxactive = new oxField(1);
        $oPayment->oxpayments__oxaddsum = new oxField(25);
        $oPayment->oxpayments__oxaddsumtype = new oxField("abs");
        $oPayment->oxpayments__oxfromamount = new oxField(0);
        $oPayment->oxpayments__oxtoamount = new oxField(1000000);
        $oPayment->oxpayments__oxsort = new oxField(0);
        $oPayment->save();

        // assigning groups
        $oObjectToGroup = oxNew('oxObject2Group');
        $oObjectToGroup->setId($this->_getUId());
        $oObjectToGroup->oxobject2group__oxshopid = new oxField($iShopId);
        $oObjectToGroup->oxobject2group__oxobjectid = new oxField($oPayment->getId());
        $oObjectToGroup->oxobject2group__oxgroupsid = new oxField('oxidadmin');
        $oObjectToGroup->save();

        // assigning coutries (Schweiz)
        $oObjectToPayment = oxNew('oxBase');
        $oObjectToPayment->init('oxobject2payment');
        $oObjectToPayment->setId($this->_getUId());
        $oObjectToPayment->oxobject2payment__oxpaymentid = new oxField($oPayment->getId());
        $oObjectToPayment->oxobject2payment__oxobjectid = new oxField($sSchweizId);
        $oObjectToPayment->oxobject2payment__oxtype = new oxField('oxcountry');
        $oObjectToPayment->save();

        // DELIVERIES:

        // Versandkosten f�r Standard: Versandkostenfrei ab 80, - Germany only
        $oDb->execute("delete from oxobject2delivery where oxdeliveryid='1b842e734b62a4775.45738618'");

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e734b62a4775.45738618");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        // Versandkosten f�r Standard: 3,90 Euro innerhalb Deutschland - Germany only
        $oDb->execute("delete from oxobject2delivery where oxdeliveryid='1b842e73470578914.54719298'");

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e73470578914.54719298");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        // Versandkosten f�r Standard: 6,90 Rest EU - excluding Germany
        $oDb->execute("delete from oxobject2delivery where oxdeliveryid='1b842e7352422a708.01472527'");

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e7352422a708.01472527");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sSchweizId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        // Versandkosten f�r Beispiel Set1: UPS 48 Std.: 9,90. - all countries
        $oDb->execute("delete from oxobject2delivery where oxdeliveryid='1b842e738970d31e3.71258327'");

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e738970d31e3.71258327");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e738970d31e3.71258327");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sSchweizId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        // Versandkosten f�r Beispiel Set2: UPS 24 Std. Express: 12,90. - all countries
        $oDb->execute("delete from oxobject2delivery where oxdeliveryid='1b842e738970d31e3.71258328'");

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e738970d31e3.71258328");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        $oObjectToDelivery = oxNew('oxBase');
        $oObjectToDelivery->init('oxobject2delivery');
        $oObjectToDelivery->setId($this->_getUId());
        $oObjectToDelivery->oxobject2delivery__oxdeliveryid = new oxField("1b842e738970d31e3.71258328");
        $oObjectToDelivery->oxobject2delivery__oxobjectid = new oxField($sSchweizId);
        $oObjectToDelivery->oxobject2delivery__oxtype = new oxField("oxcountry");
        $oObjectToDelivery->save();

        // disabling default delivery sets
        $oDb->execute('update oxdeliveryset set oxactive = 0');

        //
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_' . md5(time()));
        $oDelSet->oxdeliveryset__oxshopid = new oxField($iShopId);
        $oDelSet->oxdeliveryset__oxactive = new oxField(1);
        $oDelSet->oxdeliveryset__oxtitle = new oxField("UPS Standard (CH)");
        $oDelSet->oxdeliveryset__oxpos = new oxField(0);
        $oDelSet->setId("1b842e732a23255b1.91207750");
        $oDelSet->save();

        // - countries: Schweiz only;
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId($this->_getUId());
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($oDelSet->getId());
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField($sSchweizId);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField("oxdelset");
        $oObject2Delivery->save();

        // - payments: Nachnahme (COD)
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oObject->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField("_bf741c04bf63f17f5e998d41236d55e");
        $oObject->oxobject2payment__oxobjectid = new oxField($oDelSet->getId());
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset");
        $oObject->save();

        // - deliveries: Versandkosten f�r Beispiel Set2: UPS 24 Std. Express: 12,90.-;
        $oDel2delset = oxNew('oxBase');
        $oDel2delset->init('oxdel2delset');
        $oDel2delset->setId($this->_getUId());
        $oDel2delset->oxdel2delset__oxdelid = new oxField("1b842e738970d31e3.71258327");
        $oDel2delset->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId());
        $oDel2delset->save();

        //
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId($this->_getUId());
        $oDelSet->oxdeliveryset__oxshopid = new oxField($iShopId);
        $oDelSet->oxdeliveryset__oxactive = new oxField(1);
        $oDelSet->oxdeliveryset__oxtitle = new oxField("deutschland_test");
        $oDelSet->oxdeliveryset__oxpos = new oxField(0);
        $oDelSet->setId("1b842e732a23255b1.91207751");
        $oDelSet->save();

        // - countries: Germany only;
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId($this->_getUId());
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($oDelSet->getId());
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField("oxdelset");
        $oObject2Delivery->save();

        // - payments: all available;
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oObject->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField('dbf741c04bf63f17f5e998d41236d55e');
        $oObject->oxobject2payment__oxobjectid = new oxField($oDelSet->getId());
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset");
        $oObject->save();

        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oObject->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField('oxidcashondel');
        $oObject->oxobject2payment__oxobjectid = new oxField($oDelSet->getId());
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset");
        $oObject->save();

        // - deliveries: Versandkosten f�r Beispiel Set2: UPS 24 Std. Express: 12,90.-;
        $oDel2delset = oxNew('oxBase');
        $oDel2delset->init('oxdel2delset');
        $oDel2delset->setId($this->_getUId());
        $oDel2delset->oxdel2delset__oxdelid = new oxField("1b842e738970d31e3.71258328");
        $oDel2delset->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId());
        $oDel2delset->save();

        //
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->load('oxidstandard');
        $oDelSet->oxdeliveryset__oxshopid = new oxField($iShopId);
        $oDelSet->oxdeliveryset__oxactive = new oxField(1);
        $oDelSet->oxdeliveryset__oxtitle = new oxField("UPS Standard (Inland)");
        $oDelSet->oxdeliveryset__oxpos = new oxField(1);
        $oDelSet->save();

        // - countries: Germany only;
        $oObject2Delivery = oxNew('oxBase');
        $oObject2Delivery->init('oxobject2delivery');
        $oObject2Delivery->setId($this->_getUId());
        $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($oDelSet->getId());
        $oObject2Delivery->oxobject2delivery__oxobjectid = new oxField($sGermanyId);
        $oObject2Delivery->oxobject2delivery__oxtype = new oxField("oxdelset");
        $oObject2Delivery->save();

        // - payments: Nachnahme, Rechnung, Vorauskasse 2% Skonto;
        $oObject = oxNew('oxBase');
        $oObject->init('oxobject2payment');
        $oObject->setId($this->_getUId());
        $oObject->oxobject2payment__oxpaymentid = new oxField($oDelSet->getId());
        $oObject->oxobject2payment__oxobjectid = new oxField('oxidcashondel');
        $oObject->oxobject2payment__oxtype = new oxField("oxdelset");
        $oObject->save();

        // - deliveries: Versandkosten f�r Standard: Versandkostenfrei ab 80,-;
        $oDel2delset = oxNew('oxBase');
        $oDel2delset->init('oxdel2delset');
        $oDel2delset->setId($this->_getUId());
        $oDel2delset->oxdel2delset__oxdelid = new oxField("1b842e73470578914.54719298");
        $oDel2delset->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId());
        $oDel2delset->save();

        $oDel2delset = oxNew('oxBase');
        $oDel2delset->init('oxdel2delset');
        $oDel2delset->setId($this->_getUId());
        $oDel2delset->oxdel2delset__oxdelid = new oxField("1b842e734b62a4775.45738618");
        $oDel2delset->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId());
        $oDel2delset->save();

        // finally testing
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $oPaymentList = oxNew('oxPaymentList');
        $aPaymentList = $oPaymentList->getPaymentList("1b842e732a23255b1.91207751", 2.5, $oUser);
        $this->assertEquals(1, count($aPaymentList));
        $oPayment = current($aPaymentList);
        $this->assertEquals("oxidcashondel", $oPayment->getId());
    }

    /**
     * Testing SQL getter
     */
    // no user passed
    public function testGetFilterSelectNoUser()
    {
        $sTable = getViewName('oxpayments');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');
        $sPaymentsTable = getViewName('oxpayments');

        $sTestQ = "select $sTable.* from( select distinct $sTable.* from $sTable ";
        $sTestQ .= "left join oxobject2group ON oxobject2group.oxobjectid = $sTable.oxid ";
        $sTestQ .= "inner join oxobject2payment ON oxobject2payment.oxobjectid = 'xxx' and oxobject2payment.oxpaymentid = $sTable.oxid ";
        $sTestQ .= "where $sTable.oxactive='1' "; // and oxobject2group.oxobjectid = $sTable.oxid
        $sTestQ .= "and $sPaymentsTable.oxfromboni <= '0' and $sPaymentsTable.oxfromamount <= '666' and $sPaymentsTable.oxtoamount >= '666' ";
        $sTestQ .= " order by {$sTable}.oxsort asc ) as $sTable where ( select if( exists( select 1 from oxobject2payment as ss1, $sCountryTable where $sCountryTable.oxid=ss1.oxobjectid and ss1.oxpaymentid=$sTable.OXID and ss1.oxtype='oxcountry' limit 1),
                    exists( select 1 from oxobject2payment as s1 where s1.oxpaymentid=$sTable.OXID and s1.oxtype='oxcountry' and s1.OXOBJECTID='a7c40f631fc920687.20179984' limit 1 ), 1) &&
                    if( exists( select 1 from oxobject2group as ss3, $sGroupTable where $sGroupTable.oxid=ss3.oxgroupsid and ss3.OXOBJECTID=$sTable.OXID limit 1), 0, 1) ) ) order by $sTable.oxsort asc ";

        $oList = oxNew('oxPaymentList');
        $sQ = $oList->UNITgetFilterSelect('xxx', 666, null);

        $this->assertEquals($this->cleanSQL($sTestQ), $this->cleanSQL($sQ));
    }

    /**
     * Testing SQL getter when  no user passed
     */
    public function testGetFilterSelectAdminUser()
    {
        $this->oUser->addToGroup('_testGroupId');
        $sGroupIds = '';
        foreach ($this->oUser->getUserGroups() as $oGroup) {
            if ($sGroupIds) {
                $sGroupIds .= ', ';
            }
            $sGroupIds .= "'" . $oGroup->getId() . "'";
        }

        $sTable = getViewName('oxpayments');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $sTestQ = "select $sTable.* from( select distinct $sTable.* from $sTable ";
        $sTestQ .= "left join oxobject2group ON oxobject2group.oxobjectid = $sTable.oxid ";
        $sTestQ .= "inner join oxobject2payment ON oxobject2payment.oxobjectid = 'xxx' and oxobject2payment.oxpaymentid = $sTable.oxid ";
        $sTestQ .= "where $sTable.oxactive='1' "; //  and oxobject2group.oxobjectid = $sTable.oxid
        $sTestQ .= "and $sTable.oxfromboni <= '1000' and $sTable.oxfromamount <= '666' and $sTable.oxtoamount >= '666' ";
        $sTestQ .= " order by {$sTable}.oxsort asc ) as $sTable where ( select if( exists( select 1 from oxobject2payment as ss1, $sCountryTable where $sCountryTable.oxid=ss1.oxobjectid and ss1.oxpaymentid=$sTable.OXID and ss1.oxtype='oxcountry' limit 1),
                    exists( select 1 from oxobject2payment as s1 where s1.oxpaymentid=$sTable.OXID and s1.oxtype='oxcountry' and s1.OXOBJECTID='a7c40f631fc920687.20179984' limit 1), 1) &&
                    if( exists( select 1 from oxobject2group as ss3, $sGroupTable where $sGroupTable.oxid=ss3.oxgroupsid and ss3.OXOBJECTID=$sTable.OXID limit 1),
                    exists( select 1 from oxobject2group as s3 where s3.OXOBJECTID=$sTable.OXID and s3.OXGROUPSID in ( $sGroupIds ) limit 1), 1) ) ) order by $sTable.oxsort asc ";

        $oList = oxNew('oxPaymentList');
        $sQ = $oList->UNITgetFilterSelect('xxx', 666, $this->oUser);

        $this->assertEquals($this->cleanSQL($sTestQ), $this->cleanSQL($sQ));
    }

    /**
     * Testing country id getter
     */
    // testing home country setter and getter
    public function testSetHomeCountryAndGetCountryId()
    {
        $oList = oxNew('oxPaymentList');

        // testing default
        $this->assertEquals('a7c40f631fc920687.20179984', $oList->getCountryId(null));

        // now resetting country ids
        $oList->setHomeCountry(null);
        $this->assertNull($oList->getCountryId(null));

        // now passing user and testing
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('xxx'));

        $this->assertEquals('xxx', $oList->getCountryId($oUser));

        // setting array
        $oList->setHomeCountry(array('a', 'b'));
        $this->assertEquals('a', $oList->getCountryId(null));

        // setting string
        $oList->setHomeCountry('a');
        $this->assertEquals('a', $oList->getCountryId(null));
    }

    /**
     * Testing payment list getter
     */
    // no valid delivery set - no payment in list
    public function testGetPaymentListNoValidDelSet()
    {
        $oList = oxNew('oxPaymentList');
        $this->assertEquals(array(), $oList->getPaymentList('xxx', 55, $this->oUser));
    }

    // valid delivery set, but price is too high
    public function testGetPaymentListPriceIsTooHigh()
    {
        // making payments active
        foreach ($this->_aPayList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
            $oPayment->save();
        }

        $oList = oxNew('oxPaymentList');
        $this->assertEquals(array(), $oList->getPaymentList($this->oDelSet->getId(), 666666, $this->oUser));
    }

    // all input is just fine + admin user
    public function testGetPaymentListAllIsFinePlusUserIsPassed()
    {
        // making payments active
        foreach ($this->_aPayList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
            $oPayment->save();
        }

        $aResult = array($this->_aPayList[0]->getId(), $this->_aPayList[1]->getId(), $this->_aPayList[2]->getId(), $this->_aPayList[3]->getId());
        $oList = oxNew('oxPaymentList');
        $this->assertEquals($aResult, array_keys($oList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));
    }

    // all input is just fine + no user, will be used default country id from config
    public function testGetPaymentListAllIsFinePlusNoUser()
    {
        // making payments active
        foreach ($this->_aPayList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
            $oPayment->save();
        }

        $oList = oxNew('oxPaymentList');
        $this->assertEquals(0, count($oList->getPaymentList($this->oDelSet->getId(), 55)));
    }

    // buglist_332 sorting test
    public function testGetPaymentListAllIsFineInSpecSorting()
    {
        // making payments active
        $this->_aPayList[0]->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
        $this->_aPayList[0]->oxpayments__oxsort = new oxField(1, oxField::T_RAW);
        $this->_aPayList[0]->save();
        $this->_aPayList[1]->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
        $this->_aPayList[1]->oxpayments__oxsort = new oxField(4, oxField::T_RAW);
        $this->_aPayList[1]->save();
        $this->_aPayList[2]->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
        $this->_aPayList[2]->oxpayments__oxsort = new oxField(2, oxField::T_RAW);
        $this->_aPayList[2]->save();
        $this->_aPayList[3]->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
        $this->_aPayList[3]->oxpayments__oxsort = new oxField(3, oxField::T_RAW);
        $this->_aPayList[3]->save();

        $aResult = array($this->_aPayList[0]->getId(), $this->_aPayList[2]->getId(), $this->_aPayList[3]->getId(), $this->_aPayList[1]->getId());
        $oPaymentList = oxNew('oxPaymentList');
        $this->assertEquals($aResult, array_keys($oPaymentList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));
    }

    /**
     * Testing oxpaymentlist::getPaymentList()
     */
    public function testGetPaymentListWhenUserHasNoGroup()
    {
        $aResult = array($this->_aPayList[0]->getId(), $this->_aPayList[1]->getId(), $this->_aPayList[2]->getId(), $this->_aPayList[3]->getId());
        $oList = oxNew('oxPaymentList');
        $this->assertEquals($aResult, array_keys($oList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));

        $this->oUser->removeFromGroup('oxidadmin');

        $this->assertEquals(array(), array_keys($oList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));
    }

    /**
     * Testing oxpaymentlist::getPaymentList()
     */
    public function testGetPaymentListWhenPaymentIsAssignedToOtherGroupThanUser()
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxobject2group WHERE oxobjectid='oxdefaultadmin'");
        $oDb->execute("DELETE FROM oxobject2group WHERE oxgroupsid='oxidadmin'");

        $aResult = array($this->_aPayList[0]->getId(), $this->_aPayList[1]->getId(), $this->_aPayList[2]->getId(), $this->_aPayList[3]->getId());
        $oList = oxNew('oxPaymentList');
        $this->assertEquals($aResult, array_keys($oList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));

        //assigning payment for group
        $oO2Group = oxNew('oxObject2Group');
        $oO2Group->setId($this->_getUId());
        $oO2Group->oxobject2group__oxobjectid = new oxField($this->_aPayList[0]->getId(), oxField::T_RAW);
        $oO2Group->oxobject2group__oxgroupsid = new oxField('oxidcustomer', oxField::T_RAW);
        $oO2Group->save();

        //remove first element
        array_shift($aResult);
        $this->assertEquals($aResult, array_keys($oList->getPaymentList($this->oDelSet->getId(), 55, $this->oUser)));
    }

    /**
     * Testing oxpaymentlist::loadNonRDFaPaymentList()
     */
    public function testLoadNonRDFaPaymentList()
    {
        // making payments active
        foreach ($this->_aPayList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
            $oPayment->save();
        }

        $oObjectToPayment = oxNew('oxBase');
        $oObjectToPayment->init('oxobject2payment');
        $oObjectToPayment->setId('_testoxobject2payment');
        $oObjectToPayment->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[0]->getId());
        $oObjectToPayment->oxobject2payment__oxobjectid = new oxField('VISA');
        $oObjectToPayment->oxobject2payment__oxtype = new oxField('rdfapayment');
        $oObjectToPayment->save();

        $oPaymentList = oxNew('oxPaymentList');
        $oPaymentList->loadNonRDFaPaymentList();
        $this->assertEquals(3, $oPaymentList->count());
    }

    /**
     * Testing oxpaymentlist::loadRDFaPaymentList()
     */
    public function testLoadRDFaPaymentList()
    {
        // making payments active
        foreach ($this->_aPayList as $oPayment) {
            $oPayment->oxpayments__oxactive = new oxField(1, oxField::T_RAW);
            $oPayment->save();
        }

        $oObjectToPayment = oxNew('oxBase');
        $oObjectToPayment->init('oxobject2payment');
        $oObjectToPayment->setId('_testoxobject2payment');
        $oObjectToPayment->oxobject2payment__oxpaymentid = new oxField($this->_aPayList[0]->getId());
        $oObjectToPayment->oxobject2payment__oxobjectid = new oxField('VISA');
        $oObjectToPayment->oxobject2payment__oxtype = new oxField('rdfapayment');
        $oObjectToPayment->save();

        $oPaymentList = oxNew('oxPaymentList');
        $oPaymentList->loadRDFaPaymentList(12);

        $this->assertEquals(4, $oPaymentList->count());
        foreach ($oPaymentList as $oPayment) {
            if ($oPayment->getId() == $this->_aPayList[0]->getId()) {
                $this->assertEquals('VISA', $oPayment->oxpayments__oxobjectid->value);
            } else {
                $this->assertNull($oPayment->oxpayments__oxobjectid->value);
            }
        }
    }
}
