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
 * oxERPBase class, provides all basic functions, needed for ERP to function
 **/
abstract class oxERPBase
{

    const ERROR_USER_WRONG = "ERROR: Could not login";
    const ERROR_USER_NO_RIGHTS = "Not sufficient rights to perform operation!";
    const ERROR_USER_EXISTS = "ERROR: User already exists";
    const ERROR_NO_INIT = "Init not executed, Access denied!";
    const ERROR_DELETE_NO_EMPTY_CATEGORY = "Only empty category can be deleated";
    const ERROR_OBJECT_NOT_EXISTING = "Object does not exist";
    const ERROR_ERP_VERSION_NOT_SUPPORTED_BY_SHOP = "ERROR: shop does not support requested ERP version.";
    const ERROR_SHOP_VERSION_NOT_SUPPORTED_BY_ERP = "ERROR: ERP does not support current shop version.";

    public static $MODE_IMPORT = "Import";
    public static $MODE_DELETE = "Delete";

    protected $_blInit = false;
    protected $_iLanguage = null;
    protected $_sUserID = null;
    //session id
    protected $_sSID = null;

    protected static $_sRequestedVersion = '';

    /**
     * describes what db layer versions are implemented and usable with shop db version
     * 1st entry is default version (if none requested)
     *
     * note: shop db layer versions are integers, so <> operations can be used
     *
     * IMPORTANT: mainly these versions are used in objects, fallbacking to previous version
     * if requested is not defined, so it is required to increment version numbers by 1 and only
     * to one direction (newer version = bigger nr).
     *
     * @var array
     */
    protected static $_aDbLayer2ShopDbVersions = array(
        '2.9.0' => '8', // added new fields to oxcategories, oxorderarticle
    );

    /**
     * Imported id array
     *
     * @var array
     */
    protected $_aImportedIds = array();

    /**
     * Imported row count
     *
     * @var array
     */
    protected $_iImportedRowCount = 0;

    public $_aStatistics = array();
    public $_iIdx = 0;

    /** gets count of imported rows, total, during import
     *
     * @return int $_iImportedRowCount
     */
    abstract public function getImportedRowCount();

    /** adds true to $_aImportedIds where key is given
     *
     * @param mixed $key - given key
     *
     * @return null
     */
    abstract public function setImportedIds($key);

    /**
     * _aStatistics getter
     *
     * @return array
     */
    public function getStatistics()
    {
        return $this->_aStatistics;
    }

    /**
     * session id getter
     *
     * @return string
     */
    public function getSessionID()
    {
        return $this->_sSID;
    }

    /**
     * hook before export
     *
     * @param string $sType type of export
     *
     * @return null
     */
    abstract protected function _beforeExport($sType);

    /**
     * hook after export
     *
     * @param string $sType type of export
     *
     * @return null
     */
    abstract protected function _afterExport($sType);

    /**
     * hook before import
     *
     * @return null
     */
    abstract protected function _beforeImport();

    /**
     * hook after import
     *
     * @return null
     */
    abstract protected function _afterImport();

    /**
     * import data getter
     *
     * @param int $iIdx data index
     *
     * @return array
     */
    abstract public function getImportData($iIdx = null);

    /**
     * retrieve import type
     *
     * @param array &$aData data
     *
     * @return string
     */
    abstract protected function _getImportType(&$aData);

    /**
     * retrieve import mode
     *
     * @param array $aData data
     *
     * @return string
     */
    abstract protected function _getImportMode($aData);

    /**
     * prepare data for import
     *
     * @param array     $aData data
     * @param oxErpType $oType data type object
     *
     * @return array
     */
    abstract protected function _modifyData($aData, $oType);

    /**
     * default fallback if some handler is missing
     *
     * @param string $sMethod    method name
     * @param array  $aArguments arguments
     *
     * @throws Exception not implemented
     */
    public function __call($sMethod, $aArguments)
    {
        throw new Exception("ERROR: Handler for Object '$sMethod' not implemented!");
    }


    // -------------------------------------------------------------------------
    //
    // public interface
    //
    // -------------------------------------------------------------------------


    /**
     * Init ERP Framework
     * Creates Objects, checks Rights etc.
     *
     * @param string $sUserName user login name
     * @param string $sPassword user password
     * @param int    $iShopID   shop id to login
     * @param int    $iLanguage main language id
     *
     * @return boolean
     */
    public function init($sUserName, $sPassword, $iShopID = 1, $iLanguage = 0)
    {
        ini_set('session.use_cookies', 0);
        $_COOKIE = array('admin_sid' => false);
        $myConfig = oxRegistry::getConfig();
        $myConfig->setConfigParam('blForceSessionStart', 1);
        $myConfig->setConfigParam('blSessionUseCookies', 0);
        $myConfig->setConfigParam('blAdmin', 1);
        $myConfig->setAdminMode(true);

        $mySession = oxRegistry::getSession();
        @$mySession->start();


        oxRegistry::getSession()->setVariable("lang", $iLanguage);
        oxRegistry::getSession()->setVariable("language", $iLanguage);

        $oUser = oxNew('oxuser');
        try {
            if (!$oUser->login($sUserName, $sPassword)) {
                $oUser = null;
            }
        } catch (oxUserException $e) {
            $oUser = null;
        }

        self::_checkShopVersion();

        if (!$oUser || (isset($oUser->iError) && $oUser->iError == -1000)) {
            // authorization error
            throw new Exception(self::ERROR_USER_WRONG);
        } elseif (($oUser->oxuser__oxrights->value == "malladmin" || $oUser->oxuser__oxrights->value == $myConfig->getShopID())) {
            $this->_sSID = $mySession->getId();
            $this->_blInit = true;
            $this->_iLanguage = $iLanguage;
            $this->_sUserID = $oUser->getId();
            //$mySession->freeze();
        } else {

            //user does not have sufficient rights for shop
            throw new Exception(self::ERROR_USER_NO_RIGHTS);
        }

        $this->_resetIdx();

        return $this->_blInit;
    }

    /**
     * oxERPBase::loadSessionData()
     * load session - should be called on init
     *
     * @param string $sSessionID session id
     */
    public function loadSessionData($sSessionID)
    {
        if (!$sSessionID) {
            throw new Exception("ERROR: Session ID not valid!");
        }
        $_COOKIE = array('admin_sid' => $sSessionID);
        // start session
        $myConfig = oxRegistry::getConfig();
        $myConfig->setConfigParam('blAdmin', 1);
        $myConfig->setAdminMode(true);
        $mySession = oxRegistry::getSession();

        // change session if needed
        if ($sSessionID != session_id()) {
            if (session_id()) {
                session_write_close();
            }
            session_id($sSessionID);
            session_start();
        }

        $sAuth = $mySession->getVariable('auth');

        if (!isset($sAuth) || !$sAuth) {
            throw new Exception("ERROR: Session ID not valid!");
        }

        $this->_iLanguage = $mySession->getVariable('lang');
        $this->_sUserID = $sAuth;


        $this->_blInit = true;
    }

    /**
     * Export one object type
     *
     * @param string $sType          data type name in objects dir
     * @param string $sWhere         where filter for export
     * @param int    $iStart         limit start
     * @param int    $iCount         limit
     * @param string $sSortFieldName field name to sort by
     * @param string $sSortType      "asc" or "desc"
     */
    public function exportType($sType, $sWhere = null, $iStart = null, $iCount = null, $sSortFieldName = null, $sSortType = null)
    {
        $this->_beforeExport($sType);
        $this->_export($sType, $sWhere, $iStart, $iCount, $sSortFieldName, $sSortType);
        $this->_afterExport($sType);
    }

    /**
     * imports all data set up before
     */
    public function import()
    {
        $this->_beforeImport();
        while ($this->_importOne()) {
        }
        $this->_afterImport();
    }

    /**
     * Factory for ERP types
     *
     * @param string $sType type name in objects dir
     *
     * @return oxErpType
     */
    protected function _getInstanceOfType($sType)
    {
        $sClassName = 'oxerptype_' . $sType;
        $sFullPath = dirname(__FILE__) . '/objects/' . $sClassName . '.php';

        if (!file_exists($sFullPath)) {
            throw new Exception("Type $sType not supported in ERP interface!");
        }

        include_once $sFullPath;

        //return new $sClassName;
        return oxNew($sClassName);
    }

    /**
     * Exports one type
     * internal function, called after _beforeExport and before _afterExport methods
     *
     * @param string $sType          data type name in objects dir
     * @param string $sWhere         where filter for export
     * @param int    $iStart         limit start
     * @param int    $iCount         limit
     * @param string $sSortFieldName field name to sort by
     * @param string $sSortType      "asc" or "desc"
     */
    protected function _export($sType, $sWhere, $iStart = null, $iCount = null, $sSortFieldName = null, $sSortType = null)
    {
        global $ADODB_FETCH_MODE;

        $myConfig = oxRegistry::getConfig();
        // prepare
        $oType = $this->_getInstanceOfType($sType);
        //$sSQL    = $oType->getSQL($sWhere, $this->_iLanguage, $this->_iShopID);
        $sSQL = $oType->getSQL($sWhere, $this->_iLanguage, $myConfig->getShopId());
        $sSQL .= $oType->getSortString($sSortFieldName, $sSortType);
        $sFnc = '_Export' . $oType->getFunctionSuffix();

        $save = $ADODB_FETCH_MODE;

        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        if (isset($iCount) || isset($iStart)) {
            $rs = $oDb->selectLimit($sSQL, $iCount, $iStart);
        } else {
            $rs = $oDb->select($sSQL);
        }

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $blExport = false;
                $sMessage = '';

                $rs->fields = $oType->addExportData($rs->fields);

                // check rights
                $this->_checkAccess($oType, false);

                // export now
                try {
                    $blExport = $this->$sFnc($rs->fields);
                } catch (Exception $e) {
                    $sMessage = $e->getMessage();

                }

                $this->_aStatistics[$this->_iIdx] = array('r' => $blExport, 'm' => $sMessage);
                //#2428 MAFI
                $this->_nextIdx();

                $rs->moveNext();
            }
        }
        $ADODB_FETCH_MODE = $save;
    }

    /**
     * Just used for developing
     *
     * @param string $sTable table name
     */
    protected function _outputMappingArray($sTable)
    {
        $aData = GetTableDescription($sTable);

        $iIdx = 0;
        foreach ($aData as $key => $oADODBField) {
            if (!(is_numeric(substr($oADODBField->name, strlen($oADODBField->name) - 1, 1)) && substr($oADODBField->name, strlen($oADODBField->name) - 2, 1) == '_')) {
                echo("'" . $oADODBField->name . "'\t\t => '" . $oADODBField->name . "',\n");
                $iIdx++;
            }
        }
    }

    /**
     * return key id for data record
     *
     * @param oxErpType $oType data type object
     * @param array     $aData data
     *
     * @return string
     */
    protected function _getKeyID($oType, $aData)
    {
        $sOXID = $oType->getOxidFromKeyFields($aData);
        if (isset($sOXID)) {
            // note: also pass false here
            return $sOXID;
        }

        return oxUtilsObject::getInstance()->generateUID();
    }

    /**
     * Reset import counter, if retry is detected, only failed imports are repeated
     */
    protected function _resetIdx()
    {
        $this->_iIdx = 0;

        if (count($this->_aStatistics) && isset($this->_aStatistics[$this->_iIdx])) {
            while (isset($this->_aStatistics[$this->_iIdx]) && $this->_aStatistics[$this->_iIdx]['r']) {
                $this->_iIdx++;
            }
        }
    }

    /**
     * Increase import counter, if retry is detected, only failed imports are repeated
     */
    protected function _nextIdx()
    {
        $this->_iIdx++;

        if (count($this->_aStatistics) && isset($this->_aStatistics[$this->_iIdx])) {
            while (isset($this->_aStatistics[$this->_iIdx]) && $this->_aStatistics[$this->_iIdx]['r']) {
                $this->_iIdx++;
            }
        }
    }

    /**
     * Checks if user as sufficient rights
     *
     * @param oxErpType $oType   data type object
     * @param boolean   $blWrite check also for write access
     * @param string    $sOxid   check write access for this OXID
     */
    protected function _checkAccess($oType, $blWrite, $sOxid = null)
    {
        $myConfig = oxRegistry::getConfig();
        static $aAccessCache;

        if (!$this->_blInit) {
            throw new Exception(self::ERROR_NO_INIT);
        }

    }

    /**
     * Main Import Handler, imports one row/call/object...
     * returns true if there were any data processed, and
     * master loop should run import again.
     *
     * after importing, fills $this->_aStatistics[$this->_iIdx] with array
     * of r=>(boolean)result, m=>(string)error message
     *
     * @return boolean
     */
    protected function _importOne()
    {
        $blRet = false;

        // import one row/call/object...
        $aData = $this->getImportData();

        if ($aData) {
            $blRet = true;
            $blImport = false;
            $sMessage = '';

            $sType = $this->_getImportType($aData);
            $sMode = $this->_getImportMode($aData);
            $oType = $this->_getInstanceOfType($sType);
            $aData = $this->_modifyData($aData, $oType);

            // import now
            $sFnc = '_' . $sMode . $oType->getFunctionSuffix();

            if ($sMode == oxERPBase::$MODE_IMPORT) {
                $aData = $oType->addImportData($aData);
            }

            try {
                $iId = $this->$sFnc($oType, $aData);
                if (!$iId) {
                    $blImport = false;
                } else {
                    $this->setImportedIds($iId);
                    $blImport = true;
                }
                $sMessage = '';
            } catch (Exception $e) {
                $sMessage = $e->getMessage();
            }

            $this->_aStatistics[$this->_iIdx] = array('r' => $blImport, 'm' => $sMessage);

        }
        //hotfix #2428 MAFI
        $this->_nextIdx();

        return $blRet;
    }


    /**
     * Insert or Update a Row into database
     *
     * @param oxERPType &$oType              data type object
     * @param array     $aData               assoc. Array with fieldnames, values what should be stored in this table
     * @param bool      $blAllowCustomShopId if custom shop id is allowed
     *
     * @return string | false
     */
    protected function _save(oxERPType &$oType, $aData, $blAllowCustomShopId = false)
    {
        $myConfig = oxRegistry::getConfig();

        // check rights
        $sOxid = null;
        if (isset($aData['OXID'])) {
            $sOxid = $aData['OXID'];
        }
        $this->_checkAccess($oType, true, $sOxid);

        return $oType->saveObject($aData, $blAllowCustomShopId);
    }

    /**
     * checks if erp version is supported by shop
     *
     * @throws Exception on not supported shop version
     *
     * @return null
     */
    protected static function _checkShopVersion()
    {
        $myConfig = oxRegistry::getConfig();
        if (method_exists($myConfig, 'getSerial')) {
            if ($myConfig->getSerial() instanceof oxSerial) {
                return;
            }
        }
        throw new Exception(self::ERROR_SHOP_VERSION_NOT_SUPPORTED_BY_ERP);
    }

    /**
     * checks requested version if it is supported by shop
     *
     * @throws Exception on not supported version
     *
     * @return null
     */
    protected static function _checkRequestedVersion()
    {
        return true;
    }

    /**
     * gets requested db layer version
     *
     * @throws Exception on not supported version
     *
     * @return string
     */
    public static function getRequestedVersion()
    {
        if (!self::$_sRequestedVersion) {
            self::setVersion();
        }

        return self::$_sRequestedVersion;
    }

    /**
     * gets requested version for db fields used
     *
     * @return string
     */
    public static function getUsedDbFieldsVersion()
    {
        return self::$_aDbLayer2ShopDbVersions[self::getRequestedVersion()];
    }

    /**
     * gets requested db layer version
     *
     * @param string $sDbLayerVersion requested version
     *
     * @throws Exception on not supported version
     */
    public static function setVersion($sDbLayerVersion = '')
    {
        $sDbLayerVersion = '2.9.0';
        self::$_sRequestedVersion = $sDbLayerVersion;
        self::_checkRequestedVersion();
    }

    /**
     * create plugin object
     *
     * @param string $sId the name of the plugin
     *
     * @return oxErpPluginBase
     */
    public function createPluginObject($sId)
    {
        $sClassName = preg_replace('/[^a-z0-9_]/i', '', $sId);
        if (preg_match('/(.*)Plugin$/i', $sClassName, $m)) {
            // fix possible case changes
            $sClassName = $m[1] . 'Plugin';
        } else {
            throw new Exception("Plugin handler class has to end with 'Plugin' word (GOT '$sClassName').");
        }

        $sFileName = dirname(__FILE__) . '/plugins/' . strtolower($sClassName) . '.php';
        if (!is_readable($sFileName)) {
            $sFileName = basename($sFileName);
            throw new Exception("Can not find the requested plugin file ('$sFileName').");
        }
        include_once dirname(__FILE__) . '/plugins/oxerppluginbase.php';
        include_once $sFileName;
        if (!class_exists($sClassName)) {
            throw new Exception("Can not find the requested plugin class.");
        }
        $o = new $sClassName();
        if ($o instanceof oxErpPluginBase) {
            return $o;
        }
        throw new Exception("Plugin does not extend oxErpPluginBase class.");
    }
}
