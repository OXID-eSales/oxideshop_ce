<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Application\Model\PaymentList;
use OxidEsales\EshopCommunity\Application\Model\DeliverySetList;
use OxidEsales\EshopCommunity\Application\Model\Delivery;
use OxidEsales\EshopCommunity\Application\Model\DeliveryList;
use \oxUtilsView;
use \oxField;
use \Exception;
use \oxcontent;
use \stdClass;
use \oxDb;
use \oxTestModules;

class ContentTest extends \PHPUnit\Framework\TestCase
{
    /** @var oxContent  */
    protected $_oObj;

    protected function setUp(): void
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

    protected function tearDown(): void
    {
        $this->_oObj->delete();
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxdel2delset');
        parent::tearDown();
    }

    /**
     * Content::_canShowContent() test case
     *
     * @return unknown_type
     */
    public function testCanShowContent()
    {
        $oView = $this->getMock(ContentController::class, ["getUser", "isEnabledPrivateSales"], [], '', false);
        $oView->method('getUser')->willReturn(false);
        $oView->method('isEnabledPrivateSales')->willReturn(true);

        $this->assertTrue($oView->canShowContent("oxagb"));
        $this->assertTrue($oView->canShowContent("oxrightofwithdrawal"));
        $this->assertTrue($oView->canShowContent("oximpressum"));
        $this->assertFalse($oView->canShowContent("testcontentident"));

        $oView = $this->getMock(ContentController::class, ["getUser", "isEnabledPrivateSales"], [], '', false);
        $oView->method('getUser')->willReturn(false);
        $oView->method('isEnabledPrivateSales')->willReturn(false);
        $this->assertTrue($oView->canShowContent("testcontentident"));
    }

    /**
     * Test active content id getter when content id passed with tpl param.
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
     */
    public function testGetSeoObjectId()
    {
        $this->setRequestParameter('oxcid', 'testseoobjectid');

        $oContentView = oxNew('content');
        $this->assertSame('testseoobjectid', $oContentView->getSeoObjectId());
    }

    /**
     * Test get view id.
     */
    public function testGetViewId()
    {
        $this->setRequestParameter('oxcid', 'testparam');

        $oView = oxNew('oxubase');
        $oContentView = oxNew('content');
        $this->assertSame($oView->getViewId() . '|testparam', $oContentView->getViewId());
    }

    public function testRender(): void
    {
        $oContent = oxNew('oxContent');
        $oContent->setId('testContent');

        $oContent->oxcontents__oxloadid= new Field('testContent');

        $oContentView = $this->getMock(
            ContentController::class,
            ['canShowContent', 'getContent', 'showPlainTemplate', 'getTplName']
        );
        $oContentView->expects($this->atLeastOnce())->method('getTplName')->willReturn(false);
        $oContentView->expects($this->atLeastOnce())->method('canShowContent')->willReturn(true);
        $oContentView->expects($this->atLeastOnce())->method('getContent')->willReturn($oContent);
        $oContentView->expects($this->atLeastOnce())->method('showPlainTemplate')->willReturn('true');
        $this->assertSame('page/info/content_plain', $oContentView->render());
    }

    /**
     * Test active content id getter
     */
    public function testRenderPsOn()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("redirect"); }');

        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        try {
            // testing..
            $oView = $this->getMock(ContentController::class, ["canShowContent"], [], '', false);
            $oView->expects($this->once())->method('canShowContent')->willReturn(false);
            $oView->render();
        } catch (Exception $exception) {
            $this->assertSame("redirect", $exception->getMessage(), "Error in oxsclogincontent::getContentId()");

            return;
        }

        $this->fail("Error in content::getContentId()");
    }

    /**
     * Test prepare meta keywords.
     */
    public function testPrepareMetaKeyword()
    {
        $oContent = oxNew('oxArticle');
        $oContent->oxcontents__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oContent->oxcontents__oxtitle->expects($this->once())->method('__get')->willReturn('testtitle');

        $oContentView = $this->getMock(ContentController::class, ['getContent']);
        $oContentView->expects($this->once())->method('getContent')->willReturn($oContent);

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->prepareMetaKeyword('testtitle'), $oContentView->prepareMetaKeyword(null));
    }

    /**
     * Test prepare meta description.
     */
    public function testPrepareMetaDescription()
    {
        $oContent = oxNew('oxArticle');
        $oContent->oxcontents__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oContent->oxcontents__oxtitle->expects($this->once())->method('__get')->willReturn('testtitle');

        $oContentView = $this->getMock(ContentController::class, ['getContent']);
        $oContentView->expects($this->once())->method('getContent')->willReturn($oContent);

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->prepareMetaDescription('testtitle'), $oContentView->prepareMetaDescription(null));
    }

    /**
     * Test get content category without any assigned category.
     */
    public function testGetContentCategoryNoCategoryAssigned()
    {
        $oView = $this->getMock(ContentController::class, ['getContent']);
        $oView->expects($this->once())->method('getContent')->willReturn(new oxcontent());

        $this->assertFalse($oView->getContentCategory());
    }

    /**
     * Test get content category with assigned category.
     */
    public function testGetContentCategorySomeCategoryAssigned()
    {
        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxtype = new oxfield(2);

        $oView = $this->getMock(ContentController::class, ['getContent']);
        $oView->expects($this->once())->method('getContent')->willReturn($oContent);

        $this->assertEquals($oContent, $oView->getContentCategory());
    }

    /**
     * Test show plain template.
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

        $oView = $this->getMock(ContentController::class, ['getUser']);
        $oView->method('getUser')->willReturn(false);
        $this->assertTrue($oView->showPlainTemplate());

        $this->setRequestParameter('plain', 0);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['isTermsAccepted']);
        $oUser->method('isTermsAccepted')->willReturn(true);

        $oView = $this->getMock(ContentController::class, ['getUser']);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertFalse($oView->showPlainTemplate());
    }

    /**
     * Test active content id getter Test active content id getter when content id passed with oxcid param.
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
     */
    public function testGetTplName()
    {
        $this->setRequestParameter('tpl', 'test');
        $oObj = $this->getProxyClass("content");
        $this->assertSame('message/test', $oObj->getTplName());
    }

    /**
     * Test if render returns setted template name.
     */
    public function testRenderReturnSettedTemplateName()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $this->getConfig()->setConfigParam("blPsLoginEnabled", false);

        $oView = $this->getMock(ContentController::class, ['getTplName']);
        $oView->expects($this->once())->method('getTplName')->willReturn('test');

        $this->assertSame('test', $oView->render());
    }

    public function testContentNotFound()
    {
        $this->setRequestParameter('oxcid', null);
        $this->setRequestParameter('oxloadid', null);
        $oView = $this->getMock(ContentController::class, ['getTplName', 'getContentId']);
        $oView->expects($this->once())->method('getTplName')->willReturn('');
        $oView->method('getContentId')->willReturn(false);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['handlePageNotFoundError']);
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->willThrowException(new Exception("404"));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        try {
            $oView->render();
        } catch (Exception $exception) {
            $this->assertSame('404', $exception->getMessage());

            return;
        }

        $this->fail("no exception");
    }

    /**
     * Testing Contact::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oContent = oxNew('Content');

        $this->assertCount(1, $oContent->getBreadCrumb());
    }

    /**
     * Test get content title.
     */
    public function testGetTitle()
    {
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $oContent = oxNew('content');
        $this->assertSame('test', $oContent->getTitle());
    }

    /**
     * Content::showRdfa() Test case
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
     */
    public function testGetContentPageTpl_Business()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaBusinessEntityLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sBusinessTemplate');
        $this->assertNotEmpty($sExpResp);

        $oView = $this->getMock(ContentController::class, ['getContent']);
        $oView->expects($this->once())->method('getContent')->willReturn($oContent);
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getContentPageTpl() Test Delivery case
     */
    public function testGetContentPageTpl_Delivery()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaDeliveryChargeSpecLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sDeliveryTemplate');
        $this->assertNotEmpty($sExpResp);

        $oView = $this->getMock(ContentController::class, ['getContent']);
        $oView->expects($this->once())->method('getContent')->willReturn($oContent);
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getContentPageTpl() Test Payment case
     */
    public function testGetContentPageTpl_Payment()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('sRDFaPaymentChargeSpecLoc', '_test_value');

        $oContent = new stdClass();
        $oContent->oxcontents__oxloadid = new oxField('_test_value');

        $oViewProxy = $this->getProxyClass('Content');
        $sExpResp = $oViewProxy->getNonPublicVar('_sPaymentTemplate');
        $this->assertNotEmpty($sExpResp);

        $oView = $this->getMock(ContentController::class, ['getContent']);
        $oView->expects($this->once())->method('getContent')->willReturn($oContent);
        $aTpl = $oView->getContentPageTpl();
        $this->assertSame($sExpResp, $aTpl[0]);
    }

    /**
     * Content::getBusinessEntityExtends() Test case
     */
    public function testGetBusinessEntityExtends()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('_test1', '_value1');
        $oConf->setConfigParam('_test2', '_value2');

        $aInput = ['_test2', '_test1', '_testX'];
        $aExpResp = ['_test2' => '_value2', '_test1' => '_value1', '_testX' => null];

        $oViewProxy = $this->getProxyClass('Content');
        $oViewProxy->setNonPublicVar('_aBusinessEntityExtends', $aInput);
        $this->assertSame($aExpResp, $oViewProxy->getBusinessEntityExtends());
    }

    /**
     * Content::getNotMappedToRDFaPayments() Test case
     */
    public function testGetNotMappedToRDFaPayments()
    {
        oxTestModules::addFunction('oxPaymentList', 'getNonRDFaPaymentList', "{ return '_call_getNonRDFaPaymentList'; }");

        $oView = oxNew('Content');
        $oResp = $oView->getNotMappedToRDFaPayments();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\PaymentList::class, $oResp);
    }

    /**
     * Content::getNotMappedToRDFaDeliverySets() Test case
     */
    public function testGetNotMappedToRDFaDeliverySets()
    {
        oxTestModules::addFunction('oxdeliverysetlist', 'getNonRDFaDeliverySetList', "{ return '_call_getNonRDFaDeliverySetList'; }");

        $oView = oxNew('Content');
        $oResp = $oView->getNotMappedToRDFaDeliverySets();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliverySetList::class, $oResp);
    }

    /**
     * Content::getDeliveryChargeSpecs() Test case
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

        $this->assertCount(5, $oResp);
        foreach ($oResp as $oDelivery) {
            $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Delivery::class, $oDelivery);
            $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliverySetList::class, $oDelivery->deliverysetmethods);
        }
    }

    /**
     * Content::getDeliveryList() Test case
     */
    public function testGetDeliveryList()
    {
        oxTestModules::addFunction('oxdeliverylist', 'getList', "{ return '_call_getList'; }");

        $oView = oxNew('Content');
        $oResp = $oView->getDeliveryList();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\DeliveryList::class, $oResp);
    }

    /**
     * Content::getRdfaVAT() Test case
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
        $this->assertSame(['validfrom', 'validthrough'], array_keys($aResp));

        $startTime = time() - (2 * 60 * 60);
        $endTime = $startTime + (3 * 24 * 60 * 60);

        $this->assertLessThanOrEqual($aResp['validfrom'], date('Y-m-d\TH:i:s', $startTime));
        $this->assertLessThanOrEqual($aResp['validthrough'], date('Y-m-d\TH:i:s', $endTime));
    }

    /**
     * Content::testGetParsedContent() Test case
     *
     * Add bugfix to #0004298: If there is smarty tag in content, then it is saved in same name template.
     */
    public function testGetParsedContent()
    {
        $this->markTestSkipped('refactor not to parse');
        $this->_oObj->oxcontents__oxcontent = new oxField("[{ 'A'|cat:'B' }]SSSSSSSS", oxField::T_RAW);
        $this->_oObj->save();
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $oContent = oxNew('content');

        $this->assertSame('ABSSSSSSSS', $oContent->getParsedContent(), 'Result from smarty not same as in content page.');

        // Check if second CMS page will be generated with different content.
        $oSecond = oxNew('oxcontent');
        $oSecond->setId('_test_testGetParsedContent');

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

        $this->assertSame('ADSSSSSSSS', $oContent->getParsedContent(), 'Content not as in second page. If result ABSSSSSSSS than it is ame as in first page, so used wrong smarty cache file.');
    }

    /**
     * Test get canonical url with seo on.
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $contentMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, ["getBaseSeoLink", "getBaseStdLink"]);
        $contentMock->expects($this->once())->method('getBaseSeoLink')->willReturn("testSeoUrl");
        $contentMock->expects($this->never())->method('getBaseStdLink')->willReturn("testStdUrl");

        $contentView = $this->getMock(ContentController::class, ["getContent"]);
        $contentView->expects($this->once())->method('getContent')->willReturn($contentMock);

        $this->assertSame("testSeoUrl", $contentView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $contentMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, ["getBaseSeoLink", "getBaseStdLink"]);
        $contentMock->expects($this->never())->method('getBaseSeoLink')->willReturn("testSeoUrl");
        $contentMock->expects($this->once())->method('getBaseStdLink')->willReturn("testStdUrl");

        $contentView = $this->getMock(ContentController::class, ["getContent"]);
        $contentView->expects($this->once())->method('getContent')->willReturn($contentMock);

        $this->assertSame("testStdUrl", $contentView->getCanonicalUrl());
    }

    /**
     * Test get cannonical url for no content.
     */
    public function testGetCanonicalUrlNoContent()
    {
        $contentView = $this->getMock(ContentController::class, ['getContent']);
        $contentView->expects($this->once())->method('getContent')->willReturn(null);
        $this->assertSame('', $contentView->getCanonicalUrl());
    }
}
