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
 * Admin general export manager.
 * @package admin
 */
class GenImport_Main extends oxAdminDetails
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo   = "genImport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "genImport_main";

    /**
     * Csv file path
     *
     * @var string
     */
    protected $_sCsvFilePath = null;

    /**
     * Csv file field terminator
     * @var string
     */
    protected $_sStringTerminator = null;

    /**
     * Csv file field encloser
     * @var string
     */
    protected $_sStringEncloser = null;

    /**
     * Default Csv file field terminator
     * @var string
     */
    protected $_sDefaultStringTerminator = ";";

    /**
     * Default Csv file field encloser
     * @var string
     */
    protected $_sDefaultStringEncloser = '"';

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = "genimport_main.tpl";

    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file
     *
     * @return string
     */
    public function render()
    {
        $oConfig = $this->getConfig();

        $oErpImport = new oxErpGenImport();
        $this->_sCsvFilePath = null;

        $sNavStep = $oConfig->getParameter( 'sNavStep' );

        if ( !$sNavStep ) {
            $sNavStep = 1;
        } else {
            $sNavStep++;
        }


        $sNavStep = $this->_checkErrors( $sNavStep );

        if ( $sNavStep == 1 ) {
            $this->_aViewData['sGiCsvFieldTerminator'] = htmlentities( $this->_getCsvFieldsTerminator() );
            $this->_aViewData['sGiCsvFieldEncloser']   = htmlentities( $this->_getCsvFieldsEncolser() );
        }

        if ( $sNavStep == 2 ) {
            //saving csv field terminator and encloser to config
            if ( $sTerminator = $oConfig->getParameter( 'sGiCsvFieldTerminator' ) ) {
                $this->_sStringTerminator = $sTerminator;
                $oConfig->saveShopConfVar( 'str', 'sGiCsvFieldTerminator', $sTerminator );
            }

            if ( $sEncloser = $oConfig->getParameter( 'sGiCsvFieldEncloser' ) ) {
                $this->_sStringEncloser = $sEncloser;
                $oConfig->saveShopConfVar( 'str', 'sGiCsvFieldEncloser', $sEncloser );
            }

            $sType = $oConfig->getParameter( 'sType' );
            $oType = $oErpImport->getImportObject( $sType );
            $this->_aViewData['sType'] = $sType;
            $this->_aViewData['sImportTable'] =  $oType->getBaseTableName();
            $this->_aViewData['aCsvFieldsList'] = $this->_getCsvFieldsNames();
            $this->_aViewData['aDbFieldsList'] = $oType->getFieldList();
        }

        if ( $sNavStep == 3 ) {
            $aCsvFields = $oConfig->getParameter( 'aCsvFields' );
            $sType = $oConfig->getParameter( 'sType' );

            $oErpImport = new oxErpGenImport();
            $oErpImport->setImportTypePrefix( $sType );
            $oErpImport->setCsvFileFieldsOrder( $aCsvFields );
            $oErpImport->setCsvContainsHeader( oxSession::getVar( 'blCsvContainsHeader' ) );

            $oErpImport->doImport( $this->_getUploadedCsvFilePath() );
            $this->_aViewData['iTotalRows'] = $oErpImport->getTotalImportedRowsNumber();

            //checking if errors occured during import
            $this->_checkImportErrors( $oErpImport );

            //deleting uploaded csv file from temp dir
            $this->_deleteCsvFile();

            //check if repeating import - then forsing first step
            if ( $oConfig->getParameter( 'iRepeatImport' ) ) {
                $this->_aViewData['iRepeatImport'] = 1;
                $sNavStep = 1;
            }
        }

        if ( $sNavStep == 1 ) {
            $this->_aViewData['aImportTables'] = $oErpImport->getImportObjectsList();
            asort( $this->_aViewData['aImportTables'] );
            $this->_resetUploadedCsvData();
        }

        $this->_aViewData['sNavStep'] = $sNavStep;

        return parent::render();
    }

    /**
     * Deletes uploaded csv file from temp directory
     *
     * @return null
     *
     */
    protected function _deleteCsvFile()
    {
        $sPath = $this->_getUploadedCsvFilePath();
        if ( is_file( $sPath ) ) {
           @unlink( $sPath );
        }
    }

    /**
     * Get columns names from CSV file header. If file has no header
     * returns default columns names Column 1, Column 2..
     *
     * @return array
     */
    protected function _getCsvFieldsNames()
    {
        $blCsvContainsHeader = $this->getConfig()->getParameter( 'blContainsHeader' );
        oxSession::setVar( 'blCsvContainsHeader', $blCsvContainsHeader );
        $sCsvPath = $this->_getUploadedCsvFilePath();

        $aFirstRow = $this->_getCsvFirstRow();

        if ( !$blCsvContainsHeader ) {
            $iIndex = 1;
            foreach ( $aFirstRow as $sValue ) {
                $aCsvFields[$iIndex] = 'Column ' . $iIndex++;
            }
        } else {
            foreach ( $aFirstRow as $sKey => $sValue ) {
                $aFirstRow[$sKey] = htmlentities( $sValue );
            }

            $aCsvFields = $aFirstRow;
        }

        return $aCsvFields;
    }

    /**
     * Get first row from uploaded CSV file
     *
     * @return array
     */
    protected function _getCsvFirstRow()
    {
        $sPath = $this->_getUploadedCsvFilePath();
        $iMaxLineLength = 8192;

        //getting first row
        if ( ( $rFile = @fopen( $sPath, "r" ) ) !== false ) {
            $aRow = fgetcsv( $rFile, $iMaxLineLength, $this->_getCsvFieldsTerminator(), $this->_getCsvFieldsEncolser() );
            fclose( $rFile );
        }

        return $aRow;
    }

    /**
     * Resets CSV parameters stored in session
     *
     *  @return null
     */
    protected function _resetUploadedCsvData()
    {
        $this->_sCsvFilePath = null;
        oxSession::setVar( 'sCsvFilePath', null );
        oxSession::setVar( 'blCsvContainsHeader', null );
    }

    /**
     * Checks current import navigation step errors.
     * Returns step id in which error occured.
     *
     * @param int $iNavStep Navigation step id
     *
     * @return int
     */
    protected function _checkErrors( $iNavStep )
    {
        if ( $iNavStep == 2 ) {
            if ( !$this->_getUploadedCsvFilePath() ) {
                $oEx = oxNew( "oxExceptionToDisplay" );
                $oEx->setMessage( 'GENIMPORT_ERRORUPLOADINGFILE' );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true, 'genimport' );
                $iNavStep = 1;
            }
        }

        if ( $iNavStep == 3 ) {
            $blIsEmpty = true;
            $aCsvFields = $this->getConfig()->getParameter( 'aCsvFields' );
            foreach ( $aCsvFields as $sValue ) {
                if ( $sValue ) {
                   $blIsEmpty = false;
                   break;
                }
            }

            if ( $blIsEmpty ) {
                $oEx = oxNew( "oxExceptionToDisplay" );
                $oEx->setMessage( 'GENIMPORT_ERRORASSIGNINGFIELDS' );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true, 'genimport' );
                $iNavStep = 2;
            }
        }

        return $iNavStep;
    }

    /**
     * Checks if CSV file was uploaded. If uploaded - moves it to temp dir
     * and stores path to file in session. Return path to uploaded file.
     *
     * @return string
     */
    protected function _getUploadedCsvFilePath()
    {
        //try to get uploaded csv file path
        if ( $this->_sCsvFilePath !== null ) {
            return $this->_sCsvFilePath;
        } elseif ( $this->_sCsvFilePath = oxSession::getVar( 'sCsvFilePath' ) ) {
            return $this->_sCsvFilePath;
        }

        $oConfig = $this->getConfig();
        $aFile = $oConfig->getUploadedFile( 'csvfile' );
        if ( isset( $aFile['name'] ) && $aFile['name'] ) {
            $this->_sCsvFilePath = $oConfig->getConfigParam( 'sCompileDir' ) . basename( $aFile['tmp_name'] );
            move_uploaded_file( $aFile['tmp_name'], $this->_sCsvFilePath );
            oxSession::setVar( 'sCsvFilePath', $this->_sCsvFilePath );
            return $this->_sCsvFilePath;
        }
    }

    /**
     * Checks if any error occured during import and displays them
     *
     * @param object $oErpImport Import object
     *
     * @return null
     */
    protected function _checkImportErrors( $oErpImport )
    {
        foreach ( $oErpImport->getStatistics() as $aValue ) {
            if ( !$aValue ['r'] ) {
                $oEx = oxNew( "oxExceptionToDisplay" );
                $oEx->setMessage( $aValue ['m'] );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true, 'genimport' );
            }
        }

    }

    /**
     * Get csv field terminator symbol
     *
     * @return string
     */
    protected function _getCsvFieldsTerminator()
    {
        if ( $this->_sStringTerminator === null ) {
            $this->_sStringTerminator = $this->_sDefaultStringTerminator;
            if ( $sChar = $this->getConfig()->getConfigParam( 'sGiCsvFieldTerminator' ) ) {
                $this->_sStringTerminator = $sChar;
            }
        }
        return $this->_sStringTerminator;
    }

    /**
     * Get csv field encloser symbol
     *
     * @return string
     */
    protected function _getCsvFieldsEncolser()
    {
        if ( $this->_sStringEncloser === null ) {
            $this->_sStringEncloser = $this->_sDefaultStringEncloser;
            if ( $sChar = $this->getConfig()->getConfigParam( 'sGiCsvFieldEncloser' ) ) {
                $this->_sStringEncloser = $sChar;
            }
        }
        return $this->_sStringEncloser;
    }
}
