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
class DeliverySetRDFaTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxobject2delivery');

        parent::tearDown();
    }

    /**
     * DeliverySet_RDFa::save() delete old records test case
     *
     * @return null
     */
    public function testSave_deleteOldRecords()
    {
        $sTestID = '_test_recid';
        $this->setRequestParameter('oxid', $sTestID);

        $oMapping = oxNew('oxBase');
        $oMapping->init('oxobject2delivery');
        $oMapping->oxobject2delivery__oxdeliveryid = new oxField($sTestID);
        $oMapping->oxobject2delivery__oxobjectid = new oxField('test_del_objID');
        $oMapping->oxobject2delivery__oxtype = new oxField('rdfadeliveryset');
        $oMapping->save();

        $oDB = oxDb::getDb();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?',
            array($sTestID, 'rdfadeliveryset')
        );
        $this->assertFalse(empty($iExists));

        $oView = oxNew('DeliverySet_RDFa');
        $oView->save();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?',
            array($sTestID, 'rdfadeliveryset')
        );
        $this->assertTrue(empty($iExists));
    }

    /**
     * DeliverySet_RDFa::save() create records test case
     *
     * @return null
     */
    public function testSave_createRecords()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        $this->setRequestParameter('oxid', $sTestID);
        $this->setRequestParameter('ardfadeliveries', $aObjIDs);
        $this->setRequestParameter(
            'editval',
            array(
                 'oxobject2delivery__oxdeliveryid' => $sTestID,
                 'oxobject2delivery__oxtype'       => 'rdfadeliveryset',
            )
        );

        $oDB = oxDb::getDb();

        $oView = oxNew('DeliverySet_RDFa');
        $oView->save();

        $aCurrObjIDs = $oDB->GetCol(
            'SELECT oxobjectid FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?',
            array($sTestID, 'rdfadeliveryset')
        );
        sort($aObjIDs);
        sort($aCurrObjIDs);
        $this->assertSame($aObjIDs, $aCurrObjIDs);
    }

    /**
     * DeliverySet_RDFa::getAllRDFaDeliveries() test case
     *
     * @return null
     */
    public function testGetAllRDFaDeliveries()
    {
        $aAssignedRDFaDeliveries = array('DeliveryModeOwnFleet');
        $aExpResp = array();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetRdfa::class, array('getAssignedRDFaDeliveries'));
        $oView->expects($this->once())->method('getAssignedRDFaDeliveries')->will($this->returnValue($aAssignedRDFaDeliveries));
        $aCurrResp = $oView->getAllRDFaDeliveries();

        $this->assertTrue(is_array($aCurrResp), 'Array should be returned');
        $this->assertTrue(count($aCurrResp) > 0, 'Empty array returned');
        $this->assertTrue(current($aCurrResp) instanceof stdClass, 'Array elements should be of type stdClass');

        $blFound = false;
        foreach ($aCurrResp as $oItem) {
            foreach ($aAssignedRDFaDeliveries as $sAssignedName) {
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
     * DeliverySet_RDFa::getAssignedRDFaDeliveries() test case
     *
     * @return null
     */
    public function testGetAssignedRDFaDeliveries()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        $this->setRequestParameter('oxid', $sTestID);
        $oView = oxNew('DeliverySet_RDFa');

        $oDB = oxDb::getDb();
        $oDB->Execute('DELETE FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?', array($sTestID, 'rdfadeliveryset'));
        $this->assertSame(array(), $oView->getAssignedRDFaDeliveries(), 'Should be empty array');

        foreach ($aObjIDs as $sObjID) {
            $oMapping = oxNew('oxBase');
            $oMapping->init('oxobject2delivery');
            $oMapping->oxobject2delivery__oxdeliveryid = new oxField($sTestID);
            $oMapping->oxobject2delivery__oxobjectid = new oxField($sObjID);
            $oMapping->oxobject2delivery__oxtype = new oxField('rdfadeliveryset');
            $oMapping->save();
        }

        $aResp = $oView->getAssignedRDFaDeliveries();
        sort($aObjIDs);
        sort($aResp);
        $this->assertSame($aObjIDs, $aResp);
    }
}
