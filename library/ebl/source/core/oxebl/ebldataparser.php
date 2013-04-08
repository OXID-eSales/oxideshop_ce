<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   EBL
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

class EBLDataParser
{
    /**
     * Described all parameters parsing rules.
     *
     * Array keys:   parameter name
     * Array values: array(
     *          'type' => ['String' | 'Array' | 'AArray' | 'CustomFunctionName'],
     *          'xpath' => 'XML xPath',
     *      );
     *
     * @var Array
     */
    protected $_aParseRules = array();

    /**
     * Simple XML object.
     * @var SimpleXMLElement
     */
    protected $_oXML;

    /**
     * Get current XML object
     *
     * @return SimpleXMLElement
     */
    protected function _getXMLObject()
    {
        return $this->_oXML;
    }

    /**
     * Set current XML object
     *
     * @param SimpleXMLElement $oXML
     *
     * @return null
     */
    protected function _setXMLObject(SimpleXMLElement $oXML)
    {
        $this->_oXML = $oXML;
    }

    /**
     * Get string from specified xpath.
     *
     * @param string $sXPath
     *
     * @return string
     */
    protected function _parseString($sXPath)
    {
        $aElems = $this->_getXMLObject()->xpath($sXPath);
        if (!is_array($aElems) || count($aElems) < 1) {
            return;
        }

        return (string)$aElems[0];
    }

    /**
     * Get numeric array from specified xpath.
     *
     * @param string $sXPath
     *
     * @return array
     */
    protected function _parseArray($sXPath)
    {
        $aElems = $this->_getXMLObject()->xpath($sXPath);
        if (!is_array($aElems) || count($aElems) < 1) {
            return;
        }

        $aParams = array();
        foreach ($aElems[0] as $oElem) {
            if ($oElem->children()) {
                continue;
            }

            $aParams[] = (string)$oElem;
        }
        return $aParams;
    }

    /**
     * Get assoc array from specified xpath.
     *
     * @param string $sXPath
     *
     * @return array
     */
    protected function _parseAArray($sXPath)
    {
        $aElems = $this->_getXMLObject()->xpath($sXPath);
        if (!is_array($aElems) || count($aElems) < 1) {
            return;
        }

        $aParams = array();
        foreach ($aElems[0] as $oElem) {
            if ($oElem->children()) {
                continue;
            }

            $aParams[$oElem->getName()] = (string)$oElem;
        }
        return $aParams;
    }

    /**
     * Generic function for all parsers.
     *
     * @param array $aParamRule
     *
     * @throws EBLException
     *
     * @return mixed
     */
    protected function _getParameter($aParamRule)
    {
        if (!is_array($aParamRule)) {
            throw new EBLException('Invalid parameter: should be array.');
        }

        if (!isset($aParamRule['type']) || !isset($aParamRule['xpath'])) {
            throw new EBLException('Invalid parameter: should be set array fields [type, xpath].');
        }

        $sParseMethod = '_parse'.$aParamRule['type'];
        if (!method_exists($this, $sParseMethod)) {
            throw new EBLException('Unknown parse method: '.$sParseMethod);
        }

        return $this->$sParseMethod($aParamRule['xpath']);
    }

    /**
     * Clear parser rules list.
     *
     * @return boolean
     */
    public function clearParseRules()
    {
        $this->_aParseRules = array();
        return true;
    }

    /**
     * Delete parser rule.
     *
     * @param string $sParamName
     *
     * @return boolean
     */
    public function delParseRule($sParamName)
    {
        if (isset($this->_aParseRules[$sParamName])) {
            unset($this->_aParseRules[$sParamName]);
            return true;
        }

        return false;
    }

    /**
     * Set parser rule.
     *
     * @param string $sParamName
     * @param string $sType
     * @param string $sXPath
     *
     * @return boolean
     */
    public function setParseRule($sParamName, $sType, $sXPath)
    {
        $aParseRule = array(
            'type'  => $sType,
            'xpath' => $sXPath,
        );
        $this->_aParseRules[$sParamName] = $aParseRule;

        return true;
    }

    /**
     * Get parser rule.
     *
     * @param string $sParamName
     *
     * @return mixed
     */
    public function getParseRule($sParamName)
    {
        if (isset($this->_aParseRules[$sParamName])) {
            return $this->_aParseRules[$sParamName];
        }

        return false;
    }

    /**
     * Parse all described params, and return associative params array.
     *
     * @param SimpleXMLElement $oXML
     *
     * @return array
     */
    public function getAsocArray(SimpleXMLElement $oXML)
    {
        $aParams = array();
        $this->_setXMLObject($oXML);

        foreach ($this->_aParseRules as $sParam => $aRule) {
            $aParams[$sParam] = $this->_getParameter($aRule);
        }

        return $aParams;
    }
}
