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

namespace OxidEsales\Eshop\Setup;

use Exception;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;

/**
 * Class holds scripts (controllers) needed to perform shop setup steps
 */
class Controller extends Core
{
    /** @var View */
    private $_oView = null;

    /**
     * Returns View object
     *
     * @return View
     */
    public function getView()
    {
        if ($this->_oView == null) {
            $this->_oView = new View();
        }

        return $this->_oView;
    }

    // ---- controllers ----
    /**
     * First page with system requirements check
     *
     * @return string
     */
    public function systemReq()
    {
        /** @var Setup $oSetup */
        $oSetup = $this->getInstance("Setup");
        /** @var Language $oLanguage */
        $oLanguage = $this->getInstance("Language");
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");
        $oView = $this->getView();

        $blContinue = true;
        $aGroupModuleInfo = array();

        $blHtaccessUpdateError = false;
        try {
            $aPath = $oUtils->getDefaultPathParams();
            $aPath['sBaseUrlPath'] = $oUtils->extractRewriteBase($aPath['sShopURL']);
            //$oUtils->updateHtaccessFile( $aPath, "admin" );
            $oUtils->updateHtaccessFile($aPath);
        } catch (Exception $oExcp) {
            //$oView->setMessage( $oExcp->getMessage() );
            $blHtaccessUpdateError = true;
        }

        $oSysReq = getSystemReqCheck();
        $aInfo = $oSysReq->getSystemInfo();
        foreach ($aInfo as $sGroup => $aModules) {
            // translating
            $sGroupName = $oLanguage->getModuleName($sGroup);
            foreach ($aModules as $sModule => $iModuleState) {
                // translating
                $blContinue = $blContinue && ( bool ) abs($iModuleState);

                // was unable to update htaccess file for mod_rewrite check
                if ($blHtaccessUpdateError && $sModule == 'server_permissions') {
                    $sClass = $oSetup->getModuleClass(0);
                    $blContinue = false;
                } else {
                    $sClass = $oSetup->getModuleClass($iModuleState);
                }
                $aGroupModuleInfo[$sGroupName][] = array('module'     => $sModule,
                    'class'      => $sClass,
                    'modulename' => $oLanguage->getModuleName($sModule));
            }
        }

        $oView->setTitle('STEP_0_TITLE');
        $oView->setViewParam("blContinue", $blContinue);
        $oView->setViewParam("aGroupModuleInfo", $aGroupModuleInfo);
        $oView->setViewParam("aLanguages", getLanguages());
        $oView->setViewParam("sLanguage", $this->getInstance("Session")->getSessionParam('setup_lang'));

        return "systemreq.php";
    }

    /**
     * Welcome page
     *
     * @return string
     */
    public function welcome()
    {
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");

        //setting admin area default language
        $sAdminLang = $oSession->getSessionParam('setup_lang');
        $this->getInstance("Utilities")->setCookie("oxidadminlanguage", $sAdminLang, time() + 31536000, "/");

        $oView = $this->getView();
        $oView->setTitle('STEP_1_TITLE');
        $oView->setViewParam("aCountries", getCountryList());
        $oView->setViewParam("aLocations", getLocation());
        $oView->setViewParam("aLanguages", getLanguages());
        $oView->setViewParam("sShopLang", $oSession->getSessionParam('sShopLang'));
        $oView->setViewParam("sLanguage", $this->getInstance("Language")->getLanguage());
        $oView->setViewParam("sLocationLang", $oSession->getSessionParam('location_lang'));
        $oView->setViewParam("sCountryLang", $oSession->getSessionParam('country_lang'));

        return "welcome.php";
    }

    /**
     * License confirmation page
     *
     * @return string
     */
    public function license()
    {
        $sLicenseFile = "lizenz.txt";

        $editionPathSelector = $this->getEditionPathProvider();

        $oView = $this->getView();
        $oView->setTitle('STEP_2_TITLE');
        $oView->setViewParam("aLicenseText", $this->getInstance("Utilities")->getFileContents(
            $editionPathSelector->getSetupDirectory()
            . '/'. ucfirst($this->getInstance("Language")->getLanguage())
            . '/' . $sLicenseFile
        ));

        return "license.php";
    }

    /**
     * DB info entry page
     *
     * @return string
     */
    public function dbInfo()
    {
        $oView = $this->getView();
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");

        $iEula = $this->getInstance("Utilities")->getRequestVar("iEula", "post");
        $iEula = (int) ($iEula ? $iEula : $oSession->getSessionParam("eula"));
        if (!$iEula) {
            /** @var Setup $oSetup */
            $oSetup = $this->getInstance("Setup");
            $oSetup->setNextStep($oSetup->getStep("STEP_WELCOME"));
            $oView->setMessage($this->getInstance("Language")->getText("ERROR_SETUP_CANCELLED"));

            return "licenseerror.php";
        }

        $oView->setTitle('STEP_3_TITLE');
        $aDB = $oSession->getSessionParam('aDB');
        if (!isset($aDB)) {
            // default values
            $aDB['dbHost'] = "localhost";
            $aDB['dbUser'] = "";
            $aDB['dbPwd'] = "";
            $aDB['dbName'] = "";
            $aDB['dbiDemoData'] = 1;
        }
        $oView->setViewParam("aDB", $aDB);

        // mb string library info
        $oSysReq = getSystemReqCheck();
        $oView->setViewParam("blMbStringOn", $oSysReq->getModuleInfo('mb_string'));
        $oView->setViewParam("blUnicodeSupport", $oSysReq->getModuleInfo('unicode_support'));

        return "dbinfo.php";
    }

    /**
     * Setup paths info entry page
     *
     * @return string
     */
    public function dirsInfo()
    {
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        $oView = $this->getView();
        $oView->setTitle('STEP_4_TITLE');
        $oView->setViewParam("aSetupConfig", $oSession->getSessionParam('aSetupConfig'));
        $oView->setViewParam("aAdminData", $oSession->getSessionParam('aAdminData'));
        $oView->setViewParam("aPath", $this->getInstance("Utilities")->getDefaultPathParams());

        return "dirsinfo.php";
    }

    /**
     * Testing database connection
     *
     * @return string
     */
    public function dbConnect()
    {
        /** @var Setup $oSetup */
        $oSetup = $this->getInstance("Setup");
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        /** @var Language $oLang */
        $oLang = $this->getInstance("Language");

        $oView = $this->getView();
        $oView->setTitle('STEP_3_1_TITLE');

        $aDB = $this->getInstance("Utilities")->getRequestVar("aDB", "post");
        if (!isset($aDB['iUtfMode'])) {
            $aDB['iUtfMode'] = 0;
        }
        $oSession->setSessionParam('aDB', $aDB);

        // check if iportant parameters are set
        if (!$aDB['dbHost'] || !$aDB['dbName']) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
            $oView->setMessage($oLang->getText('ERROR_FILL_ALL_FIELDS'));

            return "default.php";
        }

        try {
            // ok check DB Connection
            /** @var Database $oDb */
            $oDb = $this->getInstance("Database");
            $oDb->openDatabase($aDB);
        } catch (Exception $oExcp) {
            if ($oExcp->getCode() === Database::ERROR_DB_CONNECT) {
                $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
                $oView->setMessage($oLang->getText('ERROR_DB_CONNECT') . " - " . $oExcp->getMessage());

                return "default.php";
            } elseif ($oExcp->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS) {
                $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
                $oView->setMessage($oExcp->getMessage());

                return "default.php";
            } else {
                try {
                    // if database is not there, try to create it
                    $oDb->createDb($aDB['dbName']);
                } catch (Exception $oExcp) {
                    $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));
                    $oView->setMessage($oExcp->getMessage());

                    return "default.php";
                }
                $oView->setViewParam("blCreated", 1);
            }
        }

        $oView->setViewParam("aDB", $aDB);
        $oSetup->setNextStep($oSetup->getStep('STEP_DB_CREATE'));

        return "dbconnect.php";
    }

    /**
     * Creating database
     *
     * @return string
     */
    public function dbCreate()
    {
        /** @var Setup $oSetup */
        $oSetup = $this->getInstance("Setup");
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        /** @var Language $oLang */
        $oLang = $this->getInstance("Language");

        $oView = $this->getView();
        $oView->setTitle('STEP_3_2_TITLE');

        $aDB = $oSession->getSessionParam('aDB');
        $blOverwrite = $this->getInstance("Utilities")->getRequestVar("ow", "get");
        if (!isset($blOverwrite)) {
            $blOverwrite = false;
        }

        /** @var Database $oDb */
        $oDb = $this->getInstance("Database");
        $oDb->openDatabase($aDB);

        // testing if Views can be created
        try {
            $oDb->testCreateView();
        } catch (Exception $oExcp) {
            // Views can not be created
            $oView->setMessage($oExcp->getMessage());
            $oSetup->setNextStep($oSetup->getStep('STEP_DB_INFO'));

            return "default.php";
        }

        // check if DB is already UP and running
        if (!$blOverwrite) {
            try {
                $blDbExists = true;
                $oDb->execSql("select * from oxconfig");
            } catch (Exception $oExcp) {
                $blDbExists = false;
            }

            if ($blDbExists) {
                // DB already UP ?
                $oView->setMessage(
                    sprintf($oLang->getText('ERROR_DB_ALREADY_EXISTS'), $aDB['dbName']) .
                    "<br><br>" . $oLang->getText('STEP_3_2_CONTINUE_INSTALL_OVER_EXISTING_DB') . " <a href=\"index.php?sid=" . $oSession->getSid() . "&istep=" . $oSetup->getStep('STEP_DB_CREATE') . "&ow=1\" id=\"step3Continue\" style=\"text-decoration: underline;\">" . $oLang->getText('HERE') . "</a>"
                );

                return "default.php";
            }
        }

        $editionPathSelector = $this->getEditionPathProvider();
        $sqlDir = $editionPathSelector->getDatabaseSqlDirectory();

        $baseEditionPathSelector = $this->getEditionPathProvider(EditionSelector::COMMUNITY);
        $baseSqlDir = $baseEditionPathSelector->getDatabaseSqlDirectory();

        //setting database collation
        $iUtfMode = isset($aDB['iUtfMode']) ? ((int) $aDB['iUtfMode']) : 0;
        $oDb->setMySqlCollation($iUtfMode);

        try {
            $oDb->queryFile("$baseSqlDir/database_schema.sql");
            $oDb->queryFile("$baseSqlDir/initial_data.sql");

            /** @var ConfigFile $shopConfig */
            $shopConfig = Registry::get("oxConfigFile");
            $vendorDir = $shopConfig->getVar('vendorDirectory');

            exec("{$vendorDir}/bin/oe-eshop-facts oe-eshop-db_migrate");
            exec("{$vendorDir}/bin/oe-eshop-facts oe-eshop-db_views_regenerate");
        } catch (Exception $oExcp) {
            $oView->setMessage($oExcp->getMessage());

            return "default.php";
        }

        if ($aDB['dbiDemoData'] == '1') {
            // install demo data
            try {
                $oDb->queryFile("$sqlDir/demodata.sql");
            } catch (Exception $oExcp) {
                // there where problems with queries
                $oView->setMessage($oLang->getText('ERROR_BAD_DEMODATA') . "<br><br>" . $oExcp->getMessage());

                return "default.php";
            }
        }

        //swap database to english
        if ($oSession->getSessionParam('location_lang') != "de") {
            try {
                $oDb->queryFile("$sqlDir/en.sql");
            } catch (Exception $oExcp) {
                $oView->setMessage($oLang->getText('ERROR_BAD_DEMODATA') . "<br><br>" . $oExcp->getMessage());

                return "default.php";
            }
        }

        //update dyn pages / shop country config options (from first step)
        $oDb->saveShopSettings(array());

        //applying utf-8 specific queries

        if ($iUtfMode) {
            $oDb->queryFile("$sqlDir/latin1_to_utf8.sql");

            //converting oxconfig table field 'oxvarvalue' values to utf
            $oDb->setMySqlCollation(0);
            $oDb->convertConfigTableToUtf();
        }

        $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
        $oView->setMessage($oLang->getText('STEP_3_2_CREATING_DATA'));

        return "default.php";
    }

    /**
     * Writing config info
     *
     * @return string
     */
    public function dirsWrite()
    {
        $oView = $this->getView();

        /** @var Setup $oSetup */
        $oSetup = $this->getInstance("Setup");
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        /** @var Language $oLang */
        $oLang = $this->getInstance("Language");
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");

        $oView->setTitle('STEP_4_1_TITLE');

        $aPath = $oUtils->getRequestVar("aPath", "post");
        $aSetupConfig = $oUtils->getRequestVar("aSetupConfig", "post");
        $aAdminData = $oUtils->getRequestVar("aAdminData", "post");

        // correct them
        $aPath['sShopURL'] = $oUtils->preparePath($aPath['sShopURL']);
        $aPath['sShopDir'] = $oUtils->preparePath($aPath['sShopDir']);
        $aPath['sCompileDir'] = $oUtils->preparePath($aPath['sCompileDir']);
        $aPath['sBaseUrlPath'] = $oUtils->extractRewriteBase($aPath['sShopURL']);

        // using same array to pass additional setup variable
        if (isset($aSetupConfig['blDelSetupDir']) && $aSetupConfig['blDelSetupDir']) {
            $aSetupConfig['blDelSetupDir'] = 1;
        } else {
            $aSetupConfig['blDelSetupDir'] = 0;
        }

        $oSession->setSessionParam('aPath', $aPath);
        $oSession->setSessionParam('aSetupConfig', $aSetupConfig);
        $oSession->setSessionParam('aAdminData', $aAdminData);

        // check if important parameters are set
        if (!$aPath['sShopURL'] || !$aPath['sShopDir'] || !$aPath['sCompileDir'] ||
            !$aAdminData['sLoginName'] || !$aAdminData['sPassword'] || !$aAdminData['sPasswordConfirm']
        ) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
            $oView->setMessage($oLang->getText('ERROR_FILL_ALL_FIELDS'));

            return "default.php";
        }

        // check if passwords match
        if (strlen($aAdminData['sPassword']) < 6) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
            $oView->setMessage($oLang->getText('ERROR_PASSWORD_TOO_SHORT'));

            return "default.php";
        }

        // check if passwords match
        if ($aAdminData['sPassword'] != $aAdminData['sPasswordConfirm']) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
            $oView->setMessage($oLang->getText('ERROR_PASSWORDS_DO_NOT_MATCH'));

            return "default.php";
        }

        // check if email matches pattern
        if (!$oUtils->isValidEmail($aAdminData['sLoginName'])) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
            $oView->setMessage($oLang->getText('ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN'));

            return "default.php";
        }

        try {
            // creating admin user
            $this->getInstance("Database")->writeAdminLoginData($aAdminData['sLoginName'], $aAdminData['sPassword']);
        } catch (Exception $oExcp) {
            $oView->setMessage($oExcp->getMessage());

            return "default.php";
        }

        // write it now
        try {
            $aParams = array_merge(( array ) $oSession->getSessionParam('aDB'), $aPath);

            // updating config file
            $oUtils->updateConfigFile($aParams);

            // updating regular htaccess file
            $oUtils->updateHtaccessFile($aParams);

            // updating admin htaccess file
            //$oUtils->updateHtaccessFile( $aParams, "admin" );
        } catch (Exception $oExcp) {
            $oSetup->setNextStep($oSetup->getStep('STEP_DIRS_INFO'));
            $oView->setMessage($oExcp->getMessage());

            return "default.php";
        }

        $this->onDirsWriteSetStep($oSetup);

        $oView->setMessage($oLang->getText('STEP_4_1_DATA_WAS_WRITTEN'));
        $oView->setViewParam("aPath", $aPath);
        $oView->setViewParam("aSetupConfig", $aSetupConfig);

        return "default.php";
    }

    /**
     * Final setup step
     *
     * @return string
     */
    public function finish()
    {
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        $aPath = $oSession->getSessionParam("aPath");

        $oView = $this->getView();
        $oView->setTitle("STEP_6_TITLE");
        $oView->setViewParam("aPath", $aPath);
        $oView->setViewParam("aSetupConfig", $oSession->getSessionParam("aSetupConfig"));
        $oView->setViewParam("blWritableConfig", is_writable($aPath['sShopDir'] . "/config.inc.php"));

        return "finish.php";
    }

    /**
     * @param string $edition
     * @return EditionPathProvider
     */
    protected function getEditionPathProvider($edition = null)
    {
        $editionPathSelector = new EditionRootPathProvider(new EditionSelector($edition));
        return new EditionPathProvider($editionPathSelector);
    }

    /**
     * @param Setup $setup
     */
    protected function onDirsWriteSetStep($setup)
    {
        $setup->setNextStep($setup->getStep('STEP_FINISH'));
    }
}
