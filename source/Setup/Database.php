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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Setup;

use Conf;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Setup database manager class
 */
class Database extends Core
{

    /**
     * Connection resource object
     *
     * @var PDO
     */
    protected $_oConn = null;

    /**
     * Error while opening sql file
     *
     * @var int
     */
    const ERROR_OPENING_SQL_FILE = 1;

    /**
     * Error while opening db connection
     *
     * @var int
     */
    const ERROR_DB_CONNECT = 1;

    /**
     * Error while creating db
     *
     * @var int
     */
    const ERROR_COULD_NOT_CREATE_DB = 2;

    /**
     * MySQL version does not fir requirements
     *
     * @var int
     */
    const ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS = 3;

    /**
     * Executes sql query. Returns query execution resource object
     *
     * @param string $sQ query to execute
     *
     * @throws Exception exception is thrown if error occured during sql execution
     *
     * @return PDOStatement|int
     */
    public function execSql($sQ)
    {
        try {
            $pdo = $this->getConnection();
            list ($sStatement) = explode(" ", ltrim($sQ));
            if (in_array(strtoupper($sStatement), array('SELECT', 'SHOW'))) {
                $oStatement = $pdo->query($sQ);
            } else {
                return $pdo->exec($sQ);
            }

            return $oStatement;
        } catch (PDOException $e) {
            throw new Exception(
                $this->getInstance("Language")->getText('ERROR_BAD_SQL') . "( $sQ ): {$e->getMessage()}\n"
            );
        }
    }

    /**
     * Testing if no error occurs while creating views
     *
     * @throws Exception exception is thrown if error occured during view creation
     */
    public function testCreateView()
    {
        $oPdo = $this->getConnection();
        try {
            // testing creation
            $sQ      = "create or replace view oxviewtest as select 1";
            $oPdo->exec($sQ);
        } catch (PDOException $e) {
            throw new Exception(
                $this->getInstance("Language")->getText('ERROR_VIEWS_CANT_CREATE') . " {$e->getMessage()}\n"
            );
        }

        try {
            // testing data selection
            $sQ      = "SELECT * FROM oxviewtest";
            $oPdo->query($sQ)->closeCursor();
        } catch (PDOException $e) {
            throw new Exception(
                $this->getInstance("Language")->getText('ERROR_VIEWS_CANT_SELECT') . " {$e->getMessage()}\n"
            );
        }

        try {
            // testing view dropping
            $sQ      = "drop view oxviewtest";
            $oPdo->exec($sQ);
        } catch (PDOException $e) {
            throw new Exception(
                $this->getInstance("Language")->getText('ERROR_VIEWS_CANT_DROP') . " {$e->getMessage()}\n"
            );
        }
    }

    /**
     * Executes queries stored in passed file
     *
     * @param string $sFilename file name where queries are stored
     */
    public function queryFile($sFilename)
    {
        $fp = @fopen($sFilename, "r");
        if (!$fp) {
            /** @var Setup $oSetup */
            $oSetup = $this->getInstance("Setup");
            // problems with file
            $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
            throw new Exception(sprintf($this->getInstance("Language")->getText('ERROR_OPENING_SQL_FILE'), $sFilename), Database::ERROR_OPENING_SQL_FILE);
        }

        $sQuery = fread($fp, filesize($sFilename));
        fclose($fp);

        if (version_compare($this->getDatabaseVersion(), "5") > 0) {
            //disable STRICT db mode if there are set any (mysql >= 5).
            $this->execSql("SET @@session.sql_mode = ''");
        }

        $aQueries = $this->parseQuery($sQuery);
        foreach ($aQueries as $sQuery) {
            $this->execSql($sQuery);
        }
    }

    /**
     * Returns database version
     *
     * @return string
     */
    public function getDatabaseVersion()
    {

        $oStatement = $this->execSql("SHOW VARIABLES LIKE 'version'");
        return $oStatement->fetchColumn(1);
    }

    /**
     * Returns connection resource object
     *
     * @return PDO
     */
    public function getConnection()
    {
        if ($this->_oConn === null) {
            $this->_oConn = $this->openDatabase(null);
        }

        return $this->_oConn;
    }

    /**
     * Opens database connection and returns connection resource object
     *
     * @param array $aParams database connection parameters array
     *
     * @throws Exception exception is thrown if connection failed or was unable to select database
     *
     * @return object
     */
    public function openDatabase($aParams)
    {
        $aParams = (is_array($aParams) && count($aParams)) ? $aParams : $this->getInstance("Session")->getSessionParam('aDB');
        if ($this->_oConn === null) {
            // ok open DB
            try {
                $dsn = sprintf('mysql:host=%s', $aParams['dbHost']);
                $this->_oConn = new PDO(
                    $dsn,
                    $aParams['dbUser'],
                    $aParams['dbPwd'],
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
                );
                $this->_oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_oConn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                /** @var Setup $oSetup */
                $oSetup = $this->getInstance("Setup");
                $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
                throw new Exception($this->getInstance("Language")->getText('ERROR_DB_CONNECT') . " - " . $e->getMessage(), Database::ERROR_DB_CONNECT, $e);
            }

            // testing version
            $oSysReq = getSystemReqCheck();
            if (!$oSysReq->checkMysqlVersion($this->getDatabaseVersion())) {
                throw new Exception($this->getInstance("Language")->getText('ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS'), Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS);
            }
            try {
                $this->_oConn->exec("USE `{$aParams['dbName']}`");
            } catch (Exception $e) {
                throw new Exception($this->getInstance("Language")->getText('ERROR_COULD_NOT_CREATE_DB') . " - " . $e->getMessage(), Database::ERROR_COULD_NOT_CREATE_DB, $e);
            }
        }

        return $this->_oConn;
    }

    /**
     * Creates database
     *
     * @param string $sDbName database name
     *
     * @throws Exception exception is thrown if database creation failed
     */
    public function createDb($sDbName)
    {
        try {
            $this->execSql("CREATE DATABASE `$sDbName`");
        } catch (Exception $e) {
            $oSetup = $this->getInstance("Setup");
            $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
            throw new Exception(sprintf($this->getInstance("Language")->getText('ERROR_COULD_NOT_CREATE_DB'), $sDbName) . " - " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Saves shop settings.
     *
     * @param array $aParams parameters to save to db
     */
    public function saveShopSettings($aParams)
    {
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");

        $oConfk = new Conf();

        $oPdo = $this->getConnection();

        $this->setIfDynamicPagesShouldBeUsed($oSession);

        $blUseDynPages = isset($aParams["use_dyn_pages"]) ? $aParams["use_dyn_pages"] : $oSession->getSessionParam('use_dynamic_pages');
        $sLocationLang = isset($aParams["location_lang"]) ? $aParams["location_lang"] : $oSession->getSessionParam('location_lang');
        $blCheckForUpdates = isset($aParams["check_for_updates"]) ? $aParams["check_for_updates"] : $oSession->getSessionParam('check_for_updates');
        $sCountryLang = isset($aParams["country_lang"]) ? $aParams["country_lang"] : $oSession->getSessionParam('country_lang');
        $sShopLang = isset($aParams["sShopLang"]) ? $aParams["sShopLang"] : $oSession->getSessionParam('sShopLang');
        $sBaseShopId = $this->getInstance("Setup")->getShopId();

        $oPdo->exec("update oxcountry set oxactive = '0'");
        $oPdo->exec("update oxcountry set oxactive = '1' where oxid = '$sCountryLang'");

        // if it is international eshop, setting admin user country to selected one
        if ($oSession->getSessionParam('location_lang') != "de") {
            $oPdo->exec("UPDATE oxuser SET oxcountryid = '$sCountryLang' where OXUSERNAME='admin'");
        }

        $oPdo->exec("delete from oxconfig where oxvarname = 'blLoadDynContents'");
        $oPdo->exec("delete from oxconfig where oxvarname = 'sShopCountry'");
        $oPdo->exec("delete from oxconfig where oxvarname = 'blCheckForUpdates'");
        // $this->execSql( "delete from oxconfig where oxvarname = 'aLanguageParams'" );

        $oInsert = $oPdo->prepare("insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values(:oxid, :shopId, :name, :type, ENCODE( :value, '{$oConfk->sConfigKey}'))");
        $oInsert->execute(
            array(
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'blLoadDynContents',
                'type' => 'bool',
                'value' => $blUseDynPages
            )
        );

        $oInsert->execute(
            array(
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'sShopCountry',
                'type' => 'str',
                'value' => $sLocationLang
            )
        );

        $oInsert->execute(
            array(
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'blCheckForUpdates',
                'type' => 'bool',
                'value' => $blCheckForUpdates
            )
        );

        $this->addConfigValueIfShopInfoShouldBeSent($oUtils, $sBaseShopId, $aParams, $oConfk, $oSession);

        //set only one active language
        $oStatement = $oPdo->query("select oxvarname, oxvartype, DECODE( oxvarvalue, '" . $oConfk->sConfigKey . "') AS oxvarvalue from oxconfig where oxvarname='aLanguageParams'");
        if ($oStatement && false !== ($aRow = $oStatement->fetch())) {
            if ($aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr') {
                $aRow['oxvarvalue'] = unserialize($aRow['oxvarvalue']);
            }
            $aLanguageParams = $aRow['oxvarvalue'];
            foreach ($aLanguageParams as $sKey => $aLang) {
                $aLanguageParams[$sKey]["active"] = "0";
            }
            $aLanguageParams[$sShopLang]["active"] = "1";

            $sValue = serialize($aLanguageParams);

            $oInsert->execute(
                array(
                    'oxid' => $oUtils->generateUid(),
                    'shopId' => $sBaseShopId,
                    'name' => 'aLanguageParams',
                    'type' => 'aarr',
                    'value' => $sValue
                )
            );
        }
    }

    /**
     * Converts config table values to utf8
     */
    public function convertConfigTableToUtf()
    {
        $oConfk = new Conf();
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");

        $pdo = $this->getConnection();

        $sSql = "SELECT oxid, oxvarname, oxvartype, DECODE( oxvarvalue, '{$oConfk->sConfigKey}') AS oxvarvalue FROM oxconfig WHERE oxvartype IN ('str', 'arr', 'aarr') ";
        $oSelect = $pdo->query($sSql);
        $oUpdate = $pdo->prepare("UPDATE oxconfig SET oxvarvalue = ENCODE( :varValue, '{$oConfk->sConfigKey}') WHERE oxid = :oxid; ");

        while (false !== ($aRow = $oSelect->fetch())) {
            if ($aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr') {
                $aRow['oxvarvalue'] = unserialize($aRow['oxvarvalue']);
            }

            $aRow['oxvarvalue'] = $oUtils->convertToUtf8($aRow['oxvarvalue']);

            $sVarValue = $aRow['oxvarvalue'];
            if (is_array($aRow['oxvarvalue'])) {
                $sVarValue = serialize($aRow['oxvarvalue']);
            }

            $oUpdate->execute(array('varValue' => $sVarValue, 'oxid' => $aRow['oxid']));
        }
    }

    /**
     * Parses query string into sql sentences
     *
     * @param string $sSQL query string (usually reqd from *.sql file)
     *
     * @return array
     */
    public function parseQuery($sSQL)
    {
        // parses query into single pieces
        $aRet = array();
        $blComment = false;
        $blQuote = false;
        $sThisSQL = "";

        $aLines = explode("\n", $sSQL);

        // parse it
        foreach ($aLines as $sLine) {
            $iLen = strlen($sLine);
            for ($i = 0; $i < $iLen; $i++) {
                if (!$blQuote && ($sLine[$i] == '#' || ($sLine[0] == '-' && $sLine[1] == '-'))) {
                    $blComment = true;
                }

                // add this char to current command
                if (!$blComment) {
                    $sThisSQL .= $sLine[$i];
                }

                // test if quote on
                if (($sLine[$i] == '\'' && $sLine[$i - 1] != '\\')) {
                    $blQuote = !$blQuote; // toggle
                }

                // now test if command end is reached
                if (!$blQuote && $sLine[$i] == ';') {
                    // add this
                    $sThisSQL = trim($sThisSQL);
                    if ($sThisSQL) {
                        $sThisSQL = str_replace("\r", "", $sThisSQL);
                        $aRet[] = $sThisSQL;
                    }
                    $sThisSQL = "";
                }
            }
            // comments and quotes can't run over newlines
            $blComment = false;
            $blQuote = false;
        }

        return $aRet;
    }

    /**
     * Sets various connection collation parameters
     *
     * @param int $iUtfMode utf8 mode
     */
    public function setMySqlCollation($iUtfMode)
    {
        $pdo = $this->getConnection();
        if ($iUtfMode) {
            $pdo->exec("ALTER SCHEMA CHARACTER SET utf8 COLLATE utf8_general_ci");
            $pdo->exec("set names 'utf8'");
            $pdo->exec("set character_set_database=utf8");
            $pdo->exec("SET CHARACTER SET latin1");
            $pdo->exec("SET CHARACTER_SET_CONNECTION = utf8");
            $pdo->exec("SET character_set_results = utf8");
            $pdo->exec("SET character_set_server = utf8");
        } else {
            $pdo->exec("ALTER SCHEMA CHARACTER SET latin1 COLLATE latin1_general_ci");
            $pdo->exec("SET CHARACTER SET latin1");
        }
    }

    /**
     * Writes utf mode config parameter to db
     *
     * @param int $iUtfMode utf mode
     */
    public function writeUtfMode($iUtfMode)
    {
        $sBaseShopId = $this->getInstance("Setup")->getShopId();
        $oConfk = new Conf();
        $sQ = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values('iSetUtfMode', '$sBaseShopId', 'iSetUtfMode', 'str', ENCODE( '{$iUtfMode}', '" . $oConfk->sConfigKey . "') )";

        $this->execSql($sQ);
    }

    /**
     * Updates default admin user login name and password
     *
     * @param string $sLoginName admin user login name
     * @param string $sPassword  admin user login password
     */
    public function writeAdminLoginData($sLoginName, $sPassword)
    {
        $sPassSalt = $this->getInstance("Utilities")->generateUID();

        $sPassword = hash('sha512', $sPassword . $sPassSalt);

        $sQ = "update oxuser set oxusername='{$sLoginName}', oxpassword='{$sPassword}', oxpasssalt='{$sPassSalt}' where OXUSERNAME='admin'";
        $this->execSql($sQ);

        $sQ = "update oxnewssubscribed set oxemail='{$sLoginName}' where OXEMAIL='admin'";
        $this->execSql($sQ);
    }

    /**
     * Adds config value if shop info should be set.
     *
     * @param Utilities $utilities  Setup utilities
     * @param string    $baseShopId Shop id
     * @param array     $parameters Parameters
     * @param Conf      $configKey  Config key loader
     * @param Session   $session    Setup session manager
     */
    protected function addConfigValueIfShopInfoShouldBeSent($utilities, $baseShopId, $parameters, $configKey, $session)
    {
        $blSendShopDataToOxid = isset($parameters["blSendShopDataToOxid"]) ? $parameters["blSendShopDataToOxid"] : $session->getSessionParam('blSendShopDataToOxid');

        $sID = $utilities->generateUid();
        $this->execSql(
            "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                             values('$sID', '$baseShopId', 'blSendShopDataToOxid', 'bool', ENCODE( '$blSendShopDataToOxid', '" . $configKey->sConfigKey . "'))"
        );
    }

    /**
     * Set to session if dynamic pages should be used.
     *
     * @param Session $session
     */
    protected function setIfDynamicPagesShouldBeUsed($session)
    {
        // disabling usage of dynamic pages if shop country is international
        if ($session->getSessionParam('location_lang') === null) {
            $session->setSessionParam('use_dynamic_pages', 'false');
        }
    }
}
