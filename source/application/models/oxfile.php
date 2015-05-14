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
 * Article files manager.
 *
 */
class oxFile extends oxBase
{

    /**
     * No active user exception code.
     */
    const NO_USER = 2;

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTable = 'oxfiles';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxfile';

    /**
     * Stores relative oxFile path from configs 'sDownloadsDir'
     *
     * @var string
     */
    protected $_sRelativeFilePath = null;

    /**
     * Paid order indicator
     *
     * @var bool
     */
    protected $_blIsPaid = null;

    /**
     * Full URL where article could be downloaded from.
     * Is set to false in case download is not available for current user
     *
     * @var string|bool
     */
    protected $_sDownloadLink = null;

    /**
     * Has valid downloads indicator
     *
     * @var bool
     */
    protected $_blHasValidDownloads = null;

    /**
     * Default manual upload dir located within general file dir
     *
     * @var string
     */
    protected $_sManualUploadDir = "uploads";

    /**
     * Initialises the instance
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    /**
     * Sets oxefile__oxstorehash with file hash and moves file to desired location
     *
     * @param int $sFileIndex File index
     */
    public function processFile($sFileIndex)
    {
        $aFileInfo = $this->getConfig()->getUploadedFile($sFileIndex);

        $this->_checkArticleFile($aFileInfo);

        $sFileHash = $this->_getFileHash($aFileInfo['tmp_name']);
        $this->oxfiles__oxstorehash = new oxField($sFileHash, oxField::T_RAW);
        $sUploadTo = $this->getStoreLocation();

        if (!$this->_uploadFile($aFileInfo['tmp_name'], $sUploadTo)) {
            throw new oxException('EXCEPTION_COULDNOTWRITETOFILE');
        }

    }

    /**
     * Checks if given file is valid upload file
     *
     * @param array $aFileInfo File info array
     */
    protected function _checkArticleFile($aFileInfo)
    {
        //checking params
        if (!isset($aFileInfo['name']) || !isset($aFileInfo['tmp_name'])) {
            throw new oxException('EXCEPTION_NOFILE');
        }

        // error uploading file ?
        if (isset($aFileInfo['error']) && $aFileInfo['error']) {
            throw new oxException('EXCEPTION_FILEUPLOADERROR_' . ((int) $aFileInfo['error']));
        }

    }

    /**
     * Return full path of root dir where download files are stored
     *
     * @return string
     */
    protected function _getBaseDownloadDirPath()
    {
        $sConfigValue = oxRegistry::getConfig()->getConfigParam('sDownloadsDir');

        //Unix full path is set
        if ($sConfigValue && $sConfigValue[0] == DIR_SEP) {
            return $sConfigValue;
        }

        //relative path is set
        if ($sConfigValue) {
            $sPath = getShopBasePath() . DIR_SEP . $sConfigValue;

            return $sPath;
        }

        //no path is set
        $sPath = getShopBasePath() . "/out/downloads/";

        return $sPath;
    }

    /**
     * Returns full filesystem path where files are stored.
     * Make sure that object oxfiles__oxstorehash or oxfiles__oxfilename
     * attribute is set before calling this method
     *
     * @return string
     */
    public function getStoreLocation()
    {
        $sPath = $this->_getBaseDownloadDirPath();
        $sPath .= DIR_SEP . $this->_getFileLocation();

        return $sPath;
    }

    /**
     * Returns relative file path from oxConfig 'sDownloadsDir' variable.
     *
     * @return string
     */
    protected function _getFileLocation()
    {
        $this->_sRelativeFilePath = '';
        $sFileHash = $this->oxfiles__oxstorehash->value;
        $sFileName = $this->oxfiles__oxfilename->value;

        //security check for demo shops
        if ($this->getConfig()->isDemoShop()) {
            $sFileName = basename($sFileName);
        }

        if ($this->isUploaded()) {
            $this->_sRelativeFilePath = $this->_getHashedFileDir($sFileHash);
            $this->_sRelativeFilePath .= DIR_SEP . $sFileHash;
        } else {
            $this->_sRelativeFilePath = DIR_SEP . $this->_sManualUploadDir . DIR_SEP . $sFileName;
        }

        return $this->_sRelativeFilePath;
    }

    /**
     * Returns relative sub dir of oxconfig 'sDownloadsDir' of
     * required file from supplied $sFileHash parameter.
     * Creates dir in case it does not exist.
     *
     * @param string $sFileHash File hash value
     *
     * @return string
     */
    protected function _getHashedFileDir($sFileHash)
    {
        $sDir = substr($sFileHash, 0, 2);
        $sAbsDir = $this->_getBaseDownloadDirPath() . DIR_SEP . $sDir;

        if (!is_dir($sAbsDir)) {
            mkdir($sAbsDir, 0755);
        }

        return $sDir;
    }

    /**
     * Calculates file hash.
     * Currently MD5 is used.
     *
     * @param string $sFileName File name values
     *
     * @return string
     */
    protected function _getFileHash($sFileName)
    {
        return md5_file($sFileName);
    }

    /**
     * Moves file from source to target and changes file mode.
     * Returns true on success.
     *
     * @param string $sSource Source filename
     * @param string $sTarget Target filename
     *
     * @return bool
     */
    protected function _uploadFile($sSource, $sTarget)
    {
        $blDone = move_uploaded_file($sSource, $sTarget);

        if ($blDone) {
            $blDone = @chmod($sTarget, 0644);
        }

        return $blDone;
    }

    /**
     * Checks whether the file has been uploaded over admin area.
     * Returns true in case file is uploaded (and hashed) over admin area.
     * Returns false in case file is placed manually (ftp) to "out/downloads/uploads" dir.
     * It's similar so don't get confused here.
     *
     * @return bool
     */
    public function isUploaded()
    {
        $blHashed = false;
        if ($this->oxfiles__oxstorehash->value) {
            $blHashed = true;
        }

        return $blHashed;
    }

    /**
     * Deletes oxFile record from DB, removes orphan files.
     *
     * @param string $sOxId default null
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        $sOxId = $sOxId ? $sOxId : $this->getId();

        $this->load($sOxId);
        // if record cannot be delete, abort deletion
        if ($blDeleted = parent::delete($sOxId)) {
            $this->_deleteFile();
        }

        return $blDeleted;
    }

    /**
     * Checks if file is not used for  other objects.
     * If not used, unlink the file.
     *
     * @return null
     */
    protected function _deleteFile()
    {
        if (!$this->isUploaded()) {
            return false;
        }
        $sHash = $this->oxfiles__oxstorehash->value;
        $oDb = oxDb::getDb();
        $iCount = $oDb->getOne(
            'SELECT COUNT(*) FROM `oxfiles` WHERE `OXSTOREHASH` = ' . $oDb->quote($sHash), false, false
        );
        if (!$iCount) {
            $sPath = $this->getStoreLocation();
            unlink($sPath);
        }
    }

    /**
     * returns oxfile__oxfilename for URL usage
     * converts spec symbols to %xx combination
     *
     * @return string
     */
    protected function _getFilenameForUrl()
    {
        return rawurlencode($this->oxfiles__oxfilename->value);
    }

    /**
     * Supplies the downloadable file for client and exits
     */
    public function download()
    {
        $oUtils = oxRegistry::getUtils();
        $sFileName = $this->_getFilenameForUrl();
        $sFileLocations = $this->getStoreLocation();

        if (!$this->exist()) {
            throw new oxException('EXCEPTION_NOFILE');
        }

        $oUtils->setHeader("Pragma: public");
        $oUtils->setHeader("Expires: 0");
        $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
        $oUtils->setHeader('Content-Disposition: attachment;filename=' . $sFileName);
        $oUtils->setHeader("Content-Type: application/octet-stream");
        if ($iFileSize = $this->getSize()) {
            $oUtils->setHeader("Content-Length: " . $iFileSize);
        }
        readfile($sFileLocations);
        $oUtils->showMessageAndExit(null);
    }

    /**
     * Check if file exist
     *
     * @return bool
     */
    public function exist()
    {
        return file_exists($this->getStoreLocation());
    }

    /**
     * Checks if this file has valid ordered downloads
     *
     * @return bool
     */
    public function hasValidDownloads()
    {
        if ($this->_blHasValidDownloads == null) {
            $this->_blHasValidDownloads = false;
            $sNow = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
            $sFileId = $this->getId();

            $oDb = oxDb::getDb();

            $sSql = "SELECT
                        `oxorderfiles`.`oxid`
                     FROM `oxorderfiles`
                        LEFT JOIN `oxorderarticles` ON `oxorderarticles`.`oxid` = `oxorderfiles`.`oxorderarticleid`
                        LEFT JOIN `oxorder` ON `oxorder`.`oxid` = `oxorderfiles`.`oxorderid`
                     WHERE `oxorderfiles`.`oxfileid` = " . $oDb->quote($sFileId) . "
                        AND ( ! `oxorderfiles`.`oxmaxdownloadcount` OR `oxorderfiles`.`oxmaxdownloadcount` > `oxorderfiles`.`oxdownloadcount`)
                        AND ( `oxorderfiles`.`oxvaliduntil` = '0000-00-00 00:00:00' OR `oxorderfiles`.`oxvaliduntil` > '{$sNow}' )
                        AND `oxorder`.`oxstorno` = 0
                        AND `oxorderarticles`.`oxstorno` = 0";

            if ($oDb->getOne($sSql)) {
                $this->_blHasValidDownloads = true;
            }
        }

        return $this->_blHasValidDownloads;
    }

    /**
     * Returns max download count of file
     *
     * @return int
     */
    public function getMaxDownloadsCount()
    {
        $iMaxCount = $this->oxfiles__oxmaxdownloads->value;
        //if value is -1, takes global options
        if ($iMaxCount < 0) {
            $iMaxCount = $this->getConfig()->getConfigParam("iMaxDownloadsCount");
        }

        return $iMaxCount;
    }

    /**
     * Returns max download count of file, if user is not registered
     *
     * @return int
     */
    public function getMaxUnregisteredDownloadsCount()
    {
        $iMaxCount = $this->oxfiles__oxmaxunregdownloads->value;
        //if value is -1, takes global options
        if ($iMaxCount < 0) {
            $iMaxCount = $this->getConfig()->getConfigParam("iMaxDownloadsCountUnregistered");
        }

        return $iMaxCount;
    }

    /**
     * Returns ordered file link expiration time in hours
     *
     * @return int
     */
    public function getLinkExpirationTime()
    {
        $iExpTime = $this->oxfiles__oxlinkexptime->value;
        //if value is -1, takes global options
        if ($iExpTime < 0) {
            $iExpTime = $this->getConfig()->getConfigParam("iLinkExpirationTime");
        }

        return $iExpTime;
    }

    /**
     * Returns download link expiration time in hours, after the first download
     *
     * @return int
     */
    public function getDownloadExpirationTime()
    {
        $iExpTime = $this->oxfiles__oxdownloadexptime->value;
        //if value is -1, takes global options
        if ($iExpTime < 0) {
            $iExpTime = $this->getConfig()->getConfigParam("iDownloadExpirationTime");
        }

        return $iExpTime;
    }

    /**
     * Returns file size in bytes
     *
     * @return int
     */
    public function getSize()
    {
        $iSize = 0;
        if ($this->exist()) {
            $iSize = filesize($this->getStoreLocation());
        }

        return $iSize;
    }
}
