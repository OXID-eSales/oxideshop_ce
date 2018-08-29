<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Payment;

use \Exception;
use \oxTestModules;

/**
 * Tests for Payment_Main class
 */
class PaymentMainTest extends \OxidTestCase
{

    /**
     * Payment_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Payment_Main');
        $this->assertEquals('payment_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof payment);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('payment_main');
        $this->assertEquals('payment_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Payment_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxpayment', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Payment_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Payment_Main::save()");

            return;
        }
        $this->fail("error in Payment_Main::save()");
    }

    /**
     * Payment_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        // testing..
        oxTestModules::addFunction('oxpayment', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Payment_Main');
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Payment_Main::saveinnlang()");

            return;
        }
        $this->fail("error in Payment_Main::saveinnlang()");
    }

    /**
     * Payment_Main::DelFields() test case
     *
     * @return null
     */
    public function testDelFields()
    {
        oxTestModules::addFunction('oxpayment', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ return array( "testField1", "testField2"); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);
        $this->setRequestParameter("aFields", array("testField2"));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PaymentMain::class, array("save"));
        $oView->expects($this->once())->method('save');
        $oView->delFields();
    }

    /**
     * Payment_Main::AddField() test case
     */
    public function testAddField()
    {
        oxTestModules::addFunction('oxPayment', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ return array( "testField1", "testField2"); }');
        $this->getConfig()->setConfigParam('blAllowSharedEdit', true);
        $this->setRequestParameter('sAddField', 'foobar');

        $view = $this->getMock($this->getProxyClassName('Payment_Main'), array('save'));
        $view->expects($this->once())->method('save');
        $view->addField();

        $fields = $view->getNonPublicVar('_aFieldArray');
        $this->assertCount(3, $fields);
        $this->assertEquals('foobar', $fields[2]->name);
    }

    /**
     * Payment_Main::AddField() test case
     *
     * Do not add field if it is empty
     * @see https://bugs.oxid-esales.com/view.php?id=6450
     */
    public function testAddFieldDoNotSaveEmptyField()
    {
        oxTestModules::addFunction('oxPayment', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'assignValuesFromText', '{ return array( "testField1", "testField2"); }');
        $this->getConfig()->setConfigParam('blAllowSharedEdit', true);
        $this->setRequestParameter('sAddField', '');

        $view = $this->getMock($this->getProxyClassName('Payment_Main'), array('save'));
        $view->expects($this->once())->method('save');
        $view->addField();

        $this->assertCount(2, $view->getNonPublicVar('_aFieldArray'));
    }
}
