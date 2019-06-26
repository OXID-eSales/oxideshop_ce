<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

use Exception;

use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use \OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use \OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use \OxidEsales\Facts\Facts;
use \OxidEsales\Eshop\Core\Edition\EditionSelector;
use \OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\DemoDataInstaller\DemoDataInstallerBuilder;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Setup utilities class
 */
class Utilities extends Core
{
    const CONFIG_FILE_NAME = 'config.inc.php';

    const DEMODATA_PACKAGE_NAME = 'oxideshop-demodata-%s';

    const DEMODATA_PACKAGE_SOURCE_DIRECTORY = 'src';

    const DEMODATA_SQL_FILENAME = 'demodata.sql';
    const LICENSE_TEXT_FILENAME = "lizenz.txt";

    /**
     * Unable to find file
     *
     * @var int
     */
    const ERROR_COULD_NOT_FIND_FILE = 1;

    /**
     * File is not readable
     *
     * @var int
     */
    const ERROR_COULD_NOT_READ_FILE = 2;

    /**
     * File is not writable
     *
     * @var int
     */
    const ERROR_COULD_NOT_WRITE_TO_FILE = 3;

    /**
     * Email validation regular expression
     *
     * @var string
     */
    protected $_sEmailTpl = "/^([-!#\$%&'*+.\/0-9=?A-Z^_`a-z{|}~\177])+@([-!#\$%&'*+\/0-9=?A-Z^_`a-z{|}~\177]+\\.)+[a-zA-Z]{2,6}\$/i";


    /**
     * Generates unique id
     *
     * @return string
     */
    public function generateUID()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Recursively removes given path files and folders
     *
     * @param string $sPath           path to remove
     * @param bool   $blDeleteSuccess removal state marker
     * @param int    $iMode           remove mode: 0 files and folders, 1 - files only
     * @param array  $aSkipFiles      files which should not be deleted (default null)
     * @param array  $aSkipFolders    folders which should not be deleted (default null)
     *
     * @return bool
     */
    public function removeDir($sPath, $blDeleteSuccess, $iMode = 0, $aSkipFiles = [], $aSkipFolders = [])
    {
        if (is_file($sPath) || is_dir($sPath)) {
            // setting path to remove
            $d = dir($sPath);
            $d->handle;
            while (false !== ($sEntry = $d->read())) {
                if ($sEntry != "." && $sEntry != "..") {
                    $sFilePath = $sPath . "/" . $sEntry;
                    if (is_file($sFilePath)) {
                        if (!in_array(basename($sFilePath), $aSkipFiles)) {
                            $blDeleteSuccess = $blDeleteSuccess * @unlink($sFilePath);
                        }
                    } elseif (is_dir($sFilePath)) {
                        // removing direcotry contents
                        $this->removeDir($sFilePath, $blDeleteSuccess, $iMode, $aSkipFiles, $aSkipFolders);
                        if ($iMode === 0 && !in_array(basename($sFilePath), $aSkipFolders)) {
                            $blDeleteSuccess = $blDeleteSuccess * @rmdir($sFilePath);
                        }
                    } else {
                        // there are some other objects ?
                        $blDeleteSuccess = $blDeleteSuccess * false;
                    }
                }
            }
            $d->close();
        } else {
            $blDeleteSuccess = false;
        }

        return $blDeleteSuccess;
    }

    /**
     * Extracts install path
     *
     * @param array $aPath path info array
     *
     * @return string
     */
    protected function _extractPath($aPath)
    {
        $sExtPath = '';
        $blBuildPath = false;
        for ($i = count($aPath); $i > 0; $i--) {
            $sDir = $aPath[$i - 1];
            if ($blBuildPath) {
                $sExtPath = $sDir . '/' . $sExtPath;
            }
            if (stristr($sDir, EditionPathProvider::SETUP_DIRECTORY)) {
                $blBuildPath = true;
            }
        }

        return $sExtPath;
    }

    /**
     * Returns path parameters for standard setup (non APS)
     *
     * @return array
     */
    public function getDefaultPathParams()
    {
        // default values
        $aParams['sShopDir'] = "";
        $aParams['sShopURL'] = "";

        // try path translated
        if (isset($_SERVER['PATH_TRANSLATED']) && ($_SERVER['PATH_TRANSLATED'] != '')) {
            $sFilepath = $_SERVER['PATH_TRANSLATED'];
        } else {
            $sFilepath = $_SERVER['SCRIPT_FILENAME'];
        }

        $aParams['sShopDir'] = str_replace("\\", "/", $this->_extractPath(preg_split("/\\\|\//", $sFilepath)));
        $aParams['sCompileDir'] = $aParams['sShopDir'] . "tmp/";

        // try referer
        $sFilepath = @$_SERVER['HTTP_REFERER'];
        if (!isset($sFilepath) || !$sFilepath) {
            $sFilepath = "http://" . @$_SERVER['HTTP_HOST'] . @$_SERVER['SCRIPT_NAME'];
        }
        $aParams['sShopURL'] = ltrim($this->_extractPath(explode("/", $sFilepath)), "/");

        return $aParams;
    }

    /**
     * Updates config.inc.php file contents.
     *
     * @param array $parameters Configuration parameters as submitted by the user
     *
     * @throws Exception File can't be found, opened for reading or written.
     */
    public function updateConfigFile($parameters)
    {
        $configFile = '';
        $language = $this->getInstance("Language");

        if (isset($parameters['sShopDir'])) {
            $configFile = $parameters['sShopDir'] . "/config.inc.php";
        }
        $this->handleMissingConfigFileException($configFile);

        clearstatcache();
        // Make config file writable, as it may be write protected
        @chmod($configFile, getDefaultFileMode());
        if (!$configFileContent = file_get_contents($configFile)) {
            throw new Exception(sprintf($language->getText('ERROR_COULD_NOT_OPEN_CONFIG_FILE'), $configFile));
        }

        // overwriting settings
        foreach ($parameters as $configOption => $value) {
            $search = ["\'", "'" ];
            $replace = ["\\\'", "\'"];
            $escapedValue = str_replace($search, $replace, $value);
            $configFileContent = str_replace("<{$configOption}>", $escapedValue, $configFileContent);
        }

        if (!file_put_contents($configFile, $configFileContent)) {
            throw new Exception(sprintf($language->getText('ERROR_CONFIG_FILE_IS_NOT_WRITABLE'), $configFile));
        }
        // Make config file read-only, this is our recomnedation for config.inc.php
        @chmod($configFile, getDefaultConfigFileMode());
    }

    /**
     * Throws an exception in case config file is missing.
     *
     * This is necessary to suppress PHP warnings during Setup. With the help of exception this problem is
     * caught and displayed properly.
     *
     * @param string $pathToConfigFile File path to eShop configuration file.
     *
     * @throws Exception Config file is missing.
     */
    private function handleMissingConfigFileException($pathToConfigFile)
    {
        if (!file_exists($pathToConfigFile)) {
            $language = $this->getLanguageInstance();

            throw new Exception(sprintf($language->getText('ERROR_COULD_NOT_OPEN_CONFIG_FILE'), $pathToConfigFile));
        }
    }

    /**
     * Updates default htaccess file with user defined params
     *
     * @param array  $aParams    various setup parameters
     * @param string $sSubFolder in case you need to update non default, but e.g. admin file, you must add its folder
     *
     * @throws Exception when .htaccess file is not accessible/readable.
     */
    public function updateHtaccessFile($aParams, $sSubFolder = "")
    {
        /** @var \OxidEsales\EshopCommunity\Setup\Language $oLang */
        $oLang = $this->getInstance("Language");

        // preparing rewrite base param
        if (!isset($aParams["sBaseUrlPath"]) || !$aParams["sBaseUrlPath"]) {
            $aParams["sBaseUrlPath"] = "";
        }

        if ($sSubFolder) {
            $sSubFolder = $this->preparePath("/" . $sSubFolder);
        }

        $aParams["sBaseUrlPath"] = trim($aParams["sBaseUrlPath"] . $sSubFolder, "/");
        $aParams["sBaseUrlPath"] = "/" . $aParams["sBaseUrlPath"];

        $sHtaccessPath = $this->preparePath($aParams["sShopDir"]) . $sSubFolder . "/.htaccess";

        clearstatcache();
        if (!file_exists($sHtaccessPath)) {
            throw new Exception(sprintf($oLang->getText('ERROR_COULD_NOT_FIND_FILE'), $sHtaccessPath), Utilities::ERROR_COULD_NOT_FIND_FILE);
        }

        @chmod($sHtaccessPath, getDefaultFileMode());
        if (is_readable($sHtaccessPath) && ($fp = fopen($sHtaccessPath, "r"))) {
            $sHtaccessFile = fread($fp, filesize($sHtaccessPath));
            fclose($fp);
        } else {
            throw new Exception(sprintf($oLang->getText('ERROR_COULD_NOT_READ_FILE'), $sHtaccessPath), Utilities::ERROR_COULD_NOT_READ_FILE);
        }

        // overwriting settings
        $sHtaccessFile = preg_replace("/RewriteBase.*/", "RewriteBase " . $aParams["sBaseUrlPath"], $sHtaccessFile);
        if (is_writable($sHtaccessPath) && ($fp = fopen($sHtaccessPath, "w"))) {
            fwrite($fp, $sHtaccessFile);
            fclose($fp);
        } else {
            // error ? strange !?
            throw new Exception(sprintf($oLang->getText('ERROR_COULD_NOT_WRITE_TO_FILE'), $sHtaccessPath), Utilities::ERROR_COULD_NOT_WRITE_TO_FILE);
        }
    }

    /**
     * Returns true if htaccess file can be updated by setup process.
     *
     * Functionality is tested via:
     *   `Acceptance/Frontend/ShopSetUpTest.php::testInstallShopCantContinueDueToHtaccessProblem`
     *
     * @return bool
     */
    public function canHtaccessFileBeUpdated()
    {
        try {
            $defaultPathParameters = $this->getDefaultPathParams();
            $defaultPathParameters['sBaseUrlPath'] = $this->extractRewriteBase($defaultPathParameters['sShopURL']);
            $this->updateHtaccessFile($defaultPathParameters);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Returns the value of an environment variable
     *
     * @param string $sVarName variable name
     *
     * @return mixed
     */
    public function getEnvVar($sVarName)
    {
        if (($sVarVal = getenv($sVarName)) !== false) {
            return $sVarVal;
        }
    }

    /**
     * Returns variable from request
     *
     * @param string $sVarName     variable name
     * @param string $sRequestType request type - "post", "get", "cookie" [optional]
     *
     * @return mixed
     */
    public function getRequestVar($sVarName, $sRequestType = null)
    {
        $sValue = null;
        switch ($sRequestType) {
            case 'post':
                if (isset($_POST[$sVarName])) {
                    $sValue = $_POST[$sVarName];
                }
                break;
            case 'get':
                if (isset($_GET[$sVarName])) {
                    $sValue = $_GET[$sVarName];
                }
                break;
            case 'cookie':
                if (isset($_COOKIE[$sVarName])) {
                    $sValue = $_COOKIE[$sVarName];
                }
                break;
            default:
                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'post');
                }

                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'get');
                }

                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'cookie');
                }
                break;
        }

        return $sValue;
    }

    /**
     * Sets cookie
     *
     * @param string $sName       name of the cookie
     * @param string $sValue      value of the cookie
     * @param int    $iExpireDate time the cookie expires
     * @param string $sPath       path on the server in which the cookie will be available on.
     */
    public function setCookie($sName, $sValue, $iExpireDate, $sPath)
    {
        setcookie($sName, $sValue, $iExpireDate, $sPath);
    }

    /**
     * Returns file contents if file is readable
     *
     * @param string $sFile path to file
     *
     * @return string | mixed
     */
    public function getFileContents($sFile)
    {
        $sContents = null;
        if (file_exists($sFile) && is_readable($sFile)) {
            $sContents = file_get_contents($sFile);
        }

        return $sContents;
    }

    /**
     * Prepares given path parameter to ce compatible with unix format
     *
     * @param string $sPath path to prepare
     *
     * @return string
     */
    public function preparePath($sPath)
    {
        return rtrim(str_replace("\\", "/", $sPath), "/");
    }

    /**
     * Extracts rewrite base path from url
     *
     * @param string $sUrl user defined shop install url
     *
     * @return string
     */
    public function extractRewriteBase($sUrl)
    {
        $sPath = "/";
        if (($aPathInfo = @parse_url($sUrl)) !== false) {
            if (isset($aPathInfo["path"])) {
                $sPath = $this->preparePath($aPathInfo["path"]);
            }
        }

        return $sPath;
    }

    /**
     * Email validation function. Returns true if email is OK otherwise - false;
     * Syntax validation is performed only.
     *
     * @param string $sEmail user email
     *
     * @return bool
     */
    public function isValidEmail($sEmail)
    {
        return preg_match($this->_sEmailTpl, $sEmail) != 0;
    }

    /**
     * Calls external database views regeneration command.
     *
     * @return bool Was the call a success?
     */
    public function executeExternalRegenerateViewsCommand()
    {
        $ViewsGenerator = new ViewsGenerator();

        return $ViewsGenerator->generate();
    }

    /**
     * Calls external database migration command.
     *
     * @param ConsoleOutput $output Add a possibility to provide a custom output handler.
     * @param Facts|null    $facts  A possible facts mock
     */
    public function executeExternalDatabaseMigrationCommand(ConsoleOutput $output = null, Facts $facts = null)
    {
        $migrations = $this->createMigrations($facts);
        $migrations->setOutput($output);

        $command = Migrations::MIGRATE_COMMAND;

        $migrations->execute($command);
    }

    /**
     * Calls external demodata assets install command.
     *
     * @return int Error code of the install command.
     */
    public function executeExternalDemodataAssetsInstallCommand()
    {
        $demoDataInstaller = $this->createDemoDataInstaller();

        return $demoDataInstaller->execute();
    }

    /**
     * Return path to composer vendor directory.
     *
     * @return string
     */
    private function getVendorDirectory()
    {
        return VENDOR_PATH;
    }

    /**
     * Check if database is up and running
     *
     * @param  Database $database
     * @return bool
     */
    public function checkDbExists($database)
    {
        try {
            $databaseExists = true;
            $database->execSql("select * from oxconfig");
        } catch (Exception $exception) {
            $databaseExists = false;
        }

        return $databaseExists;
    }

    /**
     * Get specific edition sql directory
     *
     * @param null|string $edition
     * @return string
     */
    public function getSqlDirectory($edition = null)
    {
        $editionPathSelector = $this->getEditionPathProvider($edition);
        return $editionPathSelector->getDatabaseSqlDirectory();
    }

    /**
     * Get setup directory
     *
     * @return string
     */
    public function getSetupDirectory()
    {
        $editionPathSelector = $this->getEditionPathProvider();
        return $editionPathSelector->getSetupDirectory();
    }

    /**
     * @param string $edition
     * @return EditionPathProvider
     */
    private function getEditionPathProvider($edition = null)
    {
        $editionPathSelector = new EditionRootPathProvider(new EditionSelector($edition));
        return new EditionPathProvider($editionPathSelector);
    }

    /**
     * Check if demodata package is installed.
     *
     * @return bool
     */
    public function isDemodataPrepared()
    {
        $demodataSqlFile = $this->getActiveEditionDemodataPackageSqlFilePath();
        return file_exists($demodataSqlFile) ? true : false;
    }

    /**
     * Return full path to `demodata.sql` file of demodata package based on current eShop edition.
     *
     * @return string
     */
    public function getActiveEditionDemodataPackageSqlFilePath()
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $this->getActiveEditionDemodataPackagePath(),
                self::DEMODATA_PACKAGE_SOURCE_DIRECTORY,
                self::DEMODATA_SQL_FILENAME,
            ]
        );
    }

    /**
     * Return full path to demodata package based on current eShop edition.
     *
     * @return string
     */
    public function getActiveEditionDemodataPackagePath()
    {
        $facts = new Facts();

        return $this->getVendorDirectory()
            . EditionRootPathProvider::EDITIONS_DIRECTORY
            . DIRECTORY_SEPARATOR
            . sprintf(self::DEMODATA_PACKAGE_NAME, strtolower($facts->getEdition()));
    }

    /**
     * Returns the contents of license agreement in requested language.
     *
     * @param string $languageId
     * @return string
     */
    public function getLicenseContent($languageId)
    {
        $licensePathElements = [
            $this->getSetupDirectory(),
            ucfirst($languageId),
            self::LICENSE_TEXT_FILENAME
        ];
        $licensePath = implode(DIRECTORY_SEPARATOR, $licensePathElements);

        $licenseContent = $this->getFileContents($licensePath);

        return $licenseContent;
    }

    /**
     * Removes any ANSI control codes from command output.
     *
     * @param string $outputWithAnsiControlCodes
     * @return string
     */
    public static function stripAnsiControlCodes($outputWithAnsiControlCodes)
    {
        return preg_replace('/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/', "", $outputWithAnsiControlCodes);
    }

    /**
     * @param Facts $facts The facts object to use for the creation of the migrations.
     *
     * @return Migrations
     */
    protected function createMigrations(Facts $facts = null)
    {
        $migrationsBuilder = new MigrationsBuilder();

        return $migrationsBuilder->build($facts);
    }

    /**
     * @return \OxidEsales\DemoDataInstaller\DemoDataInstaller
     */
    protected function createDemoDataInstaller()
    {
        $demoDataInstallerBuilder = new DemoDataInstallerBuilder();

        return $demoDataInstallerBuilder->build();
    }
}
