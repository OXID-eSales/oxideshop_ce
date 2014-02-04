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
 * Class handeling csv import
 *
 */
class oxErpGenImport extends oxErpCsv
{

    /**
     * Import objects types
     *
     * @var array
     */
    protected $_aObjects = array    (
                                'A' => 'article',
                                'K' => 'category',
                                'H' => 'vendor',
                                'C' => 'crossselling',
                                'Z' => 'accessoire',
                                'T' => 'article2category',
                                'I' => 'article2action',
                                'P' => 'scaleprice',
                                'U' => 'user',
                                'O' => 'order',
                                'R' => 'orderarticle',
                                'N' => 'country',
                                'Y' => 'artextends',
                            );
    /**
     * CSV file fields array.
     *
     * @var array
     */
    protected $_aCsvFileFieldsOrder = array();

    /**
     * CSV file contains header or not.
     *
     * @var bool
     */
    protected $_blCsvContainsHeader = null;

    /**
     * Csv file field terminator
     * @var string
     */
    protected $_sDefaultStringTerminator = ";";

    /**
     * Csv file field encloser
     * @var string
     */
    protected $_sDefaultStringEncloser = '"';

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call( $sMethod, $aArgs )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( substr( $sMethod, 0, 4) == "UNIT" ) {
                $sMethod = str_replace( "UNIT", "_", $sMethod );
            }
            if ( method_exists( $this, $sMethod)) {
                return call_user_func_array( array( & $this, $sMethod ), $aArgs );
            }
        }

        throw new oxSystemComponentException( "Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ")".PHP_EOL);
    }

    /**
     * Class constructor.
     *
     * @return null
     */
    public function __construct()
    {
        $this->_setDbLayerVersion();
    }

    /**
     * Get instance of type
     *
     * @param string $sType instance type
     *
     * @throws Exeption type not supported
     *
     * @return object
     */
    public function getInstanceOfType( $sType )
    {
        return parent::_getInstanceOfType( $sType );
    }

    /**
     * Setting DB layer version to latest
     *
     * @return null
     */
    protected function _setDbLayerVersion()
    {
        $aVersions = array_keys(oxErpBase::$_aDbLayer2ShopDbVersions);
        $sVersion = array_pop( $aVersions) ;
        oxErpBase::setVersion( $sVersion );
    }

    /**
     * Modyfies data befor import. Calls method for object fields
     * and csv data mapping.
     *
     * @param array  $aData CSV data
     * @param object $oType object type
     *
     * @return array
     */
    protected function _modifyData($aData, $oType)
    {
        return $this->_mapFields($aData, $oType);
    }

    /**
     * Maps numeric array to assoc. Array
     *
     * @param array  $aData numeric indices
     * @param object $oType object type
     *
     * @return array assoc. indices
     */
    protected function _mapFields($aData, $oType)
    {
        $aRet = array();
        $iIndex = 0;

        foreach ( $this->_aCsvFileFieldsOrder as $sValue ) {
            if ( !empty($sValue) ) {
                if ( strtolower( $aData[$iIndex] ) == "null" ) {
                    $aRet[$sValue] = null;
                } else {
                    $aRet[$sValue] = $aData[$iIndex];
                }
            }
            $iIndex++;
        }

        return $aRet;
    }

    /**
     * Gets import object type according type prefix
     *
     * @param array &$aData CSV data
     *
     * @throws Exeption if no such import type prefix
     *
     * @return string
     */
    protected function _getImportType( & $aData )
    {
        $sType = $this->_sImportTypePrefix;

        if ( strlen($sType) != 1 || !array_key_exists($sType, $this->_aObjects) ) {
            throw new Exception("Error unknown command: ".$sType);
        } else {
            return $this->_aObjects[$sType];
        }
    }

    /**
     * Gets import mode
     *
     * @param array $aData CSV data
     *
     * @return string
     */
    protected function _getImportMode( $aData )
    {
        return oxERPBase::$MODE_IMPORT;
    }

    /**
     * Get imort object according import type
     *
     * @param string $sType import object type
     *
     * @return object
     */
    public function getImportObject( $sType )
    {
        $this->_sImportTypePrefix = $sType;
        try {
            $sImportType = $this->_getImportType( $this->_aData );
            return $this->_getInstanceOfType( $sImportType );
        } catch( Exception $e) {
        }
    }

    /**
     * Set import object type prefix
     *
     * @param string $sType import type prefix
     *
     * @return null
     */
    public function setImportTypePrefix( $sType )
    {
        $this->_sImportTypePrefix = $sType;
    }

    /**
     * Get allowed for import objects list
     *
     * @return array
     */
    public function getImportObjectsList()
    {
        foreach ( $this->_aObjects as $sKey => $sImportType ) {
            $oType = $this->_getInstanceOfType( $sImportType );
            $aList[$sKey] = $oType->getBaseTableName();
        }
        return $aList;
    }

    /**
     * Init ERP Framework parameters
     * Creates Objects, checks Rights etc.
     *
     * @param mixed   $sUserName user name
     * @param mixed   $sPassword password
     * @param integer $iShopID   shop ID
     * @param integer $iLanguage language ID
     *
     * @return boolean
     */
    public function init( $sUserName, $sPassword, $iShopID = 1, $iLanguage = 0)
    {
        $myConfig = oxRegistry::getConfig();
        $mySession = oxRegistry::getSession();
        $oUser = oxNew('oxUser');
        $oUser->loadAdminUser();

        if ( ( $oUser->oxuser__oxrights->value == "malladmin" || $oUser->oxuser__oxrights->value == $myConfig->getShopID()) ) {
            $this->_sSID        = $mySession->getId();
            $this->_blInit      = true;
            $this->_iLanguage   = oxRegistry::getLang()->getBaseLanguage();
            $this->_sUserID     = $oUser->getId();
            //$mySession->freeze();
        } else {

            //user does not have sufficient rights for shop
            throw new Exception( self::ERROR_USER_NO_RIGHTS);
        }

        $this->_resetIdx();

        return $this->_blInit;
    }

    /**
     * Set CSV file comumns names
     *
     * @param array $aCsvFields CSV fields
     *
     * @return null
     */
    public function setCsvFileFieldsOrder( $aCsvFields )
    {
        $this->_aCsvFileFieldsOrder = $aCsvFields;
    }

    /**
     * Set if CSV file contains header row
     *
     * @param bool $blCsvContainsHeader has or not file header
     *
     * @return null
     */
    public function setCsvContainsHeader( $blCsvContainsHeader )
    {
        $this->_blCsvContainsHeader = $blCsvContainsHeader;
    }

    /**
     * Get successfully imported rows number
     *
     * @return int
     */
    public function getTotalImportedRowsNumber()
    {
        return $this->getImportedRowCount();
    }

    /**
     * Main import method, whole import of all types via a given csv file is done here
     *
     * @param string  $sPath         full path of the CSV file.
     * @param string  $sUserName     user name
     * @param string  $sUserPassword password
     * @param integer $sShopId       shop ID
     * @param integer $sShopLanguage language ID
     *
     * @return string
     *
     */
    public function doImport($sPath = null, $sUserName = null, $sUserPassword = null, $sShopId = null, $sShopLanguage = null )
    {
        $myConfig = oxRegistry::getConfig();
        $mySession = oxRegistry::getSession();

        $this->_sReturn = "";
        $iMaxLineLength = 8192; //TODO change

        $this->_sPath = $sPath;

        //init with given data
        try {
            $this->init(null, null);
        }catch(Exception $ex){
            return $this->_sReturn = 'ERPGENIMPORT_ERROR_USER_NO_RIGHTS';
        }

        $file = @fopen($this->_sPath, "r");

        if ( isset($file) && $file ) {
            $iRow = 0;
            $aRow = array();

            while ( ($aRow = fgetcsv( $file, $iMaxLineLength, $this->_getCsvFieldsTerminator(), $this->_getCsvFieldsEncolser()) ) !== false ) {

                $this->_aData[] = $aRow;
            }

            if ( $this->_blCsvContainsHeader ) {
                //skipping first row - it's header
                array_shift( $this->_aData );
            }

            try {
                $this->Import();
            } catch (Exception $ex) {
                echo $ex->getMessage();
                $this->_sReturn = 'ERPGENIMPORT_ERROR_DURING_IMPORT';
            }

        } else {
            $this->_sReturn = 'ERPGENIMPORT_ERROR_WRONG_FILE';
        }

        @fclose($file);

        return $this->_sReturn;
    }

    /**
     * Set csv field terminator symbol
     *
     * @return string
     */
    protected function _getCsvFieldsTerminator()
    {
        $myConfig = oxRegistry::getConfig();

        $sChar = $myConfig->getConfigParam( 'sGiCsvFieldTerminator' );

        if ( !$sChar ) {
            $sChar = $myConfig->getConfigParam( 'sCSVSign' );
        }
        if ( !$sChar ) {
            $sChar = $this->_sDefaultStringTerminator;
        }
        return $sChar;
    }

    /**
     * Get csv field encloser symbol
     *
     * @return string
     */
    protected function _getCsvFieldsEncolser()
    {
        $myConfig = oxRegistry::getConfig();

        if ( $sChar = $myConfig->getConfigParam( 'sGiCsvFieldEncloser' ) ) {
            return $sChar;
        } else {
            return $this->_sDefaultStringEncloser;
        }
    }
}
