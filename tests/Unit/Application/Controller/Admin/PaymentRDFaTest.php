<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \stdClass;

use \oxField;
use \oxDb;

/**
 * Tests for Shop_Main class
 */
class PaymentRDFaTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxobject2payment');

        parent::tearDown();
    }

    /**
     * Payment_RDFa::save() delete old records test case
     *
     * @return null
     */
    public function testSave_deleteOldRecords()
    {
        $sTestID = '_test_recid';
        $this->setRequestParameter('oxid', $sTestID);

        $oMapping = oxNew('oxBase');
        $oMapping->init('oxobject2payment');
        $oMapping->oxobject2payment__oxpaymentid = new oxField($sTestID);
        $oMapping->oxobject2payment__oxobjectid = new oxField('test_del_objID');
        $oMapping->oxobject2payment__oxtype = new oxField('rdfapayment');
        $oMapping->save();

        $oDB = oxDb::getDb();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2payment WHERE oxpaymentid = ? AND oxtype = ?',
            array($sTestID, 'rdfapayment')
        );
        $this->assertFalse(empty($iExists));

        $oView = oxNew('Payment_RDFa');
        $oView->save();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2payment WHERE oxpaymentid = ? AND oxtype = ?',
            array($sTestID, 'rdfapayment')
        );
        $this->assertTrue(empty($iExists));
    }

    /**
     * Payment_RDFa::save() create records test case
     *
     * @return null
     */
    public function testSave_createRecords()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        $this->setRequestParameter('oxid', $sTestID);
        $this->setRequestParameter('ardfapayments', $aObjIDs);
        $this->setRequestParameter(
            'editval',
            array(
                 'oxobject2payment__oxpaymentid' => $sTestID,
                 'oxobject2payment__oxtype'      => 'rdfapayment',
            )
        );

        $oDB = oxDb::getDb();

        $oView = oxNew('Payment_RDFa');
        $oView->save();

        $aCurrObjIDs = $oDB->GetCol(
            'SELECT oxobjectid FROM oxobject2payment WHERE oxpaymentid = ? AND oxtype = ?',
            array($sTestID, 'rdfapayment')
        );
        sort($aObjIDs);
        sort($aCurrObjIDs);
        $this->assertSame($aObjIDs, $aCurrObjIDs);
    }

    /**
     * Payment_RDFa::getAllRDFaPayments() test case
     *
     * @return null
     */
    public function testGetAllRDFaPayments()
    {
        $aAssignedRDFaPayments = array('GoogleCheckout');
        $aExpResp = array();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PaymentRdfa::class, array('getAssignedRDFaPayments'));
        $oView->expects($this->once())->method('getAssignedRDFaPayments')->will($this->returnValue($aAssignedRDFaPayments));
        $aCurrResp = $oView->getAllRDFaPayments();

        $this->assertTrue(is_array($aCurrResp), 'Array should be returned');
        $this->assertTrue(count($aCurrResp) > 0, 'Empty array returned');
        $this->assertTrue(current($aCurrResp) instanceof stdClass, 'Array elements should be of type stdClass');

        $blFound = false;
        foreach ($aCurrResp as $oItem) {
            foreach ($aAssignedRDFaPayments as $sAssignedName) {
                if (strcasecmp($oItem->name, $sAssignedName) === 0) {
                    if ($oItem->checked !== true) {
                        $this->fail('Item "' . $sAssignedName . '" should be set as active');
                    }
                } else {
                    if ($oItem->checked === true) {
                        $this->fail('Item "' . $sAssignedName . '" should not be set as active');
                    }
                }
            }
        }
    }

    /**
     * Payment_RDFa::getAssignedRDFaPayments() test case
     *
     * @return null
     */
    public function testGetAssignedRDFaPayments()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        $this->setRequestParameter('oxid', $sTestID);
        $oView = oxNew('Payment_RDFa');

        $oDB = oxDb::getDb();
        $oDB->Execute('DELETE FROM oxobject2payment WHERE oxpaymentid = ? AND oxtype = ?', array($sTestID, 'rdfapayment'));
        $this->assertSame(array(), $oView->getAssignedRDFaPayments(), 'Should be empty array');

        foreach ($aObjIDs as $sObjID) {
            $oMapping = oxNew('oxBase');
            $oMapping->init('oxobject2payment');
            $oMapping->oxobject2payment__oxpaymentid = new oxField($sTestID);
            $oMapping->oxobject2payment__oxobjectid = new oxField($sObjID);
            $oMapping->oxobject2payment__oxtype = new oxField('rdfapayment');
            $oMapping->save();
        }

        $aResp = $oView->getAssignedRDFaPayments();
        sort($aObjIDs);
        sort($aResp);
        $this->assertSame($aObjIDs, $aResp);
    }
}
