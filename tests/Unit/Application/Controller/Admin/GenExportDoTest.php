<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineBridgeInterface;

/**
 * Tests for GenExport_Do class
 */
class GenExportDoTest extends \OxidTestCase
{
    /**
     * GenExport_Do::NextTick() test case
     *
     * @return null
     */
    public function testNextTickNoMoreArticleFound()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo::class, array("getOneArticle", "write"));
        $oView->expects($this->once())->method('getOneArticle')->will($this->returnValue(false));
        $oView->expects($this->never())->method('write');
        $this->assertFalse($oView->nextTick(1));
    }

    /**
     * GenExport_Do::NextTick() test case
     *
     * @return null
     */
    public function testNextTick()
    {
        $article = oxNew('oxArticle');
        $parameters = [
            "sCustomHeader" => '',
            "linenr" => 1,
            "article" => $article,
            "spr" => $this->getConfigParam('sCSVSign'),
            "encl" => $this->getConfigParam('sGiCsvFieldEncloser')
        ];
        $templateEngine = $this->getMockBuilder(TemplateEngineBridgeInterface::class)
            ->setMethods(['renderTemplate', 'exists', 'getEngine'])
            ->disableOriginalConstructor()
            ->getMock();
        $templateEngine->expects($this->any())->method('renderTemplate')->with(
            $this->equalTo('genexport.tpl'),
            $this->equalTo($parameters),
            $this->equalTo('dyn_interface')
            );

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo::class, array("getOneArticle", "write", "getViewId", "getTemplating"));
        $oView->expects($this->once())->method('getOneArticle')->will($this->returnValue($article));
        $oView->expects($this->once())->method('write');
        $oView->expects($this->once())->method('getViewId')->will($this->returnValue('dyn_interface'));
        $oView->expects($this->any())->method('getTemplating')->will($this->returnValue($templateEngine));

        $this->assertEquals(2, $oView->nextTick(1));
    }

    /**
     * GenExport_Do::Write() test case
     *
     * @return null
     */
    public function testWrite()
    {
        $sLine = 'TestExport';
        $testFile = $this->createFile('test.txt', '');

        $oView = oxNew('GenExport_Do');
        $oView->fpFile = @fopen($testFile, "w");
        $oView->write($sLine);
        fclose($oView->fpFile);
        $sFileCont = file_get_contents($testFile, true);
        $this->assertEquals($sLine . "\n", $sFileCont);
    }

    /**
     * GenExport_Do::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenExport_Do');
        $this->assertEquals('dynbase_do.tpl', $oView->render());
    }
}
