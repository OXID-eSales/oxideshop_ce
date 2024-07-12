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
class ContentSeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown(): void
    {
        $sQ = "delete from oxcontents where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Content_Seo::GetType() test case
     *
     * @return null
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertEquals('oxcontent', $oView->getType());
    }

    /**
     * Content_Seo::getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Content_Seo');
        $this->assertTrue($oView->getEncoder() instanceof SeoEncoderContent);
    }

    /**
     * Content_Seo::getEntryUri() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, ["getContentUri"]);
        $oEncoder->expects($this->once())->method('getContentUri')->will($this->returnValue("ContentUri"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentSeo::class, ["getEditObjectId", "getEncoder"]);
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->once())->method('getEncoder')->will($this->returnValue($oEncoder));
        $this->assertEquals("ContentUri", $oView->getEntryUri());
    }

    /**
     * Content_Seo::getEncoder() test case
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ContentSeo::class, ["getEditLang"]);
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals($oContent->getBaseStdLink(0, true, false), $oView->getStdUrl("_test1"));
    }

    /**
     * Content_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertEquals('object_seo', $oView->render());
    }
}
