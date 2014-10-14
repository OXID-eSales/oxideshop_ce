<?php

namespace Behat\SahiClient\Accessor;

use Behat\SahiClient\Connection;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class AbstractAccessor
{
    /**
     * Sahi Driver instance.
     *
     * @var     Driver
     */
    protected   $con;

    /**
     * Initialize Accessor.
     *
     * @param   Connection  $con    Sahi Connection
     */
    public function __construct(Connection $con)
    {
        $this->con = $con;
    }

    /**
     * Set Sahi Connection.
     *
     * @param   Connection  $con    Sahi Connection
     */
    public function setConnection(Connection $con)
    {
        $this->con = $con;
    }

    /**
     * Return Accessor active connection instance.
     *
     * @return  Connection
     */
    public function getConnection()
    {
        return $this->con;
    }

    /**
     * Perform check on radio
     */
    public function check()
    {
        $this->con->executeStep(sprintf('_sahi._check(%s)', $this->getAccessor()));
    }

    /**
     * Return true if checkbox/radio checked.
     *
     * @return  boolean
     */
    public function isChecked()
    {
        return "true" === $this->con->evaluateJavascript(sprintf('%s.checked', $this->getAccessor()));
    }

    /**
     * Perform uncheck on radio
     */
    public function uncheck()
    {
        $this->con->executeStep(sprintf('_sahi._uncheck(%s)', $this->getAccessor()));
    }

    /**
     * Return selected text from selectbox.
     *
     * @return  string
     */
    public function getSelectedText()
    {
        return $this->con->evaluateJavascript(sprintf('_sahi._getSelectedText(%s)', $this->getAccessor()));
    }

    /**
     * Choose option in select box.
     *
     * @param   string  $val    option value
     */
    public function choose($val, $isMultiple = null)
    {
        $arguments = array('"' . str_replace('"', '\"', $val) . '"');
        if (null !== $isMultiple) {
            $arguments[] = (bool) $isMultiple ? 'true' : 'false';
        }

        $this->con->executeStep(
            sprintf('_sahi._setSelected(%s, %s)', $this->getAccessor(), implode(', ', $arguments))
        );
    }

    /**
     * Emulate setting filepath in a file input.
     *
     * @param   string  $path   file path
     */
    public function setFile($path)
    {
        $this->con->executeStep(
            sprintf('_sahi._setFile(%s, "%s")', $this->getAccessor(), str_replace('"', '\"', $path))
        );
    }

    /**
     * Perform click on element.
     */
    public function click()
    {
        $this->con->executeStep(sprintf('_sahi._click(%s)', $this->getAccessor()));
    }

    /**
     * Perform right-click on element.
     */
    public function rightClick()
    {
        $this->con->executeStep(sprintf('_sahi._rightClick(%s)', $this->getAccessor()));
    }

    /**
     * Perform double-click on element.
     */
    public function doubleClick()
    {
        $this->con->executeStep(sprintf('_sahi._doubleClick(%s)', $this->getAccessor()));
    }

    /**
     * Perform mouse-over on element.
     */
    public function mouseOver()
    {
        $this->con->executeStep(sprintf('_sahi._mouseOver(%s)', $this->getAccessor()));
    }

    /**
     * Bring focus to element.
     */
    public function focus()
    {
        $this->con->executeStep(sprintf('_sahi._focus(%s)', $this->getAccessor()));
    }

    /**
     * Remove focus from element.
     */
    public function removeFocus()
    {
        $this->con->executeStep(sprintf('_sahi._removeFocus(%s)', $this->getAccessor()));
    }

    /**
     * Blur element.
     */
    public function blur()
    {
        $this->con->executeStep(sprintf('_sahi._blur(%s)', $this->getAccessor()));
    }

    /**
     * Drag'n'Drop current element onto another.
     *
     * @param   AbstractAccessor    $to destination element
     */
    public function dragDrop(AbstractAccessor $to)
    {
        $this->con->executeStep(sprintf('_sahi._dragDrop(%s, %s)', $this->getAccessor(), $to->getAccessor()));
    }

    /**
     * Drag'n'Drop current element into X,Y.
     *
     * @param   integer $x          X
     * @param   integer $y          Y
     * @param   boolean $relative   relativity of position
     */
    public function dragDropXY($x, $y, $relative = null)
    {
        $arguments = array($x, $y);

        if (null !== $relative) {
            $arguments[] = (bool) $relative ? 'true' : 'false';
        }

        $this->con->executeStep(
            sprintf('_sahi._dragDropXY(%s, %s)', $this->getAccessor(), implode(', ', $arguments))
        );
    }

    /**
     * Simulate event.
     *
     * @param   string  $event   notify event on object
     */
    public function simulateEvent($event)
    {
        $this->con->executeStep(sprintf('_sahi._simulateEvent(%s, %s)', $this->getAccessor(), $event));
    }

    /**
     * Simulate keypress event.
     *
     * @param   string  $charInfo   a char (eg. ‘b’) OR charCode (eg. 98) OR array(13,13) for pressing ENTER
     * @param   string  $combo      CTRL|ALT|SHIFT|META
     */
    public function keyPress($charInfo, $combo = null)
    {
        $this->con->executeStep(
            sprintf('_sahi._keyPress(%s, %s)', $this->getAccessor(), $this->getKeyArgumentsString($charInfo, $combo))
        );
    }

    /**
     * Simulate keypress event.
     *
     * @param   string  $charInfo   a char (eg. ‘b’) OR charCode (eg. 98) OR array(13,13) for pressing ENTER
     * @param   string  $combo      CTRL|ALT|SHIFT|META
     */
    public function keyDown($charInfo, $combo = null)
    {
        $this->con->executeStep(
            sprintf('_sahi._keyDown(%s, %s)', $this->getAccessor(), $this->getKeyArgumentsString($charInfo, $combo))
        );
    }

    /**
     * Simulate keypress event.
     *
     * @param   string  $charInfo   a char (eg. ‘b’) OR charCode (eg. 98) OR array(13,13) for pressing ENTER
     * @param   string  $combo      CTRL|ALT|SHIFT|META
     */
    public function keyUp($charInfo, $combo = null)
    {
        $this->con->executeStep(
            sprintf('_sahi._keyUp(%s, %s)', $this->getAccessor(), $this->getKeyArgumentsString($charInfo, $combo))
        );
    }

    /**
     * Set text value.
     *
     * @param   string  $val    value
     */
    public function setValue($val)
    {
        $this->con->executeStep(
            sprintf('_sahi._setValue(%s, "%s")', $this->getAccessor(), str_replace('"', '\"', $val))
        );
    }

    /**
     * Return current text value.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->con->evaluateJavascript(sprintf('%s.value', $this->getAccessor()));
    }

    /**
     * Return node name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->con->evaluateJavascript(sprintf('%s.nodeName', $this->getAccessor()));
    }

    /**
     * Return attribute value.
     *
     * @param   string  $attr   attribute name
     *
     * @return  string
     */
    public function getAttr($attr)
    {
        return $this->con->evaluateJavascript(sprintf('%s.getAttribute("%s")', $this->getAccessor(), $attr));
    }

    /**
     * Return inner text of element.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->con->evaluateJavascript(sprintf('_sahi._getText(%s)', $this->getAccessor()));
    }

    /**
     * Return inner text of element.
     *
     * @return  string
     */
    public function getHTML()
    {
        return $this->con->evaluateJavascript(sprintf('%s.innerHTML', $this->getAccessor()));
    }

    /**
     * Highlight element.
     */
    public function highlight()
    {
        $this->con->executeStep(sprintf('_sahi._highlight(%s)', $this->getAccessor()));
    }

    /**
     * Return true if the element is visible on the user interface.
     *
     * @return  boolean
     */
    public function isVisible()
    {
        return 'true' === $this->con->evaluateJavascript(sprintf('_sahi._isVisible(%s)', $this->getAccessor()));
    }

    /**
     * Return true if the element is visible on the user interface.
     *
     * @return  boolean
     */
    public function isExists()
    {
        return 'true' === $this->con->evaluateJavascript(sprintf('_sahi._exists(%s)', $this->getAccessor()));
    }

    /**
     * Return accessor string.
     *
     * @return  string
     */
    abstract public function getAccessor();

    /**
     * Return accessor string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getAccessor();
    }

    /**
     * Return Key arguments string.
     *
     * @param   string  $charInfo   a char (eg. ‘b’) OR charCode (eg. 98) OR array(13,13) for pressing ENTER
     * @param   string  $combo      CTRL|ALT|SHIFT|META
     *
     * @return  string
     */
    private function getKeyArgumentsString($charInfo, $combo)
    {
        $arguments = array();

        if (is_array($charInfo)) {
            $arguments[] = '[' . implode(',', $charInfo) . ']';
        } elseif (is_string($charInfo)) {
            $arguments[] = '"' . $charInfo . '"';
        } else {
            $arguments[] = $charInfo;
        }

        if (null !== $combo) {
            $arguments[] = '"' . $combo . '"';
        }

        return implode(', ', $arguments);
    }
}
