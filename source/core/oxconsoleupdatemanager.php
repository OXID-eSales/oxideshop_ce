<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Console Application Update Manager
 */
class oxConsoleUpdateManager
{

    /**
     * @var bool Object already created?
     */
    protected static $_bCreated = false;

    /**
     * @var string Latest version available at GitHub
     */
    protected $_sLatestVersion;

    /**
     * @var oxIOutput|null
     */
    protected $_oOutput;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (static::$_bCreated) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Only one instance for oxConsoleUpdateManager allowed');
            throw $oEx;
        }

        static::$_bCreated = true;
    }

    /**
     * Run update
     */
    public function run(oxIOutput $oOutput = null)
    {
        $this->_oOutput = $oOutput;
        $sUpdatePath = null;

        try {
            $sVersion = $this->getLatestVersion();
            if (oxConsoleApplication::VERSION == $this->getLatestVersion()) {
                if (null !== $oOutput) {
                    $oOutput->writeLn('Your console application is up to date');
                }
                return;
            }

            $sUpdatePath = $this->_downloadPackage($sVersion);
            $this->_update($sUpdatePath);
        } catch (oxConsoleException $oEx) {
            if (null === $oOutput) {
                throw $oEx;
            }

            $oOutput->writeLn();
            $oOutput->writeLn($oEx->getMessage());
            return;
        }

        if (null !== $oOutput) {
            $oOutput->writeLn('OXID Console updated successfully');
        }
    }

    /**
     * Update OXID Console application from update path
     *
     * @param string $sUpdatePath
     *
     * @throws oxConsoleException
     */
    protected function _update($sUpdatePath)
    {
        $sUpdatePath = $this->_appendDirectorySeparator($sUpdatePath);
        $oOutput = $this->getOutput();
        if (null !== $oOutput) {
            $oOutput->write('Updating OXID Console files');
        }

        $sCopyPath = $sUpdatePath . 'copy_this' . DIRECTORY_SEPARATOR;
        if (!is_dir($sCopyPath)) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('No copy_this folder in update path');
            throw $oEx;
        }

        $oIterator = new RecursiveDirectoryIterator($sCopyPath);
        $oIterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
        $oAllFiles = new RecursiveIteratorIterator($oIterator);

        foreach ($oAllFiles as $oFilePath) {
            $sBasePath = str_replace($sCopyPath, OX_BASE_PATH, $oFilePath);
            $this->_replaceFile((string)$oFilePath, $sBasePath);
        }

        if (null !== $oOutput) {
            $oOutput->writeLn(' [OK]');
        }
    }

    /**
     * Replace one file with other
     *
     * @param string $sFromPath
     * @param string $sToPath
     */
    protected function _replaceFile($sFromPath, $sToPath)
    {
        $oOutput = $this->getOutput();

        if (!is_file($sFromPath)) {
            if ($oOutput) {
                $oOutput->writeLn('Warning! ' . $sFromPath . ' not found - skipping');
            }
            return;
        }

        $sCopyDir = dirname($sToPath);
        if (!is_dir($sCopyDir)) {
            mkdir($sCopyDir, 0755, true);
        }

        @unlink($sToPath);
        rename($sFromPath, $sToPath);
    }

    /**
     * Download package from GitHub by version
     *
     * @param string $sVersion
     *
     * @return string Path were packed is saved
     * @throws oxConsoleException
     */
    protected function _downloadPackage($sVersion)
    {
        $oConfig = oxRegistry::getConfig();
        $sCompileDir = $this->_appendDirectorySeparator($oConfig->getShopConfVar('sCompileDir'));
        $sSavePath = $sCompileDir . uniqid('oxid-console') . '.zip';
        $oOutput = $this->getOutput();

        if (null !== $oOutput) {
            $oOutput->write('Downloading update');
        }

        $bResult = (bool)file_put_contents(
            $sSavePath,
            fopen("https://github.com/EllisV/oxid-console/archive/{$sVersion}.zip", 'r')
        );

        if (!$bResult) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Could not download update package');
            throw $oEx;
        }

        if (null !== $oOutput) {
            $oOutput->writeLn(' [OK]');
        }

        return $this->_unzipPackage($sSavePath, $sVersion);
    }

    /**
     * Unzip specific version of update package
     *
     * @param string $sPath
     * @param string $sVersion
     *
     * @return string Path to directory
     * @throws oxConsoleException
     */
    protected function _unzipPackage($sPath, $sVersion)
    {
        $oZip = new ZipArchive();
        $oOutput = $this->getOutput();

        if (null !== $oOutput) {
            $oOutput->write('Unzip update');
        }

        if ($oZip->open($sPath) !== true) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Error trying to open archive');
            throw $oEx;
        }

        $sExtractPath = dirname($sPath);
        if (!$oZip->extractTo($sExtractPath)) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Error trying to unzip archive');
            throw $oEx;
        }
        $oZip->close();

        if (null !== $oOutput) {
            $oOutput->writeLn(' [OK]');
        }

        return $sExtractPath . DIRECTORY_SEPARATOR . 'oxid-console-' . substr($sVersion, 1);
    }

    /**
     * Get output
     *
     * @return oxIOutput|null
     */
    public function getOutput()
    {
        return $this->_oOutput;
    }

    /**
     * Get latest version number of OXID console
     *
     * Makes a cURL request to GitHub API to get latest tag
     *
     * @return string|bool Current version or false on fail
     * @throws oxConsoleException
     */
    public function getLatestVersion()
    {
        if (null === $this->_sLatestVersion) {
            $oCurl = curl_init('https://api.github.com/repos/EllisV/oxid-console/git/refs/tags');
            curl_setopt($oCurl, CURLOPT_USERAGENT, 'OXID-Console-Version-Checker');
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
            $sResponse = curl_exec($oCurl);

            if (curl_errno($oCurl) || !$sResponse) {
                /** @var oxConsoleException $oEx */
                $oEx = oxNew('oxConsoleException');
                $oEx->setMessage('Can not get a response from server');
                throw $oEx;
            }

            $aResponse = json_decode($sResponse);
            if (!is_array($aResponse)) {
                /** @var oxConsoleException $oEx */
                $oEx = oxNew('oxConsoleException');
                $oEx->setMessage('Wrong response format from server');
                throw $oEx;
            }

            $oLatestEntry = array_pop($aResponse);
            if (!isset($oLatestEntry->ref)) {
                /** @var oxConsoleException $oEx */
                $oEx = oxNew('oxConsoleException');
                $oEx->setMessage('No reference key were found');
                throw $oEx;
            }

            $this->_sLatestVersion = substr($oLatestEntry->ref, 10);
        }

        return $this->_sLatestVersion;
    }

    /**
     * Append directory separator to path
     *
     * @param string $sPath
     *
     * @return string
     */
    protected function _appendDirectorySeparator($sPath)
    {
        if (substr($sPath, -1) != DIRECTORY_SEPARATOR) {
            return $sPath . DIRECTORY_SEPARATOR;
        }

        return $sPath;
    }
}