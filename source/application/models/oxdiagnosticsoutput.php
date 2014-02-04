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
 * Diagnostic tool result outputer
 * Performs OutputKey check of shop files and generates report file.
 *
 * @package model
 */

class oxDiagnosticsOutput {

    /**
     * result key
     *
     * @var string
     */
    protected $_sOutputKey             = "diagnostic_tool_result";


    /**
     * Result file path
     *
     * @var string
     */
    protected $_sOutputFileName        = "diagnostic_tool_result.html";

    /**
     * Utils object
     *
     * @var mixed
     */
    protected $_oUtils = null;

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_oUtils = oxRegistry::getUtils();
    }

    /**
     * OutputKey setter
     *
     * @param $sOutputKey string
     */
    public function setOutputKey( $sOutputKey )
    {
        if ( !empty( $sOutputKey ) ) {
            $this->_sOutputKey = $sOutputKey;
        }
    }

    /**
     * OutputKey getter
     *
     * @return string
     */
    public function getOutputKey()
    {
        return $this->_sOutputKey;
    }

    /**
     * OutputFileName setter
     *
     * @param $sOutputFileName string
     */
    public function setOutputFileName( $sOutputFileName )
    {
        if ( !empty( $sOutputFileName ) ) {
            $this->_sOutputFileName = $sOutputFileName;
        }
    }

    /**
     * OutputKey getter
     *
     * @return string
     */
    public function getOutputFileName()
    {
        return $this->_sOutputFileName;
    }

    /**
     * Stores result file in file cache
     *
     * @param $sResult
     */
    public function storeResult( $sResult )
    {
        $this->_oUtils->toFileCache( $this->_sOutputKey, $sBody . $sResult );
    }

    /**
     * Reads exported result file contents
     *
     * @return string
     */
    public function readResultFile( $sOutputKey = null )
    {
        $sCurrentKey = ( empty($sOutputKey) ) ?  $this->_sOutputKey : $sOutputKey;

        return $this->_oUtils->fromFileCache( $sCurrentKey );
    }

    /**
     * Sends generated file for download
     *
     * @return null
     */
    public function downloadResultFile( $sOutputKey = null )
    {
        $sCurrentKey = ( empty($sOutputKey) ) ?  $this->_sOutputKey : $sOutputKey;

        $this->_oUtils = oxRegistry::getUtils();
        $iFileSize = filesize( $this->_oUtils->getCacheFilePath( $sCurrentKey ) );

        $this->_oUtils->setHeader( "Pragma: public" );
        $this->_oUtils->setHeader( "Expires: 0" );
        $this->_oUtils->setHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0, private" );
        $this->_oUtils->setHeader('Content-Disposition: attachment;filename=' . $this->_sOutputFileName );
        $this->_oUtils->setHeader( "Content-Type: application/octet-stream" );
        if ( $iFileSize) {
            $this->_oUtils->setHeader( "Content-Length: " . $iFileSize );
        }
        echo $this->_oUtils->fromFileCache( $sCurrentKey );
    }
}