<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

/**
 * Testing TextEditorHandler class.
 */
class TextEditorHandlerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Test renderTextEditor: return plain text editor output, if rich text editor is not set.
     */
    public function testRenderTextEditorNoRichTextEditor()
    {
        $expEditorHtml = "<textarea id='editor_sField' name='sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>";

        $textEditorHandler = $this->getMock(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class, array('renderRichTextEditor'));
        $textEditorHandler->expects($this->any())->method('renderRichTextEditor')->will($this->returnValue(''));

        $editorHtml = $textEditorHandler->renderTextEditor(100, 100, 'sEditObjectValue', 'sField');
        $this->assertEquals($expEditorHtml, $editorHtml);
    }

    /**
     * Test renderTextEditor: return rich text editor output, if it is set.
     */
    public function testRenderTextEditorIfRichTextEditorIsSet()
    {
        $expEditorHtml = "Rich Text Editor Output";

        $textEditorHandler = $this->getMock(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class, array('renderRichTextEditor'));
        $textEditorHandler->expects($this->any())->method('renderRichTextEditor')->will($this->returnValue($expEditorHtml));

        $editorHtml = $textEditorHandler->renderTextEditor(100, 100, 'sEditObjectValue', 'sField');
        $this->assertEquals($expEditorHtml, $editorHtml);
    }

    /**
     * Test get plain editor.
     *
     * @param string $width              The width of the editor.
     * @param string $height             The height of the editor.
     * @param string $expectedEditorHtml The expected output of the editor.
     *
     * @dataProvider renderPlainTextEditorDataProvider
     */
    public function testRenderPlainTextEditor($width, $height, $expectedEditorHtml)
    {
        $textEditorHandler = oxNew(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class);
        $editorHtml = $textEditorHandler->renderPlainTextEditor($width, $height, 'sEditObjectValue', 'sField');
        $this->assertEquals($expectedEditorHtml, $editorHtml);
    }

    /**
     * Data provider for the testRenderPlainTextEditor
     *
     * @return array
     */
    public function renderPlainTextEditorDataProvider()
    {
        return array(
            array(100, 100, "<textarea id='editor_sField' name='sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>"),
            array('100%', '100%', "<textarea id='editor_sField' name='sField' style='width:100%; height:100%;'>sEditObjectValue</textarea>"),
            array(100, '100%', "<textarea id='editor_sField' name='sField' style='width:100px; height:100%;'>sEditObjectValue</textarea>"),
            array('100%', 100, "<textarea id='editor_sField' name='sField' style='width:100%; height:100px;'>sEditObjectValue</textarea>"),
        );
    }

    /**
     * Test setter and getter of stylesheet.
     */
    public function testSetGetStyleSheet()
    {
        $expCssFile = "style.css";

        $textEditorHandler = oxNew(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class);
        $textEditorHandler->setStyleSheet($expCssFile);

        $this->assertEquals($expCssFile, $textEditorHandler->getStyleSheet());
    }

    /**
     * Test, that by default the text editor is not disabled.
     *
     * @group texteditordisabled
     */
    public function testIsTextEditorDisabledIsFalseOnDefault()
    {
        $textEditorHandler = oxNew(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class);

        $this->assertFalse($textEditorHandler->isTextEditorDisabled());

        return $textEditorHandler;
    }

    /**
     * Test, that switching the text editor to disabled works.
     *
     * @group texteditordisabled
     */
    public function testDisableTextEditorLeadsToRightResult()
    {
        $textEditorHandler = $this->testIsTextEditorDisabledIsFalseOnDefault();

        $textEditorHandler->disableTextEditor();

        $this->assertTrue($textEditorHandler->isTextEditorDisabled());
    }
}
