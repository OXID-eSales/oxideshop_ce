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
        $expEditorHtml = "<textarea id='editor_sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>";

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
            array(100, 100, "<textarea id='editor_sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>"),
            array('100%', '100%', "<textarea id='editor_sField' style='width:100%; height:100%;'>sEditObjectValue</textarea>"),
            array(100, '100%', "<textarea id='editor_sField' style='width:100px; height:100%;'>sEditObjectValue</textarea>"),
            array('100%', 100, "<textarea id='editor_sField' style='width:100%; height:100px;'>sEditObjectValue</textarea>"),
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

}
