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

use \OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use \OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use \OxidEsales\Facts\Facts;
use \OxidEsales\EshopCommunity\Setup\Exception\CommandExecutionFailedException;
use \OxidEsales\DoctrineMigrationWrapper\Migrations;

/**
 * Setup utilities class
 */
class Utilities extends Core
{
    const CONFIG_FILE_NAME = 'config.inc.php';

    const DEMODATA_PACKAGE_NAME = 'oxideshop-demodata-%s';

    const DEMODATA_PACKAGE_SOURCE_DIRECTORY = 'src';
    const COMPOSER_VENDOR_BIN_DIRECTORY = 'bin';

    const DEMODATA_SQL_FILENAME = 'demodata.sql';
    const LICENSE_TEXT_FILENAME = "lizenz.txt";

    const DATABASE_VIEW_REGENERATION_BINARY_FILENAME = 'oe-eshop-db_views_generate';
    const DATABASE_MIGRATION_BINARY_FILENAME = 'oe-eshop-doctrine_migration';
    const DEMODATA_ASSETS_INSTALL_BINARY_FILENAME = 'oe-eshop-demodata_install';

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
     * @param array $aParams paths parameters
     *
     * @throws Exception File can't be found, opened for reading or written.
     */
    public function updateConfigFile($aParams)
    {
        $sConfPath = $aParams['sShopDir'] . "/config.inc.php";

        $oLang = $this->getInstance("Language");

        $this->handleMissingConfigFileException($sConfPath);

        clearstatcache();
        @chmod($sConfPath, getDefaultFileMode());
        if (($fp = fopen($sConfPath, "r"))) {
            $sConfFile = fread($fp, filesize($sConfPath));
            fclose($fp);
        } else {
            throw new Exception(sprintf($oLang->getText('ERROR_COULD_NOT_OPEN_CONFIG_FILE'), $sConfPath));
        }

        // overwriting settings
        foreach ($aParams as $sParamName => $sParamValue) {
            // non integer type variables must be surrounded by quotes
            if ($sParamName[0] != 'i') {
                $sParamValue = "'{$sParamValue}'";
            }
            $sConfFile = str_replace("<$sParamName>", $sParamValue, $sConfFile);
        }

        if (($fp = fopen($sConfPath, "w"))) {
            fwrite($fp, $sConfFile);
            fclose($fp);
            @chmod($sConfPath, getDefaultConfigFileMode());
        } else {
            throw new Exception(sprintf($oLang->getText('ERROR_CONFIG_FILE_IS_NOT_WRITABLE'), $aParams['sShopDir']));
        }
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
     */
    public function executeExternalRegenerateViewsCommand()
    {
        $regenerateViewsCommand = $this->formCommandToVendor(self::DATABASE_VIEW_REGENERATION_BINARY_FILENAME);
        $this->executeShellCommand($regenerateViewsCommand);
    }

    /**
     * Calls external database migration command.
     */
    public function executeExternalDatabaseMigrationCommand()
    {
        $databaseMigrateCommand = $this->formCommandToVendor(self::DATABASE_MIGRATION_BINARY_FILENAME) . ' ' . Migrations::MIGRATE_COMMAND;
        $this->executeShellCommand($databaseMigrateCommand);
    }

    /**
     * Calls external demodata assets install command.
     */
    public function executeExternalDemodataAssetsInstallCommand()
    {
        $installDemoDataCommand = $this->formCommandToVendor(self::DEMODATA_ASSETS_INSTALL_BINARY_FILENAME);
        $this->executeShellCommand($installDemoDataCommand);
    }

    /**
     * Execute shell command and capture output when return code is non zero.
     *
     * @param string $command Command to execute.
     *
     * @throws CommandExecutionFailedException When execution returns non zero return code.
     */
    private function executeShellCommand($command)
    {
        $commandWithStdErrRedirection = $command . " 2>&1";

        exec($commandWithStdErrRedirection, $outputLines, $returnCode);

        if (($returnCode !== 0)) {
            $exception = new CommandExecutionFailedException($command);
            $exception->setReturnCode($returnCode);
            $exception->setCommandOutput($returnCode !== 127 ? $outputLines : ['Impossible to execute the command.']);

            throw $exception;
        }
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
     * Return path to composer vendor bin directory.
     *
     * @return string
     */
    private function getVendorBinaryDirectory()
    {
        return $this->getVendorDirectory() . self::COMPOSER_VENDOR_BIN_DIRECTORY;
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
        $editionPathSelector = new EditionRootPathProvider(new Facts($edition));
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

        return $this->getUtilitiesInstance()->getVendorDirectory()
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
     * Form command to script file in Vendor directory.
     *
     * @param string $command
     *
     * @return string
     */
    private function formCommandToVendor($command)
    {
        $migrateCommand = implode(
            DIRECTORY_SEPARATOR,
            [$this->getVendorBinaryDirectory(), $command]
        );
        $migrateCommand = '"' . $migrateCommand . '"';

        return $migrateCommand;
    }
}
