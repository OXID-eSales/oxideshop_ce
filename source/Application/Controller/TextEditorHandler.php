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

namespace OxidEsales\EshopCommunity\Application\Controller;

/**
 * Responsible for generation of text editor output.
 *
 * Class TextEditorHandler
 */
class TextEditorHandler
{
    /**
     * @var string The style sheet for the editor.
     */
    private $stylesheet = null;

    /**
     * Render text editor.
     *
     * @param int    $width       The editor width.
     * @param int    $height      The editor height.
     * @param object $objectValue The object value passed to editor.
     * @param string $fieldName   The name of object field which content is passed to editor.
     *
     * @return string The Editor output.
     */
    public function renderTextEditor($width, $height, $objectValue, $fieldName)
    {
        $sEditorHtml = $this->renderRichTextEditor($width, $height, $objectValue, $fieldName);
        if (!$sEditorHtml) {
            $sEditorHtml = $this->renderPlainTextEditor($width, $height, $objectValue, $fieldName);
        }
        return $sEditorHtml;
    }

    /**
     * Returns simple textarea element filled with object text to edit.
     *
     * @param int    $width       The editor width.
     * @param int    $height      The editor height.
     * @param object $objectValue The object value passed to editor.
     * @param string $fieldName   The name of object field which content is passed to editor.
     *
     * @return string The Editor output.
     */
    public function renderPlainTextEditor($width, $height, $objectValue, $fieldName)
    {
        if (strpos($width, '%') === false) {
            $width .= 'px';
        }
        if (strpos($height, '%') === false) {
            $height .= 'px';
        }

        return "<textarea id='editor_{$fieldName}' style='width:{$width}; height:{$height};'>{$objectValue}</textarea>";
    }

    /**
     * Returns the generated output of wysiwyg editor.
     *
     * @param int    $width       The editor width.
     * @param int    $height      The editor height.
     * @param object $objectValue The object value passed to editor.
     * @param string $fieldName   The name of object field which content is passed to editor.
     *
     * @return string The Editor output.
     */
    public function renderRichTextEditor($width, $height, $objectValue, $fieldName)
    {
        return '';
    }

    /**
     * Set the style sheet for the editor.
     *
     * @param string $stylesheet The stylesheet for editor.
     */
    public function setStyleSheet($stylesheet)
    {
        $this->stylesheet = $stylesheet;
    }

    /**
     * Get the style sheet for the editor.
     *
     * @return string The stylesheet for the editor.
     */
    public function getStyleSheet()
    {
        return $this->stylesheet;
    }
}
