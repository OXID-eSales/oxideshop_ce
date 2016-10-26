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
namespace Unit\Application\Controller;

use \OxidEsales\EshopCommunity\Application\Model\PaymentList;
use \OxidEsales\EshopCommunity\Application\Model\DeliverySetList;
use \OxidEsales\EshopCommunity\Application\Model\Delivery;
use \OxidEsales\EshopCommunity\Application\Model\DeliveryList;

use \oxUtilsView;
use \oxField;
use \Exception;
use \oxcontent;
use \stdClass;
use \oxDb;
use \oxTestModules;

/*
 * Dummy class for getParsedContent function test.
 *
 */
class contentTest_oxUtilsView extends oxUtilsView
{

    public function parseThroughSmarty($sDesc, $sOxid = null, $oActView = null, $blRecompile = false)
    {
        return $sDesc;
    }
}

/**
 * Tests for content class
 */
class ContentTest extends \OxidTestCase
{

    /** @var oxContent  */
    protected $_oObj = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oObj = oxNew('oxbase');
        $this->_oObj->init('oxcontents');
        $this->_oObj->oxcontents__oxtitle = new oxField('test', oxField::T_RAW);
        $sShopId = $this->getConfig()->getShopId();
        $this->_oObj->oxcontents__oxshopid = new oxField($sShopId, oxField::T_RAW);
        $this->_oObj->oxcontents__oxloadid = new oxField('_testLoadId', oxField::T_RAW);
        $this->_oObj->oxcontents__oxcontent = new oxField("testcontentDE&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        //$this->_oObj->oxcontents__oxcontent = new oxField('[{ $oxcmp_shop->oxshops__oxowneremail->value }]', oxField::T_RAW);
        $this->_oObj->oxcontents__oxcontent_1 = new oxField("testcontentENG&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        $this->_oObj->oxcontents__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oObj->oxcontents__oxactive_1 = new oxField('1', oxField::T_RAW);
        $this->_oObj->save();

        $sOxid = $this->_oObj->getId();

        $this->_oObj = oxNew('oxcontent');
        $this->_oObj->load($sOxid);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oObj->delete();
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxdel2delset');
        parent::tearDown();
        oxRemClassModule('contentTest_oxUtilsView');
    }

    /**
     * Content::_canShowContent() test case
     *
     * @return unknown_type
     */
    public function testCanShowContent()
    {
        $oView = $this->getMock("content", array("getUser", "isEnabledPrivateSales"), array(), '', false);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $oView->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(true));

        $this->assertTrue($oView->UNITcanShowContent("oxagb"));
        $this->assertTrue($oView->UNITcanShowContent("oxrightofwithdrawal"));
        $this->assertTrue($oView->UNITcanShowContent("oximpressum"));
        $this->assertFalse($oView->UNITcanShowContent("testcontentident"));

        $oView = $this->getMock("content", array("getUser", "isEnabledPrivateSales"), array(), '', false);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $oView->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));
        $this->assertTrue($oView->UNITcanShowContent("testcontentident"));
    }

    /**
     * Test active content id getter when content id passed with tpl param.
     *
     * @return null
     */
    public function testGetContentIdIfAgb()
    {
        $sContentId = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("SELECT oxid FROM oxcontents WHERE oxloadid = 'oxagb' ");
        $this->setRequestParameter('oxcid', $sContentId);
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oView = oxNew('content');
        $this->assertEquals($oView->getContentId(), $sContentId);
    }

    /**
     * Test get object seo id.
     *
     * @return null
     */
    public function testGetSeoObjectId()
    {
        $this->setRequestParameter('oxcid', 'testseoobjectid');

        $oContentView = oxNew('content');
        $this->assertEquals('testseoobjectid', $oContentView->UNITgetSeoObjectId());
    }

    /**
     * Test get view id.
     *
     * @return null
     */
    public function testGetViewId()
    {
        $this->setRequestParameter('oxcid', 'testparam');

        $oView = oxNew('oxubase');
        $oContentView = oxNew('content');
        $this->assertEquals($oView->getViewId() . '|testparam', $oContentView->getViewId());
    }

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("testContent");

        $oContentView = $this->getMock('content', array('_canShowContent', 'getContent', 'showPlainTemplate', '_getTplName'));
        $oContentView->expects($this->atLeastOnce())->method('_getTplName')->will($this->returnValue(false));
        $oContentView->expects($this->atLeastOnce())->method('_canShowContent')->will($this->returnValue(true));
        $oContentView->expects($this->atLeastOnce())->method('getContent')->will($this->returnValue($oContent));
        $oContentView->expects($this->atLeastOnce())->method('showPlainTemplate')->will($this->returnValue('true'));
        $this->assertEquals('page/info/content_plain.tpl', $oContentView->render());
    }

    /**
     * Test active content id getter
     *
     * @return null
     */
    public function testRenderPsOn()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("redirect"); }');

        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        try {
            // testing..
            $oView = $this->getMock("content", array("_canShowContent"), array(), '', false);
            $oView->expects($this->once())->method('_canShowContent')->will($this->returnValue(false));
            $oView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals("redirect", $oExcp->getMessage(), "Error in oxsclogincontent::getContentId()");

            return;
        }
        $this->fail("Error in content::getContentId()");
    }

    /**
     * Test prepare meta keywords.
     *
     * @return null
     */
    public function testPrepareMetaKeyword()
    {
        $oContent = oxNew('oxArticle');
        $oContent->oxcontents__oxtitle = $this->getMock('oxField', array('__get'));
        $oContent->oxcontents__oxtitle->expects($this->once())->method('__get')->will($this->returnValue('testtitle'));

        $oContentView = $this->getMock('content', array('getContent'));
        $oContentView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->UNITprepareMetaKeyword('testtitle'), $oContentView->UNITprepareMetaKeyword(null));
    }

    /**
     * Test prepare meta description.
     *
     * @return null
     */
    public function testPrepareMetaDescription()
    {
        $oContent = oxNew('oxArticle');
        $oContent->oxcontents__oxtitle = $this->getMock('oxField', array('__get'));
        $oContent->oxcontents__oxtitle->expects($this->once())->method('__get')->will($this->returnValue('testtitle'));

        $oContentView = $this->getMock('content', array('getContent'));
        $oContentView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->UNITprepareMetaDescription('testtitle'), $oContentView->UNITprepareMetaDescription(null));
    }

    /**
     * Test get content category without any assigned category.
     *
     * @return null
     */
    public function testGetContentCategoryNoCategoryAssigned()
    {
        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue(new oxcontent()));

        $this->assertFalse($oView->getContentCategory());
    }

    /**
     * Test get content category with assigned category.
     *
     * @return null
     */
    public function testGetContentCategorySomeCategoryAssigned()
    {
        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxtype = new oxfield(2);

        $oView = $this->getMock('content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));

        $this->assertEquals($oContent, $oView->getContentCategory());
    }

    /**
     * Test show plain template.
     *
     * @return null
     */
    public function testShowPlainTemplate()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);
        $this->setRequestParameter('plain', 0);
        $oView = oxNew('content');
        $this->assertFalse($oView->showPlainTemplate());

        $this->setRequestParameter('plain', 1);
        $oView = oxNew('content');
        $this->assertTrue($oView->showPlainTemplate());
    }

    /**
     * Test show plain template.
     *
     * @return null
     */
    public function testShowPlainTemplatePsOn()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);
        $this->setRequestParameter('plain', 0);
        $oView = oxNew('content');
        $this->assertTrue($oView->showPlainTemplate());

        $this->setRequestParameter('plain', 1);
        $oView = oxNew('content');
        $this->assertTrue($oView->showPlainTemplate());

        $this->setRequestParameter('plain', 0);

        $oView = $this->getMock('content', array('getUser'));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertTrue($oView->showPlainTemplate());

        $this->setRequestParameter('plain', 0);

        $oUser = $this->getMock('oxuser', array('isTermsAccepted'));
        $oUser->expects($this->any())->method('isTermsAccepted')->will($this->returnValue(true));

        $oView = $this->getMock('content', array('getUser'));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertFalse($oView->showPlainTemplate());

    }

    /**
     * Test active content id getter Test active content id getter when content id passed with oxcid param.
     *
     * @return null
     */
    public function testGetContentIdWithOxcidParam()
    {
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');

        $oObj = oxNew('content');

        // testing special chars conversion
        $this->assertEquals($oObj->getContentId(), $this->_oObj->getId());
    }

    /**
     * Test active content id getter
     *
     * @return null
     */
    public function testGetContentIdIfNotActive()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId('_testContent');
        $oContent->oxcontents__oxactive = new oxField('0');
        $oContent->save();
        $this->setRequestParameter('oxcid', $oContent->getId());

        $oContentView = oxNew('Content');

        // testing special chars conversion
        $this->assertFalse($oContentView->getContentId());
    }

    /**
     * Test active content id getter.
     *
     * @return null
     */
    public function testGetContentIdWhenNoIdSpecified()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $sContentId = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("SELECT oxid FROM oxcontents WHERE oxloadid = 'oximpressum' ");

        $oObj = oxNew('content');
        $this->assertEquals($sContentId, $oObj->getContentId());
    }

    /**
     * Test active content getter.
     *
     * @return null
     */
    public function testGetContent()
    {
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');

        $oObj = oxNew('content');

        // testing special chars conversion
        $this->assertEquals($oObj->getContent()->getId(), $this->_oObj->getId());
    }

    /**
     * Test getting template name.
     *
     * @return null
     */
    public function testGetTplName()
    {
        $this->setRequestParameter('tpl', 'test.tpl');
        $oObj = $this->getProxyClass("content");
        $this->assertEquals('message/test.tpl', $oObj->UNITgetTplName());
    }

    /**
     * Test if render returns setted template name.
     *
     * @return null
     */
    public function testRenderReturnSettedTemplateName()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oView = $this->getMock('content', array('_getTplName'));
        $oView->expects($this->once())->method('_getTplName')->will($this->returnValue('test.tpl'));

        $this->assertEquals('test.tpl', $oView->render());
    }

    /**
     * Test getting template name when tpl param contains
     * not template name, but content id.
     *
     * @return null
     */
    public function testGetTplNameWhenTplParamIsContentId()
    {
        $this->setRequestParameter('tpl', '2eb46767947d21851.22681675');
        $oObj = $this->getProxyClass("content");
        $this->assertNull($oObj->UNITgetTplName());
    }

    public function testContentNotFound()
    {
        $this->setRequestParameter('oxcid', null);
        $this->setRequestParameter('oxloadid', null);
        $oView = $this->getMock('content', array('_getTplName', 'getContentId'));
        $oView->expects($this->once())->method('_getTplName')->will($this->returnValue(''));
        $oView->expects($this->any())->method('getContentId')->will($this->returnValue(false));

        $oUtils = $this->getMock('oxutils', array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->will($this->throwException(new Exception("404")));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        try {
            $oView->render();
        } catch (Exception $e) {
            $this->assertEquals('404', $e->getMessage());

            return;
        }
        $this->fail("no exception");
    }

    /**
     * Testing Contact::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oContent = oxNew('Content');

        $this->assertEquals(1, count($oContent->getBreadCrumb()));
    }

    /**
     * Test get content title.
     *
     * @return null
     */
    public function testGetTitle()
    {
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $oContent = oxNew('content');
        $this->assertEquals('test', $oContent->getTitle());
    }

    /**
     * Content::showRdfa() Test case
     *
     * @return null
     */
    public function testShowRdfa()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('blRDFaEmbedding', '_test_value');

        $oView = oxNew('Content');
        $this->assertSame('_test_value', $oView->showRdfa());
    }

    /**
     * Content::getContentPageTpl() Test Business case
     *
     * @return null
     */
    public function testGetContentPageTpl_Business()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaBusinessEntityLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sBusinessTemplate');
        $this->assertFalse(empty($sExpResp));

        $oView = $this->getMock('Content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getContentPageTpl() Test Delivery case
     *
     * @return null
     */
    public function testGetContentPageTpl_Delivery()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaDeliveryChargeSpecLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sDeliveryTemplate');
        $this->assertFalse(empty($sExpResp));

        $oView = $this->getMock('Content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getContentPageTpl() Test Payment case
     *
     * @return null
     */
    public function testGetContentPageTpl_Payment()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaPaymentChargeSpecLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sPaymentTemplate');
        $this->assertFalse(empty($sExpResp));

        $oView = $this->getMock('Content', array('getContent'));
        $oView->expects($this->once())->method('getContent')->will($this->returnValue($oContent));
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getBusinessEntityExtends() Test case
     *
     * @return null
     */
    public function testGetBusinessEntityExtends()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('_test1', '_value1');
        $oConf->setConfigParam('_test2', '_value2');
        $aInput = array('_test2', '_test1', '_testX');
        $aExpResp = array('_test2' => '_value2', '_test1' => '_value1', '_testX' => null);

        $oViewProxy = $this->getProxyClass('Content');
        $oViewProxy->setNonPublicVar('_aBusinessEntityExtends', $aInput);
        $this->assertSame($aExpResp, $oViewProxy->getBusinessEntityExtends());
    }

    /**
     * Content::getNotMappedToRDFaPayments() Test case
     *
     * @return null
     */
    public function testGetNotMappedToRDFaPayments()
    {
        oxTestModules::addFunction('oxPaymentList', 'getNonRDFaPaymentList', '{ return \'_call_getNonRDFaPaymentList\'; }');

        $oView = oxNew('Content');
        $oResp = $oView->getNotMappedToRDFaPayments();
        $this->assertTrue($oResp instanceof PaymentList);
    }

    /**
     * Content::getNotMappedToRDFaDeliverySets() Test case
     *
     * @return null
     */
    public function testGetNotMappedToRDFaDeliverySets()
    {
        oxTestModules::addFunction('oxdeliverysetlist', 'getNonRDFaDeliverySetList', '{ return \'_call_getNonRDFaDeliverySetList\'; }');

        $oView = oxNew('Content');
        $oResp = $oView->getNotMappedToRDFaDeliverySets();
        $this->assertTrue($oResp instanceof DeliverySetList);
    }

    /**
     * Content::getDeliveryChargeSpecs() Test case
     *
     * @return null
     */
    public function testGetDeliveryChargeSpecs()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testDeliveryId');
        $oDelivery->oxdelivery__oxtitle = new oxField('_testDelivertTitle' . $i, oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(0, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(999999, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField(10, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('%', oxField::T_RAW);

        $oDelivery->save();

        $oDel2Delset = oxNew('oxBase');
        $oDel2Delset->init('oxdel2delset');
        $oDel2Delset->setId('_testDel2DelSetId');
        $oDel2Delset->oxdel2delset__oxdelid = new oxField($oDelivery->getId(), oxField::T_RAW);
        $oDel2Delset->oxdel2delset__oxdelsetid = new oxField('oxidstandard', oxField::T_RAW);
        $oDel2Delset->save();

        $oView = oxNew('Content');
        $oResp = $oView->getDeliveryChargeSpecs();

        $this->assertEquals(5, count($oResp));
        foreach ($oResp as $oDelivery) {
            $this->assertTrue($oDelivery instanceof Delivery);
            $this->assertTrue($oDelivery->deliverysetmethods instanceof DeliverySetList);
        }
    }

    /**
     * Content::getDeliveryList() Test case
     *
     * @return null
     */
    public function testGetDeliveryList()
    {
        oxTestModules::addFunction('oxdeliverylist', 'getList', '{ return \'_call_getList\'; }');

        $oView = oxNew('Content');
        $oResp = $oView->getDeliveryList();
        $this->assertTrue($oResp instanceof DeliveryList);
    }

    /**
     * Content::getRdfaVAT() Test case
     *
     * @return null
     */
    public function testGetRdfaVAT()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('iRDFaVAT', 123);

        $oView = oxNew('Content');
        $this->assertSame(123, $oView->getRdfaVAT());
    }

    /**
     * Content::getRdfaPriceValidity() Test case
     */
    public function testGetRdfaPriceValidity()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('iRDFaPriceValidity', 3);

        $oView = oxNew('Content');
        $aResp = $oView->getRdfaPriceValidity();
        $this->assertSame(array('validfrom', 'validthrough'), array_keys($aResp));

        $startTime = time() - (2 * 60 * 60);
        $endTime = $startTime + (3 * 24 * 60 * 60);

        $this->assertTrue(date('Y-m-d\TH:i:s', $startTime) <= $aResp['validfrom']);
        $this->assertTrue(date('Y-m-d\TH:i:s', $endTime) <= $aResp['validthrough']);
    }

    /**
     * Content::testGetParsedContent() Test case
     *
     * Add bugfix to #0004298: If there is smarty tag in content, then it is saved in same name template.
     */
    public function testGetParsedContent()
    {
        $this->_oObj->oxcontents__oxcontent = new oxField("[{ 'A'|cat:'B' }]SSSSSSSS", oxField::T_RAW);
        $this->_oObj->save();
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $oContent = oxNew('content');

        $this->assertEquals('ABSSSSSSSS', $oContent->getParsedContent(), 'Result from smarty not same as in content page.');

        // Check if second CMS page will be generated with different content.
        $oSecond = oxNew('oxcontent');
        $oSecond->setId('_test_testGetParsedContent'); // = new oxField('_test_testGetParsedContent');
        $oSecond->oxcontents__oxtitle = new oxField('test', oxField::T_RAW);
        $sShopId = $this->getConfig()->getShopId();
        $oSecond->oxcontents__oxshopid = new oxField($sShopId, oxField::T_RAW);
        $oSecond->oxcontents__oxloadid = new oxField('_testLoadId_testGetParsedContent', oxField::T_RAW);
        $oSecond->oxcontents__oxcontent = new oxField("[{ 'A'|cat:'D' }]SSSSSSSS", oxField::T_RAW);
        $oSecond->oxcontents__oxcontent_1 = new oxField("testcontentENG&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        $oSecond->oxcontents__oxactive = new oxField('1', oxField::T_RAW);
        $oSecond->oxcontents__oxactive_1 = new oxField('1', oxField::T_RAW);
        $oSecond->save();
        $this->setRequestParameter('oxcid', $oSecond->getId());
        $oContent = oxNew('content');

        $this->assertEquals( 'ADSSSSSSSS', $oContent->getParsedContent(), 'Content not as in second page. If result ABSSSSSSSS than it is ame as in first page, so used wrong smarty cache file.' );
    }

    /**
     * getParsedContent() test case
     * test returned parsed content with smarty tags when template regeneration is disabled
     * and template is saved twice.
     *
     * @return null
     */
    public function testGetParsedContentTagsWhenTemplateAlreadyGeneratedAndRegenerationDisabled()
    {
        $this->getConfig()->setConfigParam('blCheckTemplates', false);

        $this->_oObj->oxcontents__oxcontent = new oxField("[{* *}]generated", oxField::T_RAW);
        $this->_oObj->save();
        $this->setRequestParameter('oxcid', $this->_oObj->getId());

        $oContent = oxNew('content');
        $oContent->getParsedContent();

        $this->_oObj->oxcontents__oxcontent = new oxField("[{* *}]regenerated", oxField::T_RAW);
        $this->_oObj->save();

        $oContent = oxNew('content');
        $this->assertEquals('regenerated', $oContent->getParsedContent());
    }

    /**
     * Test get canonical url with seo on.
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $contentMock = $this->getMock("oxcontent", array("getBaseSeoLink", "getBaseStdLink"));
        $contentMock->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $contentMock->expects($this->never())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));

        $contentView = $this->getMock("Content", array("getContent"));
        $contentView->expects($this->once())->method('getContent')->will($this->returnValue($contentMock));

        $this->assertEquals("testSeoUrl", $contentView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $contentMock = $this->getMock("oxcontent", array("getBaseSeoLink", "getBaseStdLink"));
        $contentMock->expects($this->never())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $contentMock->expects($this->once())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));

        $contentView = $this->getMock("Content", array("getContent"));
        $contentView->expects($this->once())->method('getContent')->will($this->returnValue($contentMock));

        $this->assertEquals("testStdUrl", $contentView->getCanonicalUrl());
    }

    /**
     * Test get cannonical url for no content.
     */
    public function testGetCanonicalUrlNoContent()
    {
        $contentView = $this->getMock('Content', array('getContent'));
        $contentView->expects($this->once())->method('getContent')->will($this->returnValue(null));
        $this->assertSame('', $contentView->getCanonicalUrl());
    }
}
