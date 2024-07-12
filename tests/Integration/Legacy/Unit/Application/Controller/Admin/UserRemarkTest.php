<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Model\ListModel;
use \oxField;
use \Exception;
use \oxTestModules;

/**
 * Testing User_Remark class
 */
class UserRemarkTest extends \PHPUnit\Framework\TestCase
{

    /**
     * user_remark::render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxRemark', 'load($sId)', '{$this->oxremark__oxtext = new oxField("text-$sId");$this->oxremark__oxheader = new oxField("header-$sId");}');
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("rem_oxid", "testId");

        $oView = oxNew('user_remark');
        $this->assertSame("user_remark", $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\User::class, $aViewData['edit']);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Model\ListModel::class, $aViewData['allremark']);
        $this->assertSame('text-testId', $aViewData['remarktext']);
        $this->assertSame('header-testId', $aViewData['remarkheader']);
    }

    /**
     * user_remark::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxremark', 'load', '{ return true; }');
        oxTestModules::addFunction('oxremark', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter('oxid', 'oxdefaultadmin');
        $this->setRequestParameter('remarktext', 'test text');
        $this->setRequestParameter('remarkheader', 'test header');

        try {
            $oView = oxNew('user_remark');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "Error in user_remark::save()");

            return;
        }

        $this->fail("Error in user_remark::save()");
    }

    /**
     * user_remark::testDelete() test case
     */
    public function testDelete()
    {
        oxTestModules::addFunction('oxremark', 'delete', '{ throw new Exception( "delete" ); }');

        try {
            $oView = oxNew('user_remark');
            $oView->delete();
        } catch (Exception $exception) {
            $this->assertSame("delete", $exception->getMessage(), "Error in user_remark::delete()");

            return;
        }

        $this->fail("Error in user_remark::delete()");
    }
}
