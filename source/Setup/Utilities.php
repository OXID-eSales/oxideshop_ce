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

use Exception;
use OxidEsales\Eshop\Core\Edition\EditionPathProvider;

/**
 * Setup utilities class
 */
class Utilities extends Core
{
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
     * Converts given data array from iso-8859-15 to utf-8
     *
     * @param array $aData data to convert
     *
     * @return array
     */
    public function convertToUtf8($aData)
    {
        if (is_array($aData)) {
            $aKeys = array_keys($aData);
            $aValues = array_values($aData);

            //converting keys
            if (count($aData) > 1) {
                foreach ($aKeys as $sKeyIndex => $sKeyValue) {
                    if (is_string($sKeyValue)) {
                        $aKeys[$sKeyIndex] = iconv('iso-8859-15', 'utf-8', $sKeyValue);
                    }
                }

                $aData = array_combine($aKeys, $aValues);

                //converting values
                foreach ($aData as $sKey => $sValue) {
                    if (is_array($sValue)) {
                        $this->convertToUtf8($sValue);
                    }

                    if (is_string($sValue)) {
                        $aData[$sKey] = iconv('iso-8859-15', 'utf-8', $sValue);
                    }
                }
            }
        } else {
            $aData = iconv('iso-8859-15', 'utf-8', $aData);
        }

        return $aData;
    }

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
    public function removeDir($sPath, $blDeleteSuccess, $iMode = 0, $aSkipFiles = array(), $aSkipFolders = array())
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
     * @param string $aPath path info array
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
     * Updates config.inc.php file contents
     *
     * @param array $aParams paths parameters
     *
     * @throws Exception exception is thrown is file cant be open for reading or can not be written
     */
    public function updateConfigFile($aParams)
    {
        $sConfPath = $aParams['sShopDir'] . "/config.inc.php";

        $oLang = $this->getInstance("Language");

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
            $sConfFile = preg_replace("/(this->{$sParamName}).*'<.*>'.*;/", "\\1 = " . $sParamValue . ";", $sConfFile);
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
     * Updates default htaccess file with user defined params
     *
     * @param array  $aParams    various setup parameters
     * @param string $sSubFolder in case you need to update non default, but e.g. admin file, you must add its folder
     */
    public function updateHtaccessFile($aParams, $sSubFolder = "")
    {
        /** @var Language $oLang */
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
}
