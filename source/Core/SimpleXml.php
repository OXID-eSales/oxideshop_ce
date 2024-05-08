<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use SimpleXMLElement;

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
class SimpleXml
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
        $this->addSimpleXmlElement($oXml, $oInput);

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
    protected function addSimpleXmlElement($oXml, $oInput, $sPreferredKey = null)
    {
        $aElements = is_object($oInput) ? get_object_vars($oInput) : (array) $oInput;

        foreach ($aElements as $sKey => $mElement) {
            $oXml = $this->addChildNode($oXml, $sKey, $mElement, $sPreferredKey);
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
    protected function addChildNode($oXml, $sKey, $mElement, $sPreferredKey = null)
    {
        $aAttributes = [];
        if (is_array($mElement) && array_key_exists('attributes', $mElement) && is_array($mElement['attributes'])) {
            $aAttributes = $mElement['attributes'];
            $mElement = $mElement['value'];
        }

        if (is_object($mElement) || is_array($mElement)) {
            if (is_array($mElement) && is_int(key($mElement))) {
                $this->addSimpleXmlElement($oXml, $mElement, $sKey);
            } else {
                $oChildNode = $oXml->addChild($sPreferredKey ? $sPreferredKey : $sKey);
                $this->addNodeAttributes($oChildNode, $aAttributes);
                $this->addSimpleXmlElement($oChildNode, $mElement);
            }
        } else {
            $oChildNode = $oXml->addChild($sPreferredKey ? $sPreferredKey : $sKey);
            $oChildNode[0] = $mElement; // $oChildNode[0] is the inner text-node
            $this->addNodeAttributes($oChildNode, $aAttributes);
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
    protected function addNodeAttributes($oNode, $aAttributes)
    {
        $aAttributes = (array) $aAttributes;
        foreach ($aAttributes as $sKey => $sValue) {
            $oNode->addAttribute($sKey, $sValue);
        }

        return $oNode;
    }
}
