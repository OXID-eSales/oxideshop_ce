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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Parses objects to XML and XML to simple XML objects.
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
 */
class oxSimpleXml
{

    /**
     * Parses object structure to XML string
     *
     * @param object $oInput    Input object
     * @param string $sDocument Document name.
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

    /**
     * Recursively adds $oInput object data to SimpleXMLElement structure
     *
     * @param SimpleXMLElement    $oXml          Xml handler
     * @param string|array|object $oInput        Input object
     * @param string              $sPreferredKey Key to use instead of node's key.
     *
     * @return SimpleXMLElement
     */
    protected function _addSimpleXmlElement($oXml, $oInput, $sPreferredKey = null)
    {
        $aElements = is_object($oInput) ? get_object_vars($oInput) : (array) $oInput;

        foreach ($aElements as $sKey => $mElement) {
            $oXml = $this->_addChildNode($oXml, $sKey, $mElement, $sPreferredKey);
        }

        return $oXml;
    }

    /**
     * Adds child node to given simple xml object.
     *
     * @param SimpleXMLElement    $oXml
     * @param string              $sKey
     * @param string|array|object $mElement
     * @param string              $sPreferredKey
     *
     * @return SimpleXMLElement
     */
    protected function _addChildNode($oXml, $sKey, $mElement, $sPreferredKey = null)
    {
        $aAttributes = array();
        if (is_array($mElement) && array_key_exists('attributes', $mElement) && is_array($mElement['attributes'])) {
            $aAttributes = $mElement['attributes'];
            $mElement = $mElement['value'];
        }

        if (is_object($mElement) || is_array($mElement)) {
            if (is_int(key($mElement))) {
                $this->_addSimpleXmlElement($oXml, $mElement, $sKey);
            } else {
                $oChildNode = $oXml->addChild($sPreferredKey? $sPreferredKey : $sKey);
                $this->_addNodeAttributes($oChildNode, $aAttributes);
                $this->_addSimpleXmlElement($oChildNode, $mElement);
            }
        } else {
            $oChildNode = $oXml->addChild($sPreferredKey? $sPreferredKey : $sKey, $mElement);
            $this->_addNodeAttributes($oChildNode, $aAttributes);
        }

        return $oXml;
    }

    /**
     * Adds attributes to given node.
     *
     * @param SimpleXMLElement $oNode
     * @param array            $aAttributes
     *
     * @return SimpleXMLElement
     */
    protected function _addNodeAttributes($oNode, $aAttributes)
    {
        $aAttributes = (array) $aAttributes;
        foreach ($aAttributes as $sKey => $sValue) {
            $oNode->addAttribute($sKey, $sValue);
        }

        return $oNode;
    }
}
