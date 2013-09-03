<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: oxutilsxml.php 56456 13.8.7 13.15Z tadas.rimkus $
 */

/**
 * XML document handler
 */
class oxUtilsXml extends oxSuperCfg
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
    public function loadXml( $sXml, $oDomDocument = null )
    {
        if ( !$oDomDocument ) {
            $oDomDocument = new DOMDocument('1.0', 'utf-8');
        }

        libxml_use_internal_errors( true );
        $oDomDocument->loadXML( $sXml );
        $errors = libxml_get_errors();
        $blLoaded = empty( $errors );
        libxml_clear_errors();

        if ( $blLoaded ) {
            return $oDomDocument;
        }
        return false;
    }

}