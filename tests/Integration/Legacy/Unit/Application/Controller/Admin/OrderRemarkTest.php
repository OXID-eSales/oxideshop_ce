<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \Exception;
use \oxDb;
use \oxTestModules;

/**
 * Testing Order_Remark class
 */
class OrderRemarkTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDB()->execute('delete from oxremark where oxtext = "test text"');
        $this->cleanUpTable('oxorder');
        parent::tearDown();
    }

    /**
     * order_remark::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("rem_oxid", "testId");

        $oView = oxNew('order_remark');
        $this->assertSame("order_remark", $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('allremark', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Model\ListModel::class, $aViewData['allremark']);
    }

    /**
     * order_remark::save() test case
     */
    public function testSave()
    {
        $this->setRequestParameter('oxid', '_testOrder');
        $this->setRequestParameter('remarktext', 'test text');
        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrder');

        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->save();
        $oView = oxNew('order_remark');
        $oView->save();

        $oRemark = oxNew("oxRemark");
        $oRemark->load("_testRemark");
        $this->assertSame('r', oxDb::getDB()->getOne('select oxtype from oxremark where oxtext = "test text"'));
        $this->assertSame('oxdefaultadmin', oxDb::getDB()->getOne('select oxparentid from oxremark where oxtext = "test text"'));
    }

    /**
     * order_remark::Render() test case
     */
    public function testDelete()
    {
        oxTestModules::addFunction('oxRemark', 'delete', '{ throw new Exception( "delete" ); }');

        // testing..
        try {
            $oView = oxNew('order_remark');
            $oView->delete();
        } catch (Exception $exception) {
            $this->assertSame("delete", $exception->getMessage(), "Error in order_remark::delete()");

            return;
        }

        $this->fail("Error in order_remark::delete()");
    }
}
