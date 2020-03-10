<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

use Exception;
use OxidEsales\EshopCommunity\Setup\Exception\LanguageParamsException;
use OxidEsales\Facts\Facts;
use PDO;
use PDOException;
use PDOStatement;

class Database extends Core
{
    /** @var int */
    public const ERROR_DB_CONNECT = 1;
    /** @var int */
    public const ERROR_COULD_NOT_CREATE_DB = 2;
    /** @var int */
    private const ERROR_OPENING_SQL_FILE = 1;
    /** @var PDO */
    protected $_oConn = null;

    /**
     * Executes sql query. Returns query execution resource object
     *
     * @param string $sQ query to execute
     *
     * @return PDOStatement|int
     * @throws Exception exception is thrown if error occured during sql execution
     */
    public function execSql($sQ)
    {
        try {
            $pdo = $this->getConnection();
            list($sStatement) = explode(' ', ltrim($sQ));
            if (in_array(strtoupper($sStatement), ['SELECT', 'SHOW'])) {
                $oStatement = $pdo->query($sQ);
            } else {
                return $pdo->exec($sQ);
            }

            return $oStatement;
        } catch (PDOException $e) {
            throw new Exception(
                $this->translate('ERROR_BAD_SQL') . "( $sQ ): {$e->getMessage()}\n"
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
            $sQ = "create or replace view oxviewtest as select 1";
            $oPdo->exec($sQ);
        } catch (PDOException $e) {
            throw new Exception(
                $this->translate('ERROR_VIEWS_CANT_CREATE') . " {$e->getMessage()}\n"
            );
        }

        try {
            // testing data selection
            $sQ = "SELECT * FROM oxviewtest";
            $oPdo->query($sQ)->closeCursor();
        } catch (PDOException $e) {
            throw new Exception(
                $this->translate('ERROR_VIEWS_CANT_SELECT') . " {$e->getMessage()}\n"
            );
        }

        try {
            // testing view dropping
            $sQ = "drop view oxviewtest";
            $oPdo->exec($sQ);
        } catch (PDOException $e) {
            throw new Exception(
                $this->translate('ERROR_VIEWS_CANT_DROP') . " {$e->getMessage()}\n"
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
            throw new Exception(sprintf($this->translate('ERROR_OPENING_SQL_FILE'), $sFilename), Database::ERROR_OPENING_SQL_FILE);
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
     * @param $parameters
     * @return PDO
     * @throws Exception
     */
    public function openDatabase($parameters)
    {
        $connectionParameters = $this->prepareConnectionParameters($parameters);
        $this->preparePdoConnection($connectionParameters);
        $this->executeUseStatement($connectionParameters['dbName']);
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
            $this->execSql("CREATE DATABASE `$sDbName` CHARACTER SET utf8 COLLATE utf8_general_ci;");
            $this->executeUseStatement($sDbName);
        } catch (Exception $e) {
            $oSetup = $this->getInstance("Setup");
            $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
            throw new Exception(sprintf($this->translate('ERROR_COULD_NOT_CREATE_DB'), $sDbName) . " - " . $e->getMessage(), $e->getCode(), $e);
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

        $oPdo = $this->getConnection();

        $blSendTechnicalInformationToOxid = true;
        $facts = new Facts();
        if ($facts->isCommunity()) {
            $blSendTechnicalInformationToOxid = isset($aParams["send_technical_information_to_oxid"]) ? $aParams["send_technical_information_to_oxid"] : $oSession->getSessionParam('send_technical_information_to_oxid');
        }
        $blCheckForUpdates = isset($aParams["check_for_updates"]) ? $aParams["check_for_updates"] : $oSession->getSessionParam('check_for_updates');
        $sCountryLang = isset($aParams["country_lang"]) ? $aParams["country_lang"] : $oSession->getSessionParam('country_lang');
        $sShopLang = isset($aParams["sShopLang"]) ? $aParams["sShopLang"] : $oSession->getSessionParam('sShopLang');
        $sBaseShopId = $this->getInstance("Setup")->getShopId();

        $oPdo->exec("update oxcountry set oxactive = '0'");

        $oUpdate = $oPdo->prepare("update oxcountry set oxactive = '1' where oxid = :countryLang");
        $oUpdate->execute([':countryLang' => $sCountryLang]);

        $oPdo->exec("delete from oxconfig where oxvarname = 'blSendTechnicalInformationToOxid'");
        $oPdo->exec("delete from oxconfig where oxvarname = 'blCheckForUpdates'");
        $oPdo->exec("delete from oxconfig where oxvarname = 'sDefaultLang'");
        // $this->execSql( "delete from oxconfig where oxvarname = 'aLanguageParams'" );

        $oInsert = $oPdo->prepare("insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                             values (:oxid, :shopId, :name, :type, :value)");
        $oInsert->execute(
            [
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'blSendTechnicalInformationToOxid',
                'type' => 'bool',
                'value' => $blSendTechnicalInformationToOxid
            ]
        );

        $oInsert->execute(
            [
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'blCheckForUpdates',
                'type' => 'bool',
                'value' => $blCheckForUpdates
            ]
        );

        $oInsert->execute(
            [
                'oxid' => $oUtils->generateUid(),
                'shopId' => $sBaseShopId,
                'name' => 'sDefaultLang',
                'type' => 'str',
                'value' => $sShopLang
            ]
        );

        $this->addConfigValueIfShopInfoShouldBeSent($oUtils, $sBaseShopId, $aParams, $oSession);

        //set only one active language
        $oStatement = $oPdo->query("select oxvarname, oxvartype, oxvarvalue from oxconfig where oxvarname='aLanguageParams'");
        if ($oStatement && false !== ($aRow = $oStatement->fetch())) {
            if (!is_array(unserialize($aRow['oxvarvalue']))) {
                throw new LanguageParamsException("aLanguageParams can not be type of 
                " . gettype($aRow['oxvarvalue']) . ", aLanguageParams must be type of array");
            }

            if ($aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr') {
                $aRow['oxvarvalue'] = unserialize($aRow['oxvarvalue']);
            }
            $aLanguageParams = $aRow['oxvarvalue'];
            foreach ($aLanguageParams as $sKey => $aLang) {
                $aLanguageParams[$sKey]["active"] = "0";
            }
            $aLanguageParams[$sShopLang]["active"] = "1";

            $sValue = serialize($aLanguageParams);

            $oPdo->exec("delete from oxconfig where oxvarname = 'aLanguageParams'");
            $oInsert->execute(
                [
                    'oxid' => $oUtils->generateUid(),
                    'shopId' => $sBaseShopId,
                    'name' => 'aLanguageParams',
                    'type' => 'aarr',
                    'value' => $sValue
                ]
            );
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
        $aRet = [];
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
     * Updates default admin user login name and password
     *
     * @param string $sLoginName admin user login name
     * @param string $sPassword admin user login password
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
     * @param Utilities $utilities Setup utilities
     * @param string $baseShopId Shop id
     * @param array $parameters Parameters
     * @param Session $session Setup session manager
     */
    protected function addConfigValueIfShopInfoShouldBeSent($utilities, $baseShopId, $parameters, $session)
    {
        $blSendShopDataToOxid = isset($parameters["blSendShopDataToOxid"]) ? $parameters["blSendShopDataToOxid"] : $session->getSessionParam('blSendShopDataToOxid');

        $sID = $utilities->generateUid();
        $this->execSql("delete from oxconfig where oxvarname = 'blSendShopDataToOxid'");
        $this->execSql(
            "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                             values('$sID', '$baseShopId', 'blSendShopDataToOxid', 'bool', '$blSendShopDataToOxid')"
        );
    }

    /**
     * @param $parameters
     * @return array
     */
    private function prepareConnectionParameters($parameters): array
    {
        return (is_array($parameters) && !empty($parameters)) ?
            $parameters :
            $this->getInstance('Session')->getSessionParam('aDB');
    }

    /**
     * @param array $connectionParameters
     * @throws Exception
     */
    private function preparePdoConnection(array $connectionParameters): void
    {
        if ($this->_oConn !== null) {
            return;
        }
        try {
            $this->createPdoConnection($connectionParameters);
        } catch (PDOException $e) {
            $this->resetSetupStep();
            throw new Exception(
                $this->translate('ERROR_DB_CONNECT') . ' - ' . $e->getMessage(),
                Database::ERROR_DB_CONNECT,
                $e
            );
        }
    }

    /** @param array $parameters */
    private function createPdoConnection(array $parameters): void
    {
        $dsn = sprintf('mysql:host=%s;port=%s', $parameters['dbHost'], $parameters['dbPort']);
        $this->_oConn = new PDO(
            $dsn,
            $parameters['dbUser'],
            $parameters['dbPwd'],
            [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
        );
        $this->_oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_oConn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    private function resetSetupStep(): void
    {
        $setup = $this->getInstance('Setup');
        $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
    }

    /**
     * @param string $name
     * @throws Exception
     */
    private function executeUseStatement(string $name): void
    {
        try {
            $this->_oConn->exec("USE `$name`");
        } catch (Exception $e) {
            throw new Exception(
                $this->translate('ERROR_COULD_NOT_CREATE_DB') . ' - ' . $e->getMessage(),
                Database::ERROR_COULD_NOT_CREATE_DB,
                $e
            );
        }
    }

    /**
     * @param string $message
     * @return string
     */
    private function translate(string $message): string
    {
        return (string)$this->getInstance('Language')->getText($message);
    }
}
