<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\SeoEncoderContent;
use \oxDb;

/**
 * Tests for Content_Seo class
 */
class ContentSeoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $sQ = "delete from oxcontents where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Content_Seo::GetType() test case
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertSame('oxcontent', $oView->getType());
    }

    /**
     * Content_Seo::getEncoder() test case
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Content_Seo');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\SeoEncoderContent::class, $oView->getEncoder());
    }

    /**
     * Content_Seo::getEntryUri() test case
     */
    public function testGetEntryUri()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, ["getContentUri"]);
        $oEncoder->expects($this->once())->method('getContentUri')->willReturn("ContentUri");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentSeo::class, ["getEditObjectId", "getEncoder"]);
        $oView->expects($this->once())->method('getEditObjectId')->willReturn("_test1");
        $oView->expects($this->once())->method('getEncoder')->willReturn($oEncoder);
        $this->assertSame("ContentUri", $oView->getEntryUri());
    }

    /**
     * Content_Seo::getEncoder() test case
     */
    public function testGetStdUrl()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentSeo::class, ["getEditLang"]);
        $oView->expects($this->once())->method('getEditLang')->willReturn(0);

        $this->assertEquals($oContent->getBaseStdLink(0, true, false), $oView->getStdUrl("_test1"));
    }

    /**
     * Content_Seo::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertSame('object_seo', $oView->render());
    }
}
