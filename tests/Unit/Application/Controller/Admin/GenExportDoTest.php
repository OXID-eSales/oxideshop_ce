<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

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
            "encl" => $this->getConfigParam('sGiCsvFieldEncloser'),
            'oxEngineTemplateId' => 'dyn_interface'
        ];
        $renderer = $this->getMockBuilder(TemplateRendererInterface::class)
            ->setMethods(['renderTemplate', 'renderFragment', 'getTemplateEngine', 'exists'])
            ->disableOriginalConstructor()
            ->getMock();
        $renderer->expects($this->any())->method('renderTemplate')->with(
            $this->equalTo('genexport.tpl'),
            $this->equalTo($parameters)
        );

        $bridge = $this->getMockBuilder(TemplateRendererBridgeInterface::class)
            ->setMethods(['setEngine', 'getEngine', 'getTemplateRenderer'])
            ->disableOriginalConstructor()
            ->getMock();
        $bridge->expects($this->any())->method('getTemplateRenderer')->will($this->returnValue($renderer));

        $container = $this->getContainerMock('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface', $bridge);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo::class, array("getOneArticle", "write", "getViewId", "getContainer"));
        $oView->expects($this->once())->method('getOneArticle')->will($this->returnValue($article));
        $oView->expects($this->once())->method('write');
        $oView->expects($this->once())->method('getViewId')->will($this->returnValue('dyn_interface'));
        $oView->expects($this->any())->method('getContainer')->will($this->returnValue($container));

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

    /**
     * Check that render method returns expected template name.
     * Could be useful as an integrational test to test that template from controller is set to template engine
     *
     * @param $expectedTemplate
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getContainerMock($serviceName, $serviceMock)
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get', 'has'])
            ->getMock();
        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo($serviceName))
            ->will($this->returnValue($serviceMock));

        return $container;
    }
}
