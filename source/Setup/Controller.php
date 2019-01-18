<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

use Exception;
use \OxidEsales\Facts\Edition\EditionSelector;
use \OxidEsales\Eshop\Core\SystemRequirements;
use \OxidEsales\EshopCommunity\Setup\Controller\ModuleStateMapGenerator;
use \OxidEsales\EshopCommunity\Setup\Exception\CommandExecutionFailedException;
use \OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException;

/**
 * Class holds scripts (controllers) needed to perform shop setup steps
 */
class Controller extends Core
{
    /** @var \OxidEsales\EshopCommunity\Setup\View */
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
        $utilities = $this->getUtilitiesInstance();

        $eulaOptionValue = $utilities->getRequestVar("iEula", "post");
        $eulaOptionValue = (int)($eulaOptionValue ? $eulaOptionValue : $session->getSessionParam("eula"));
        if (!$eulaOptionValue) {
            $setup = $this->getSetupInstance();
            $setup->setNextStep($setup->getStep("STEP_WELCOME"));
            $view->setMessage($this->getLanguageInstance()->getText("ERROR_SETUP_CANCELLED"));

            throw new SetupControllerExitException("licenseerror.php");
        }

        $databaseConfigValues = $session->getSessionParam('aDB');
        $demodataPackageExists = $utilities->isDemodataPrepared();

        if (!isset($databaseConfigValues)) {
            // default values
            $databaseConfigValues['dbHost'] = "localhost";
            $databaseConfigValues['dbPort'] = "3306";
            $databaseConfigValues['dbUser'] = "";
            $databaseConfigValues['dbPwd'] = "";
            $databaseConfigValues['dbName'] = "";
            $databaseConfigValues['dbiDemoData'] = $demodataPackageExists ? 1 : 0;
        }

        $this->setViewOptions(
            'dbinfo.php',
            'STEP_3_TITLE',
            [
                "aDB" => $databaseConfigValues,
                "blMbStringOn" => $systemRequirements->getModuleInfo('mb_string'),
                "blUnicodeSupport" => $systemRequirements->getModuleInfo('unicode_support'),
                "demodataPackageExists" => $demodataPackageExists
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
        if ($this->userDecidedIgnoreDBWarning()) {
            $session->setSessionParam('blIgnoreDbRecommendations', true);
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
            } elseif (($exception->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS)) {
                $setup->setNextStep(null);
                $this->formMessageIfMySqyVersionIsNotRecommended($view, $language);
                $databaseExists = false;
                // check if DB is already UP and running
                if (!$this->databaseCanBeOverwritten($database)) {
                    $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
                    $databaseExists = true;
                }
                $this->formMessageIgnoreDbVersionNotRecommended($view, $language, $session->getSid(), $setup->getStep('STEP_DIRS_INFO'), $databaseExists);

                throw new SetupControllerExitException();
            } else {
                $this->ensureDatabasePresent($database, $databaseConfigValues['dbName']);
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
            if (($exception->getCode() === Database::ERROR_COULD_NOT_CREATE_DB) && $this->userDecidedIgnoreDBWarning()) {
                //User agreed to ignore SystemRequirements warning, database does not exist yet, create database.
                $this->ensureDatabasePresent($database, $databaseConfigValues['dbName']);
            } elseif (($exception->getCode() === Database::ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS)) {
                $setup->setNextStep(null);
                $this->formMessageIfMySqyVersionIsNotRecommended($view, $language);
                $databaseExists = false;
                // check if DB is already UP and running
                if (!$this->databaseCanBeOverwritten($database)) {
                    $this->formMessageIfDBCanBeOverwritten($databaseConfigValues['dbName'], $view, $language);
                    $databaseExists = true;
                }
                $this->formMessageIgnoreDbVersionNotRecommended($view, $language, $session->getSid(), $setup->getStep('STEP_DB_CREATE'), $databaseExists);

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
            $utilities = $this->getUtilitiesInstance();
            $demodataInstallationRequired = $databaseConfigValues['dbiDemoData'];

            if ($demodataInstallationRequired && !$utilities->isDemodataPrepared()) {
                throw new SetupControllerExitException($language->getText('ERROR_NO_DEMODATA_INSTALLED'));
            }

            // install demo/initial data
            try {
                $this->installShopData($database, $demodataInstallationRequired);
            } catch (CommandExecutionFailedException $exception) {
                $this->handleCommandExecutionFailedException($exception);

                throw new SetupControllerExitException();
            } catch (Exception $exception) {
                // there where problems with queries
                $view->setMessage($language->getText('ERROR_BAD_DEMODATA') . "<br><br>" . $exception->getMessage());

                throw new SetupControllerExitException();
            }
        } catch (Exception $exception) {
            $view->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }

        //update if send information to OXID / shop country config options (from first step)
        $database->saveShopSettings([]);

        // This value will not change, as it's deprecated and will be removed in next major version.
        $database->execSql("update `oxshops` set `oxversion` = '6.0.0'");

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
        $aSetupConfig = $session->getSessionParam("aSetupConfig");
        $aDB = $session->getSessionParam("aDB");

        try {
            $this->getUtilitiesInstance()->executeExternalRegenerateViewsCommand(); // move to last step possible?
        } catch (CommandExecutionFailedException $exception) {
            $this->handleCommandExecutionFailedException($exception);

            throw new SetupControllerExitException();
        } catch (Exception $exception) {
            $view = $this->getView();
            $view->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }

        $this->setViewOptions(
            'finish.php',
            'STEP_6_TITLE',
            [
                "aPath" => $pathCollection,
                "aSetupConfig" => $aSetupConfig,
                "aDB" => $aDB,
                "blWritableConfig" => is_writable($pathCollection['sShopDir'] . "/config.inc.php")
            ]
        );
    }

    /**
     * Returns View object
     *
     * @return \OxidEsales\EshopCommunity\Setup\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \OxidEsales\EshopCommunity\Setup\Setup $setup
     */
    protected function onDirsWriteSetStep($setup)
    {
        $setup->setNextStep($setup->getStep('STEP_FINISH'));
    }

    /**
     * Check if database can be safely overwritten.
     *
     * @param \OxidEsales\EshopCommunity\Setup\Database $database database instance used to connect to DB
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
     * Show warning-question if database with same name already exists.
     *
     * @param string                                    $databaseName name of database to check if exist
     * @param \OxidEsales\EshopCommunity\Setup\View     $view         to set parameters for template
     * @param \OxidEsales\EshopCommunity\Setup\Language $language     to translate text
     */
    private function formMessageIfDBCanBeOverwritten($databaseName, $view, $language)
    {
        $view->setMessage(sprintf($language->getText('ERROR_DB_ALREADY_EXISTS'), $databaseName));
    }

    /**
     * Show warning-question if MySQL version does meet minimal requirements, but is neither recommended nor supported.
     *
     * @param \OxidEsales\EshopCommunity\Setup\View     $view     to set parameters for template
     * @param \OxidEsales\EshopCommunity\Setup\Language $language to translate text
     */
    private function formMessageIfMySqyVersionIsNotRecommended($view, $language)
    {
        $view->setMessage(sprintf($language->getText('ERROR_MYSQL_VERSION_DOES_NOT_FIT_RECOMMENDATIONS')));
    }

    /**
     * Show a message and a link to continue installation process, not regarding errors and warnings
     *
     * @param \OxidEsales\EshopCommunity\Setup\View     $view      to set parameters for template
     * @param \OxidEsales\EshopCommunity\Setup\Language $language  to translate text
     * @param string                                    $sessionId
     * @param string                                    $setupStep where to redirect if chose to rewrite database
     */
    private function formMessageInstallAnyway($view, $language, $sessionId, $setupStep)
    {
        $view->setMessage("<br><br>" . $language->getText('STEP_4_2_OVERWRITE_DB') . " <a href=\"index.php?sid=" . $sessionId . "&istep=" . $setupStep . "&ow=1\" id=\"step3Continue\" style=\"text-decoration: underline;\">" . $language->getText('HERE') . "</a>");
    }

    /**
     * Show a message and a link to continue installation process, not regarding errors and warnings
     *
     * @param \OxidEsales\EshopCommunity\Setup\View     $view           to set parameters for template
     * @param \OxidEsales\EshopCommunity\Setup\Language $language       to translate text
     * @param string                                    $sessionId
     * @param string                                    $setupStep      where to redirect if chose to rewrite database
     * @param bool                                      $databaseExists Database already exists
     */
    private function formMessageIgnoreDbVersionNotRecommended($view, $language, $sessionId, $setupStep, $databaseExists)
    {
        $ignoreParam = $databaseExists ? '&ow=1&owrec=1' : '&owrec=1';
        $info = $databaseExists ? 'STEP_4_2_OVERWRITE_DB' : 'STEP_4_2_NOT_RECOMMENDED_MYSQL_VERSION';
        $view->setMessage("<br><br>" . $language->getText($info) . " <a href=\"index.php?sid=" . $sessionId . "&istep=" . $setupStep . $ignoreParam . "id=\"step3Continue\" style=\"text-decoration: underline;\">" . $language->getText('HERE') . "</a>");
    }

    /**
     * Installs demo data or initial, dependent on parameter
     *
     * @param \OxidEsales\EshopCommunity\Setup\Database $database
     * @param int                                       $demoDataRequired
     *
     * @throws SetupControllerExitException
     */
    private function installShopData($database, $demoDataRequired = 0)
    {
        $baseSqlDir = $this->getUtilitiesInstance()->getSqlDirectory(EditionSelector::COMMUNITY);

        try {
            // If demo data files are provided.
            if ($demoDataRequired && $this->getUtilitiesInstance()->isDemodataPrepared()) {
                $this->getUtilitiesInstance()->executeExternalDatabaseMigrationCommand();

                // Install demo data.
                $database->queryFile($this->getUtilitiesInstance()->getActiveEditionDemodataPackageSqlFilePath());
                // Copy demo data files.
                $this->getUtilitiesInstance()->executeExternalDemodataAssetsInstallCommand();
            } else {
                $database->queryFile("$baseSqlDir/initial_data.sql");

                $this->getUtilitiesInstance()->executeExternalDatabaseMigrationCommand();
            }
        } catch (Exception $exception) {
            $commandException = new CommandExecutionFailedException('Migration', $exception->getCode(), $exception);
            $commandException->setCommandOutput([$exception->getMessage()]);

            $this->handleCommandExecutionFailedException($commandException);

            throw new SetupControllerExitException();
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
     *
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

    /**
     * Ensure the database is available
     *
     * @throws \OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException
     *
     * @param \OxidEsales\EshopCommunity\Setup\Database $database
     * @param string                                    $dbName
     *
     * @throws SetupControllerExitException
     */
    private function ensureDatabasePresent($database, $dbName)
    {
        try {
            // if database is not there, try to create it
            $database->createDb($dbName);
        } catch (Exception $exception) {
            $setup = $this->getSetupInstance();
            $setup->setNextStep($setup->getStep('STEP_DB_INFO'));
            $this->getView()->setMessage($exception->getMessage());

            throw new SetupControllerExitException();
        }
        $this->getView()->setViewParam("blCreated", 1);
    }
}
