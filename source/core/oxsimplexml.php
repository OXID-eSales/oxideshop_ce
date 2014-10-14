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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * The wrapper of simpleXML functions.
 */
class oxSimpleXml
{
    /**
     * Recursively adds $oInput object data to SimpleXMLElement structure
     *
     * @param SimpleXMLElement    $oXml   Xml handler
     * @param string|array|object $oInput Input object
     *
     * @return SimpleXMLElement
     */
    protected function _addSimpleXmlElement($oXml, $oInput)
    {
        if (is_object( $oInput )) {
            $aObjectVars = get_object_vars( $oInput );
        } elseif (is_array($oInput) ) {
            $aObjectVars = $oInput;
        } else {
            return $oXml;
        }

        foreach ($aObjectVars as $sKey => $oVar) {
            if (is_object( $oVar ) ) {
                $oChildNode = $oXml->addChild($sKey);
                $this->_addSimpleXmlElement($oChildNode, $oVar);
            } elseif (is_array( $oVar) ) {
                foreach ($oVar as $oSubValue) {
                    //this check for complex type is probably redundant, but I give up to solve it over single recursion call
                    //use existing Unit tests for refactoring
                    if (is_array($oSubValue) || is_object($oSubValue)) {
                        $oChildNode = $oXml->addChild($sKey);
                        $this->_addSimpleXmlElement($oChildNode, $oSubValue);
                    } else {
                        $oXml->addChild($sKey, $oSubValue);
                    }
                }
            } else {
                //assume $oVar is string
                $oXml->addChild($sKey, $oVar);
            }
        }

        return $oXml;
    }

    /**
     * Parses object structure to XML string
     *
     * Example object:
     * oxStdClass Object
     *   (
     *       [title] => TestTitle
     *       [keys] => oxStdClass Object
     *           (
     *               [key] => Array
     *                   (
     *                       [0] => testKey1
     *                       [1] => testKey2
     *                   )
     *           )
     *   )
     *
     * would produce the following XML:
     * <?xml version="1.0" encoding="utf-8"?>
     * <testXml><title>TestTitle</title><keys><key>testKey1</key><key>testKey2</key></keys></testXml>
     *
     * @param  object $oInput    Input object
     * @param  string $sDocument Document name.
     *
     * @return string
     */
    public function objectToXml($oInput, $sDocument)
    {
        $oXml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><$sDocument/>");
        $this->_addSimpleXmlElement($oXml, $oInput);

        return $oXml->asXml();
    }

    /**
     * Parses XML string into object structure
     *
     * @param string $sXml XML Input
     *
     * @return SimpleXMLElement
     */
    public function xmlToObject($sXml)
    {
        return simplexml_load_string($sXml);
    }
}