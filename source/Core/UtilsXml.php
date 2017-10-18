<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use DOMDocument;

/**
 * XML document handler
 */
class UtilsXml extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Takes XML string and makes DOMDocument
     * Returns DOMDocument or false, if it can't be loaded
     *
     * @param string      $sXml         XML as a string
     * @param DOMDocument $oDomDocument DOM handler
     *
     * @return DOMDocument|bool
     */
    public function loadXml($sXml, $oDomDocument = null)
    {
        if (!$oDomDocument) {
            $oDomDocument = new DOMDocument('1.0', 'utf-8');
        }

        libxml_use_internal_errors(true);
        $oDomDocument->loadXML($sXml);
        $errors = libxml_get_errors();
        $blLoaded = empty($errors);
        libxml_clear_errors();

        if ($blLoaded) {
            return $oDomDocument;
        }

        return false;
    }
}
