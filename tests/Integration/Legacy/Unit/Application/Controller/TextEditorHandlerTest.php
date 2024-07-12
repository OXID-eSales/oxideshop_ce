<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

/**
 * Testing TextEditorHandler class.
 */
class TextEditorHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test renderTextEditor: return plain text editor output, if rich text editor is not set.
     */
    public function testRenderTextEditorNoRichTextEditor()
    {
        $expEditorHtml = "<textarea id='editor_sField' name='sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>";

        $textEditorHandler = $this->getMock(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class, ['renderRichTextEditor']);
        $textEditorHandler->method('renderRichTextEditor')->willReturn('');

        $editorHtml = $textEditorHandler->renderTextEditor(100, 100, 'sEditObjectValue', 'sField');
        $this->assertSame($expEditorHtml, $editorHtml);
    }

    /**
     * Test renderTextEditor: return rich text editor output, if it is set.
     */
    public function testRenderTextEditorIfRichTextEditorIsSet()
    {
        $expEditorHtml = "Rich Text Editor Output";

        $textEditorHandler = $this->getMock(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class, ['renderRichTextEditor']);
        $textEditorHandler->method('renderRichTextEditor')->willReturn($expEditorHtml);

        $editorHtml = $textEditorHandler->renderTextEditor(100, 100, 'sEditObjectValue', 'sField');
        $this->assertSame($expEditorHtml, $editorHtml);
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
        $this->assertSame($expectedEditorHtml, $editorHtml);
    }

    /**
     * Data provider for the testRenderPlainTextEditor
     *
     * @return array
     */
    public function renderPlainTextEditorDataProvider(): \Iterator
    {
        yield [100, 100, "<textarea id='editor_sField' name='sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>"];
        yield ['100%', '100%', "<textarea id='editor_sField' name='sField' style='width:100%; height:100%;'>sEditObjectValue</textarea>"];
        yield [100, '100%', "<textarea id='editor_sField' name='sField' style='width:100px; height:100%;'>sEditObjectValue</textarea>"];
        yield ['100%', 100, "<textarea id='editor_sField' name='sField' style='width:100%; height:100px;'>sEditObjectValue</textarea>"];
    }

    /**
     * Test setter and getter of stylesheet.
     */
    public function testSetGetStyleSheet()
    {
        $expCssFile = "style.css";

        $textEditorHandler = oxNew(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class);
        $textEditorHandler->setStyleSheet($expCssFile);

        $this->assertSame($expCssFile, $textEditorHandler->getStyleSheet());
    }

    /**
     * Test, that by default the text editor is not disabled.
     */
    #[\PHPUnit\Framework\Attributes\Group('texteditordisabled')]
    public function testIsTextEditorDisabledIsFalseOnDefault()
    {
        $textEditorHandler = oxNew(\OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::class);

        $this->assertFalse($textEditorHandler->isTextEditorDisabled());

        return $textEditorHandler;
    }

    /**
     * Test, that switching the text editor to disabled works.
     */
    #[\PHPUnit\Framework\Attributes\Group('texteditordisabled')]
    public function testDisableTextEditorLeadsToRightResult()
    {
        $textEditorHandler = $this->testIsTextEditorDisabledIsFalseOnDefault();

        $textEditorHandler->disableTextEditor();

        $this->assertTrue($textEditorHandler->isTextEditorDisabled());
    }
}
