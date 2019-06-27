<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
     * @var bool Information in the text editor is editable by default.
     *           In some cases it should not be etc. when product is derived.
     */
    protected $textEditorDisabled = false;

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

        $disabledTextEditor = $this->isTextEditorDisabled() ? 'disabled ' : '';

        return "<textarea ${disabledTextEditor}id='editor_{$fieldName}' name='$fieldName' " .
               "style='width:{$width}; height:{$height};'>{$objectValue}</textarea>";
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

    /**
     * Mark text editor disabled: information in it should not be editable.
     */
    public function disableTextEditor()
    {
        $this->textEditorDisabled = true;
    }

    /**
     * If information in text editor is not editable.
     *
     * @return bool
     */
    public function isTextEditorDisabled()
    {
        return $this->textEditorDisabled;
    }
}
