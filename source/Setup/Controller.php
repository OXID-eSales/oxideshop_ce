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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Setup;

use Exception;
use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\Eshop\Core\SystemRequirements;
use OxidEsales\EshopCommunity\Setup\Controller\ModuleStateMapGenerator;
use OxidEsales\EshopCommunity\Setup\Exception\CommandExecutionFailedException;
use OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException;

/**
 * Class holds scripts (controllers) needed to perform shop setup steps
 */
class Controller extends Core
{
    /** @var View */
    private $view = null;

    /**
     * Controller constructor
     */
    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * First page with system requirements check
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSystemRequirementsPageCanContinueWithSetup`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSystemRequirementsPageShowsTranslatedModuleNames`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSystemRequirementsPageShowsTranslatedModuleGroupNames`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSystemRequirementsContainsProperModuleStateHtmlClassNames`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testInstallShopCantContinueDueToHtaccessProblem`
     */
    public function systemReq()
    {
        $systemRequirementsInfo = $this->getSystemRequirementsInfo();
        $moduleStateMapGenerator = $this->getModuleStateMapGenerator($systemRequirementsInfo);

        $moduleStateMap = $moduleStateMapGenerator->getModuleStateMap();
        $isSafeForSetupToContinue = SystemRequirements::canSetupContinue($systemRequirementsInfo);

        $this->setViewOptions(
            'systemreq.php',
            'STEP_0_TITLE',
            [
                "blContinue" => $isSafeForSetupToContinue,
                "aGroupModuleInfo" => $moduleStateMap,
                "aLanguages" => getLanguages(),
                "sLanguage" => $this->getSessionInstance()->getSessionParam('setup_lang'),
            ]
        );
    }

    /**
     * Welcome page
     */
    public function welcome()
    {
        $session = $this->getSessionInstance();

        //setting admin area default language
        $adminLanguage = $session->getSessionParam('setup_lang');
        $this->getUtilitiesInstance()->setCookie("oxidadminlanguage", $adminLanguage, time() + 31536000, "/");

        $this->setViewOptions(
            'welcome.php',
            'STEP_1_TITLE',
            [
                "aCountries" => getCountryList(),
                "aLocations" => getLocation(),
                "aLanguages" => getLanguages(),
                "sShopLang" => $session->getSessionParam('sShopLang'),
                "sLanguage" => $this->getLanguageInstance()->getLanguage(),
                "sLocationLang" => $session->getSessionParam('location_lang'),
                "sCountryLang" => $session->getSessionParam('country_lang')
            ]
        );
    }

    /**
     * License confirmation page
     */
    public function license()
    {
        $languageId = $this->getLanguageInstance()->getLanguage();
        $utils = $this->getUtilitiesInstance();

        $this->setViewOptions(
            'license.php',
            'STEP_2_TITLE',
            [
                "aLicenseText" => $utils->getLicenseContent($languageId)
            ]
        );
    }

    /**
     * DB info entry page
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToWelcomeScreenInCaseLicenseIsNotCheckedAsAgreed`
     */
    public function dbInfo()
    {
        $view = $this->getView();
        $session = $this->getSessionInstance();
        $systemRequirements = getSystemReqCheck();

        $eulaOptionValue = $this->getUtilitiesInstance()->getRequestVar("iEula", "post");
        $eulaOptionValue = (int)($eulaOptionValue ? $eulaOptionValue : $session->getSessionParam("eula"));
        if (!$eulaOptionValue) {
            $setup = $this->getSetupInstance();
            $setup->setNextStep($setup->getStep("STEP_WELCOME"));
            $view->setMessage($this->getLanguageInstance()->getText("ERROR_SETUP_CANCELLED"));

            throw new SetupControllerExitException("licenseerror.php");
        }

        $databaseConfigValues = $session->getSessionParam('aDB');
        if (!isset($databaseConfigValues)) {
            // default values
            $databaseConfigValues['dbHost'] = "localhost";
            $databaseConfigValues['dbUser'] = "";
            $databaseConfigValues['dbPwd'] = "";
            $databaseConfigValues['dbName'] = "";
            $databaseConfigValues['dbiDemoData'] = 1;
        }

        $this->setViewOptions(
            'dbinfo.php',
            'STEP_3_TITLE',
            [
                "aDB" => $databaseConfigValues,
                "blMbStringOn" => $systemRequirements->getModuleInfo('mb_string'),
                "blUnicodeSupport" => $systemRequirements->getModuleInfo('unicode_support')
            ]
        );
    }

    /**
     * Setup paths info entry page
     */
    public function dirsInfo()
    {
        $session = $this->getSessionInstance();
        $setup = $this->getSetupInstance();

        if ($this->userDecidedOverwriteDB()) {
            $session->setSessionParam('blOverwrite', true);
        }

        $this->setViewOptions(
            'dirsinfo.php',
            'STEP_4_TITLE',
            [
                "aAdminData" => $session->getSessionParam('aAdminData'),
                "aPath" => $this->getUtilitiesInstance()->getDefaultPathParams(),
                "aSetupConfig" => ["blDelSetupDir" => $setup->deleteSetupDirectory()],
            ]
        );
    }

    /**
     * Testing database connection
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenNotAllFieldsAreFilled`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenDatabaseUserDoesNotHaveAccess`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenDatabaseUserIsValidButCantCreateDatabase`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testUserIsNotifiedIfAValidDatabaseAlreadyExistsBeforeTryingToOverwriteIt`
     */
    public function dbConnect()
    {
        $setup = $this->getSetupInstance();
        $session = $this->getSessionInstance();
        $language = $this->getLanguageInstance();

        $view = $this->getView();
        $view->setTitle('STEP_3_1_TITLE');

        $databaseConfigValues = $this->getUtilitiesInstance()->getRequestVar("aDB", "post");
        $session->setSessionParam('aDB', $databaseConfigValues);

        // check if important parameters are set
        if (!$databaseConfigValues['dbHost'] || !$databaseConfigValues['dbName']) {
            $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
            $view->setMessage($language->getText('ERROR_FILL_ALL_FIELDS'));

            throw new SetupControllerExitException();
        }

        try {
            // ok check DB Connection
            $database = $this->getDatabaseInstance();
            $database->openDatabase($databaseConfigValues);
        } catch (Exception $exception) {
            if ($exception->getCode() === Database::ERROR_DB_CONNECT) {
                $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
                $view->setMessage($language->getText('ERROR_DB_CONNECT') . " - " . $exception->getMessage());

                throw new SetupControllerExitException();
            } elseif ($exception->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS) {
                $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
                $view->setMessage($exception->getMessage());

                throw new SetupControllerExitException();
            } elseif ($exception->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS) {
                $setup->setNextStep(null);
                $this->formMessageIfMySqyVersionIsNotRecommended($view, $language);
                // check if DB is already UP and running
                if (!$this->databaseCanBeOverwritten($databaseConfigValues)) {
                    $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
                }
                $this->formMessageInstallAnyway($view, $language, $session->getSid(), $setup->getStep('STEP_DIRS_INFO'));

                throw new SetupControllerExitException();
            } else {
                try {
                    // if database is not there, try to create it
                    $database->createDb($databaseConfigValues['dbName']);
                } catch (Exception $exception) {
                    $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
                    $view->setMessage($exception->getMessage());

                    throw new SetupControllerExitException();
                }
                $view->setViewParam("blCreated", 1);
            }
        }

        $view->setViewParam("aDB", $databaseConfigValues);

        // check if DB is already UP and running
        if (!$this->databaseCanBeOverwritten($database)) {
            $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
            $this->formMessageInstallAnyway($view, $language, $session->getSid(), $setup->getStep('STEP_DIRS_INFO'));

            throw new SetupControllerExitException();
        }

        $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));

        $this->view->setTemplateFileName("dbconnect.php");
    }

    /**
     * Creating database
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenDatabaseUserIsValidButCantCreateDatabase`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testUserIsNotifiedIfAValidDatabaseAlreadyExistsBeforeTryingToOverwriteIt`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenSetupSqlFileIsMissing`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDatabaseEntryPageWhenSetupSqlFileHasSyntaxError`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupShowsErrorMessageWhenMigrationFileContainsSyntaxErrors`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupShowsErrorMessageWhenMigrationExecutableIsMissing`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupShowsErrorMessageWhenViewRegenerationReturnsErrorCode`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupShowsErrorMessageWhenViewsRegenerationExecutableIsMissing`
     */
    public function dbCreate()
    {
        $setup = $this->getSetupInstance();
        $session = $this->getSessionInstance();
        $language = $this->getLanguageInstance();

        $view = $this->getView();
        $view->setTitle('STEP_4_2_TITLE');

        $databaseConfigValues = $session->getSessionParam('aDB');

        try {
            $database = $this->getDatabaseInstance();
            $database->openDatabase($databaseConfigValues);
        } catch (Exception $exception) {
            if ($exception->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS) {
                $setup->setNextStep(null);
                $this->formMessageIfMySqyVersionIsNotRecommended($view, $language);
                // check if DB is already UP and running
                if (!$this->databaseCanBeOverwritten($database)) {
                    $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
                }
                $this->formMessageInstallAnyway($view, $language, $session->getSid(), $setup->getStep('STEP_DB_CREATE'));

                throw new SetupControllerExitException();
            } else {
                $setup->setNextStep($setup->getStep('STEP_DB_CREATE'));
                $view->setMessage($exception->getMessage());

                throw new SetupControllerExitException();
            }
        }

        // testing if Views can be created
        try {
            $database->testCreateView();
        } catch (Exception $exception) {
            // Views can not be created
            $view->setMessage($exception->getMessage());
            $setup->setNextStep($setup->getStep('STEP_DB_INFO'));

            throw new SetupControllerExitException();
        }

        // check if DB is already UP and running
        if (!$this->databaseCanBeOverwritten($database)) {
            $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
            $this->formMessageInstallAnyway($view, $language, $session->getSid(), $setup->getStep('STEP_DB_CREATE'));

            throw new SetupControllerExitException();
        }

        try {
            $baseSqlDir = $this->getUtilitiesInstance()->getSqlDirectory(EditionSelector::COMMUNITY);
            $database->queryFile("$baseSqlDir/database_schema.sql");

            // install demo/initial data
            try {
                $this->installShopData($database, $databaseConfigValues['dbiDemoData']);
            } catch (CommandExecutionFailedException $exception) {
                $this->handleCommandExecutionFailedException($exception);
                throw new SetupControllerExitException();
            } catch (Exception $exception) {
                // there where problems with queries
                $view->setMessage($language->getText('ERROR_BAD_DEMODATA') . "<br><br>" . $exception->getMessage());

                throw new SetupControllerExitException();
            }

            try {
                $this->getUtilitiesInstance()->executeExternalRegenerateViewsCommand();
            } catch (CommandExecutionFailedException $exception) {
                $this->handleCommandExecutionFailedException($exception);

                throw new SetupControllerExitException();
            }
        } catch (Exception $exception) {
            $view->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }

        //update dyn pages / shop country config options (from first step)
        $database->saveShopSettings(array());

        try {
            $adminData = $session->getSessionParam('aAdminData');
            // creating admin user
            $database->writeAdminLoginData($adminData['sLoginName'], $adminData['sPassword']);
        } catch (Exception $exception) {
            $view->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }

        $view->setMessage($language->getText('STEP_4_2_UPDATING_DATABASE'));
        $this->onDirsWriteSetStep($setup);
    }

    /**
     * Writing config info
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDirInfoEntryPageWhenNotAllFieldsAreFilled`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDirInfoEntryPageWhenPasswordIsTooShort`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDirInfoEntryPageWhenPasswordDoesNotMatch`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDirInfoEntryPageWhenInvalidEmailUsed`
     *   `Acceptance/Frontend/ShopSetUpTest.php::testSetupRedirectsToDirInfoEntryPageWhenSetupCantFindConfigFile`
     */
    public function dirsWrite()
    {
        $view = $this->getView();
        $setup = $this->getSetupInstance();
        $session = $this->getSessionInstance();
        $language = $this->getLanguageInstance();
        $utils = $this->getUtilitiesInstance();

        $view->setTitle('STEP_4_1_TITLE');

        $pathCollection = $utils->getRequestVar("aPath", "post");
        $setupConfig = $utils->getRequestVar("aSetupConfig", "post");
        $adminData = $utils->getRequestVar("aAdminData", "post");

        // correct them
        $pathCollection['sShopURL'] = $utils->preparePath($pathCollection['sShopURL']);
        $pathCollection['sShopDir'] = $utils->preparePath($pathCollection['sShopDir']);
        $pathCollection['sCompileDir'] = $utils->preparePath($pathCollection['sCompileDir']);
        $pathCollection['sBaseUrlPath'] = $utils->extractRewriteBase($pathCollection['sShopURL']);

        // using same array to pass additional setup variable
        if (isset($setupConfig['blDelSetupDir']) && $setupConfig['blDelSetupDir']) {
            $setupConfig['blDelSetupDir'] = 1;
        } else {
            $setupConfig['blDelSetupDir'] = 0;
        }

        $session->setSessionParam('aPath', $pathCollection);
        $session->setSessionParam('aSetupConfig', $setupConfig);
        $session->setSessionParam('aAdminData', $adminData);

        // check if important parameters are set
        if (!$pathCollection['sShopURL'] || !$pathCollection['sShopDir'] || !$pathCollection['sCompileDir']
            || !$adminData['sLoginName'] || !$adminData['sPassword'] || !$adminData['sPasswordConfirm']
        ) {
            $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));
            $view->setMessage($language->getText('ERROR_FILL_ALL_FIELDS'));

            throw new SetupControllerExitException();
        }

        // check if passwords match
        if (strlen($adminData['sPassword']) < 6) {
            $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));
            $view->setMessage($language->getText('ERROR_PASSWORD_TOO_SHORT'));

            throw new SetupControllerExitException();
        }

        // check if passwords match
        if ($adminData['sPassword'] != $adminData['sPasswordConfirm']) {
            $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));
            $view->setMessage($language->getText('ERROR_PASSWORDS_DO_NOT_MATCH'));

            throw new SetupControllerExitException();
        }

        // check if email matches pattern
        if (!$utils->isValidEmail($adminData['sLoginName'])) {
            $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));
            $view->setMessage($language->getText('ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN'));

            throw new SetupControllerExitException();
        }

        // write it now
        try {
            $parameters = array_merge(( array )$session->getSessionParam('aDB'), $pathCollection);

            // updating config file
            $utils->updateConfigFile($parameters);

            // updating regular htaccess file
            $utils->updateHtaccessFile($parameters);

            // updating admin htaccess file
            //$oUtils->updateHtaccessFile( $aParams, "admin" );
        } catch (Exception $exception) {
            $setup->setNextStep($setup->getStep('STEP_DIRS_INFO'));
            $view->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }

        $view->setMessage($language->getText('STEP_4_1_DATA_WAS_WRITTEN'));
        $view->setViewParam("aPath", $pathCollection);
        $view->setViewParam("aSetupConfig", $setupConfig);

        $databaseConfigValues = $session->getSessionParam('aDB');
        $view->setViewParam("aDB", $databaseConfigValues);
        $setup->setNextStep($setup->getStep('STEP_DB_CREATE'));
    }

    /**
     * Final setup step
     */
    public function finish()
    {
        $session = $this->getSessionInstance();
        $pathCollection = $session->getSessionParam("aPath");

        $this->setViewOptions(
            'finish.php',
            'STEP_6_TITLE',
            [
                "aPath" => $pathCollection,
                "aSetupConfig" => $session->getSessionParam("aSetupConfig"),
                "blWritableConfig" => is_writable($pathCollection['sShopDir'] . "/config.inc.php")
            ]
        );
    }

    /**
     * Returns View object
     *
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param Setup $setup
     */
    protected function onDirsWriteSetStep($setup)
    {
        $setup->setNextStep($setup->getStep('STEP_FINISH'));
    }

    /**
     * Check if database can be safely overwritten.
     *
     * @param Database $database database instance used to connect to DB
     *
     * @return bool
     */
    private function databaseCanBeOverwritten($database)
    {
        $canBeOverwritten = true;

        if (!$this->userDecidedOverwriteDB()) {
            $canBeOverwritten = !$this->getUtilitiesInstance()->checkDbExists($database);
        }

        return $canBeOverwritten;
    }

    /**
     * Return if user already decided to overwrite database.
     *
     * @return bool
     */
    private function userDecidedOverwriteDB()
    {
        $userDecidedOverwriteDatabase = false;

        $overwriteCheck = $this->getUtilitiesInstance()->getRequestVar("ow", "get");
        $session = $this->getSessionInstance();

        if (isset($overwriteCheck) || $session->getSessionParam('blOverwrite')) {
            $userDecidedOverwriteDatabase = true;
        }

        return $userDecidedOverwriteDatabase;
    }

    /**
     * Show warning-question if database with same name already exists.
     *
     * @param string   $databaseName name of database to check if exist
     * @param View     $view         to set parameters for template
     * @param Language $language     to translate text
     */
    private function formMessageIfDBCanBeOverwritten($databaseName, $view, $language)
    {
        $view->setMessage(sprintf($language->getText('ERROR_DB_ALREADY_EXISTS'), $databaseName));
    }

    /**
     * Show warning-question if MySQL version does meet minimal requirements, but is neither recommended nor supported.
     *
     * @param View     $view     to set parameters for template
     * @param Language $language to translate text
     */
    private function formMessageIfMySqyVersionIsNotRecommended($view, $language)
    {
        $view->setMessage(sprintf($language->getText('ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS')));
    }

    /**
     * Show a message and a link to continue installation process, not regarding errors and warnings
     *
     * @param View     $view      to set parameters for template
     * @param Language $language  to translate text
     * @param string   $sessionId
     * @param string   $setupStep where to redirect if chose to rewrite database
     */
    private function formMessageInstallAnyway($view, $language, $sessionId, $setupStep)
    {
        $view->setMessage("<br><br>" . $language->getText('STEP_4_2_NOT_RECOMMENDED_MYSQL_VERSION') . " <a href=\"index.php?sid=" . $sessionId . "&istep=" . $setupStep . "&ow=1\" id=\"step3Continue\" style=\"text-decoration: underline;\">" . $language->getText('HERE') . "</a>");
    }

    /**
     * Installs demodata or initial, dependent on parameter
     *
     * @param Database $database
     * @param int      $demodata
     */
    private function installShopData($database, $demodata = 0)
    {
        $editionSqlDir = $this->getUtilitiesInstance()->getSqlDirectory();
        $baseSqlDir = $this->getUtilitiesInstance()->getSqlDirectory(EditionSelector::COMMUNITY);

        // If demodata files are provided.
        if ($this->getUtilitiesInstance()->checkIfDemodataPrepared($demodata)) {
            $this->getUtilitiesInstance()->executeExternalDatabaseMigrationCommand();

            // Install demo data.
            $database->queryFile($this->getUtilitiesInstance()->getActiveEditionDemodataPackageSqlFilePath());
            // Copy demodata files.
            $this->getUtilitiesInstance()->executeExternalDemodataAssetsInstallCommand();
        } else {
            $database->queryFile("$baseSqlDir/initial_data.sql");

            $this->getUtilitiesInstance()->executeExternalDatabaseMigrationCommand();

            if ($demodata) {
                $database->queryFile("$editionSqlDir/demodata.sql");
            }
        }
    }

    /**
     * Allows to set all necessary view information with single method call.
     *
     * @param string $templateFileName File name of template which will be used to pass in the context data.
     * @param string $titleId          Title Id which will be used in the template.
     * @param array  $viewOptions      An array containing all view elements to be used inside a template.
     */
    protected function setViewOptions($templateFileName, $titleId, $viewOptions)
    {
        $view = $this->getView();
        $view->setTemplateFileName($templateFileName);
        $view->setTitle($titleId);

        foreach ($viewOptions as $optionKey => $optionValue) {
            $view->setViewParam($optionKey, $optionValue);
        }
    }

    /**
     * Getter for ModuleStateMapGenerator.
     *
     * Returns an instance of ModuleStateMapGenerator which has all necessary functions predefined:
     *
     *   - StateHtmlClassConverterFunction to convert module state to HTML class attribute for setup page;
     *   - ModuleNameTranslateFunction to translate requirement module id to it's full name;
     *   - ModuleGroupNameTranslateFunction to translate requirement module group id to it's full name.
     *
     * @param array $systemRequirementsInfo
     *
     * @return ModuleStateMapGenerator
     */
    private function getModuleStateMapGenerator($systemRequirementsInfo)
    {
        $setup = $this->getSetupInstance();
        $language = $this->getLanguageInstance();

        $moduleStateMapGenerator = new Controller\ModuleStateMapGenerator($systemRequirementsInfo);

        $moduleStateMapGenerator->setModuleStateHtmlClassConvertFunction(function ($moduleState) use ($setup) {
            return $setup->getModuleClass($moduleState);
        });
        $moduleStateMapGenerator->setModuleNameTranslateFunction(function ($moduleId) use ($language) {
            return $language->getModuleName($moduleId);
        });
        $moduleStateMapGenerator->setModuleGroupNameTranslateFunction(function ($moduleGroupId) use ($language) {
            return $language->getModuleName($moduleGroupId);
        });

        return $moduleStateMapGenerator;
    }

    /**
     * Get updated array in the same format as provided by `SystemRequirements::getSystemInfo`.
     *
     * @return array Updated SystemRequirementsInfo array.
     */
    private function getSystemRequirementsInfo()
    {
        $systemRequirements = getSystemReqCheck();

        return $this->updateSystemRequirementsInfo(
            $systemRequirements->getSystemInfo()
        );
    }

    /**
     * Modify given array of format `SystemRequirements::getSystemInfo` with exceptional cases.
     *
     * ATM it is a bit tricky to include these changes due to the way SystemRequirements are constructed.
     *
     * @param array $systemRequirementsInfo An array taken from `SystemRequirements::getSystemInfo`.
     *
     * @return array An array in the same format as provided in `SystemRequirements::getSystemInfo`.
     */
    private function updateSystemRequirementsInfo($systemRequirementsInfo)
    {
        return SystemRequirements::filter(
            $systemRequirementsInfo,
            function ($groupId, $moduleId, $moduleState) {
                // HtAccess check exception case
                if (($groupId === SystemRequirements::MODULE_GROUP_ID_SERVER_CONFIG)
                    && ($moduleId === SystemRequirements::MODULE_ID_MOD_REWRITE)
                    && (!$this->canUpdateHtaccess())
                ) {
                    return SystemRequirements::MODULE_STATUS_BLOCKS_SETUP;
                }

                // MySql version detect exception case
                // More information can be obtained from commits with tag 'ESDEV-3999'
                if (($groupId === SystemRequirements::MODULE_GROUP_ID_SERVER_CONFIG)
                    && ($moduleId === SystemRequirements::MODULE_ID_MYSQL_VERSION)
                ) {
                    return SystemRequirements::MODULE_STATUS_UNABLE_TO_DETECT;
                }

                return $moduleState;
            }
        );
    }

    /**
     * Check if htaccess file can be updated.
     *
     * @return bool Returns true in case htaccess file can be updated.
     */
    private function canUpdateHtaccess()
    {
        $utilities = $this->getUtilitiesInstance();
        return $utilities->canHtaccessFileBeUpdated();
    }

    /**
     * @param CommandExecutionFailedException $exception
     */
    private function handleCommandExecutionFailedException($exception)
    {
        $language = $this->getLanguageInstance();
        $view = $this->getView();

        $commandOutput = $exception->getCommandOutput();
        $htmlCommandOutput = $this->convertCommandOutputToHtmlOutput($commandOutput);

        $errorLines[] = sprintf(
            $language->getText('EXTERNAL_COMMAND_ERROR_1'),
            $exception->getCommand(),
            $exception->getReturnCode()
        );
        $errorLines[] = $language->getText('EXTERNAL_COMMAND_ERROR_2');

        $errorHeader = implode("<br />", $errorLines);
        $errorMessage = implode("<br /><br />", [$errorHeader, $htmlCommandOutput]);

        $view->setMessage($errorMessage);
    }

    /**
     * @param string $commandOutput
     * @return string
     */
    private function convertCommandOutputToHtmlOutput($commandOutput)
    {
        $commandOutput = Utilities::stripAnsiControlCodes($commandOutput);
        $commandOutput = htmlspecialchars($commandOutput);
        $commandOutput = str_replace("\n", "<br />", $commandOutput);
        $commandOutput = "<span style=\"font-family: courier,serif\">$commandOutput</span>";

        return $commandOutput;
    }
}
