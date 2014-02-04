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
 * General export class.
 * @package admin
 */
class VoucherSerie_Export extends VoucherSerie_Main
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "voucherserie_export";

    /**
     * Export file extension
     *
     * @var string
     */
    public $sExportFileType = "csv";

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = "voucherserie_export.tpl";

    /**
     * Number of records to export per tick
     *
     * @var int
     */
    public $iExportPerTick = 1000;

    /**
     * Calls parent costructor and initializes $this->_sFilePath parameter
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        // export file name
        $this->sExportFileName = $this->_getExportFileName();

        // set generic frame template
        $this->_sFilePath = $this->_getExportFilePath();
    }

    /**
     * Returns export file download url
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        $myConfig = $this->getConfig();

        // override cause of admin dir
        $sUrl = $myConfig->getConfigParam( 'sShopURL' ). $myConfig->getConfigParam( 'sAdminDir' );
        if ( $myConfig->getConfigParam( 'sAdminSSLURL' ) ) {
            $sUrl = $myConfig->getConfigParam( 'sAdminSSLURL' );
        }

        $sUrl = oxRegistry::get("oxUtilsUrl")->processUrl( $sUrl.'/index.php' );
        return $sUrl . '&amp;cl='.$this->sClassDo.'&amp;fnc=download';
    }

    /**
     * Return export file name
     *
     * @return string
     */
    protected function _getExportFileName()
    {
        $sSessionFileName = oxSession::getVar( "sExportFileName" );
        if ( !$sSessionFileName ) {
            $sSessionFileName = md5( $this->getSession()->getId() . oxUtilsObject::getInstance()->generateUId() );
            oxSession::setVar( "sExportFileName", $sSessionFileName );
        }
        return $sSessionFileName;
    }

    /**
     * Return export file path
     *
     * @return string
     */
    protected function _getExportFilePath()
    {
        return $this->getConfig()->getConfigParam( 'sShopDir' ) . "/export/". $this->_getExportFileName();
    }

    /**
     * Performs Voucherserie export to export file.
     *
     * @return null
     */
    public function download()
    {
        $oUtils = oxRegistry::getUtils();
        $oUtils->setHeader( "Pragma: public" );
        $oUtils->setHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        $oUtils->setHeader( "Expires: 0" );
        $oUtils->setHeader( "Content-Disposition: attachment; filename=vouchers.csv");
        $oUtils->setHeader( "Content-Type: application/csv" );
        $sFile = $this->_getExportFilePath();
        if ( file_exists( $sFile ) && is_readable( $sFile ) ) {
            readfile( $sFile );
        }
        $oUtils->showMessageAndExit( "" );
    }

    /**
     * Does Export
     *
     * @return null
     */
    public function run()
    {
        $blContinue = true;
        $iExportedItems = 0;

        $this->fpFile = @fopen( $this->_sFilePath, "a");
        if ( !isset( $this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop( ERR_FILEIO);
        } else {
            // file is open
            $iStart = oxConfig::getParameter("iStart");
            if (!$iStart) {
                ftruncate($this->fpFile, 0);
            }

            if ( ( $iExportedItems = $this->exportVouchers( $iStart ) ) === false ) {
                // end reached
                $this->stop( ERR_SUCCESS );
                $blContinue = false;
            }

            if ( $blContinue ) {
                // make ticker continue
                $this->_aViewData['refresh']   = 0;
                $this->_aViewData['iStart']    = $iStart + $iExportedItems;
                $this->_aViewData['iExpItems'] = $iStart + $iExportedItems;
            }
            fclose( $this->fpFile);
        }
    }

    /**
     * Writes voucher number information to export file and returns number of written records info
     *
     * @param int $iStart start exporting from
     *
     * @return int
     */
    public function exportVouchers( $iStart )
    {
        $iExported = false;

        if ( $oSerie = $this->_getVoucherSerie() ) {

            $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

            $sSelect = "select oxvouchernr from oxvouchers where oxvoucherserieid = ".$oDb->quote( $oSerie->getId() );
            $rs = $oDb->selectLimit( $sSelect, $this->iExportPerTick, $iStart );

            if ( !$rs->EOF ) {
                $iExported = 0;

                // writing header text
                if ( $iStart == 0 ) {
                    $this->write( oxRegistry::getLang()->translateString("VOUCHERSERIE_MAIN_VOUCHERSTATISTICS", oxRegistry::getLang()->getTplLanguage(), true ));
                }
            }

            // writing vouchers..
            while ( !$rs->EOF ) {
                $this->write( current( $rs->fields ) );
                $iExported++;
                $rs->moveNext();
            }
        }

        return $iExported;
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     *
     * @return null
     */
    public function write( $sLine )
    {
        if ( $sLine ) {
           fwrite( $this->fpFile, $sLine."\n");
        }
    }
}
