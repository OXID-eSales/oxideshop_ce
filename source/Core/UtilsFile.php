<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * File manipulation utility class
 */
class UtilsFile extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Promotions images upload dir name
     *
     * @var string
     */
    const PROMO_PICTURE_DIR = 'promo';

    /**
     * Max pictures count
     *
     * @var int
     */
    protected $_iMaxPicImgCount = 12;

    /**
     * Max zoom pictures count
     *
     * @var int
     */
    protected $_iMaxZoomImgCount = 12;

    /**
     * Image type and its folder information array
     *
     * @var array
     */
    protected $_aTypeToPath = ['TC'    => 'master/category/thumb',
                                    'CICO'  => 'master/category/icon',
                                    'PICO'  => 'master/category/promo_icon',
                                    'MICO'  => 'master/manufacturer/icon',
                                    'VICO'  => 'master/vendor/icon',
                                    'PROMO' => self::PROMO_PICTURE_DIR,
                                    'ICO'   => 'master/product/icon',
                                    'TH'    => 'master/product/thumb',
                                    'M1'    => 'master/product/1',
                                    'M2'    => 'master/product/2',
                                    'M3'    => 'master/product/3',
                                    'M4'    => 'master/product/4',
                                    'M5'    => 'master/product/5',
                                    'M6'    => 'master/product/6',
                                    'M7'    => 'master/product/7',
                                    'M8'    => 'master/product/8',
                                    'M9'    => 'master/product/9',
                                    'M10'   => 'master/product/10',
                                    'M11'   => 'master/product/11',
                                    'M12'   => 'master/product/12',
        //
                                    'P1'    => '1',
                                    'P2'    => '2',
                                    'P3'    => '3',
                                    'P4'    => '4',
                                    'P5'    => '5',
                                    'P6'    => '6',
                                    'P7'    => '7',
                                    'P8'    => '8',
                                    'P9'    => '9',
                                    'P10'   => '10',
                                    'P11'   => '11',
                                    'P12'   => '12',
                                    'Z1'    => 'z1',
                                    'Z2'    => 'z2',
                                    'Z3'    => 'z3',
                                    'Z4'    => 'z4',
                                    'Z5'    => 'z5',
                                    'Z6'    => 'z6',
                                    'Z7'    => 'z7',
                                    'Z8'    => 'z8',
                                    'Z9'    => 'z9',
                                    'Z10'   => 'z10',
                                    'Z11'   => 'z11',
                                    'Z12'   => 'z12',
        //
                                    'WP'    => 'master/wrapping',
                                    'FL'    => 'media',
    ];

    /**
     * Denied file types
     *
     * @var array
     */
    protected $_aBadFiles = ['php', 'php3', 'php4', 'php5', 'phps', 'php6', 'jsp', 'cgi', 'cmf', 'exe', 'phtml', 'pht', 'phar'];

    /**
     * Allowed to upload files in demo mode ( "white list")
     *
     * @var array
     */
    protected $_aAllowedFiles = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Counts how many new files added.
     *
     * @var integer
     */
    protected $_iNewFilesCounter = 0;

    /**
     * Class constructor, initailizes pictures count info (_iMaxPicImgCount/_iMaxZoomImgCount)
     *
     * @return null
     */
    public function __construct()
    {
        $myConfig = $this->getConfig();

        if ($iPicCount = $myConfig->getConfigParam('iPicCount')) {
            $this->_iMaxPicImgCount = $iPicCount;
        }

        $this->_iMaxZoomImgCount = $this->_iMaxPicImgCount;
    }

    /**
     * Getter for param _iNewFilesCounter which counts how many new files added.
     *
     * @return integer
     */
    public function getNewFilesCounter()
    {
        return $this->_iNewFilesCounter;
    }

    /**
     * Setter for param _iNewFilesCounter which counts how many new files added.
     *
     * @param integer $iNewFilesCounter New files count.
     */
    protected function _setNewFilesCounter($iNewFilesCounter)
    {
        $this->_iNewFilesCounter = (int) $iNewFilesCounter;
    }

    /**
     * Normalizes dir by adding missing trailing slash
     *
     * @param string $sDir Directory
     *
     * @return string
     */
    public function normalizeDir($sDir)
    {
        if (isset($sDir) && $sDir != "" && substr($sDir, -1) !== '/') {
            $sDir .= "/";
        }

        return $sDir;
    }

    /**
     * Copies directory tree for creating a new shop.
     *
     * @param string $sSourceDir Source directory
     * @param string $sTargetDir Target directory
     */
    public function copyDir($sSourceDir, $sTargetDir)
    {
        $oStr = getStr();
        $handle = opendir($sSourceDir);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($sSourceDir . '/' . $file)) {
                    // recursive
                    $sNewSourceDir = $sSourceDir . '/' . $file;
                    $sNewTargetDir = $sTargetDir . '/' . $file;
                    if (strcasecmp($file, 'CVS') && strcasecmp($file, '.svn')) {
                        @mkdir($sNewTargetDir, 0777);
                        $this->copyDir($sNewSourceDir, $sNewTargetDir);
                    }
                } else {
                    $sSourceFile = $sSourceDir . '/' . $file;
                    $sTargetFile = $sTargetDir . '/' . $file;

                    //do not copy files within dyn_images
                    if (!$oStr->strstr($sSourceDir, 'dyn_images') || $file == 'nopic.jpg' || $file == 'nopic_ico.jpg') {
                        @copy($sSourceFile, $sTargetFile);
                    }
                }
            }
        }
        closedir($handle);
    }

    /**
     * Deletes directory tree.
     *
     * @param string $sSourceDir Path to directory
     *
     * @return null
     */
    public function deleteDir($sSourceDir)
    {
        if (is_dir($sSourceDir)) {
            if ($oDir = dir($sSourceDir)) {
                while (false !== $sFile = $oDir->read()) {
                    if ($sFile == '.' || $sFile == '..') {
                        continue;
                    }

                    if (!$this->deleteDir($oDir->path . DIRECTORY_SEPARATOR . $sFile)) {
                        $oDir->close();

                        return false;
                    }
                }

                $oDir->close();

                return rmdir($sSourceDir);
            }
        } elseif (file_exists($sSourceDir)) {
            return unlink($sSourceDir);
        }
    }

    /**
     * Reads remote stored file. Returns contents of file.
     *
     * @param string $sPath Remote file path & name
     *
     * @return string
     */
    public function readRemoteFileAsString($sPath)
    {
        $sRet = '';
        $hFile = @fopen($sPath, 'r');
        if ($hFile) {
            socket_set_timeout($hFile, 2);
            while (!feof($hFile)) {
                $sLine = fgets($hFile, 4096);
                $sRet .= $sLine;
            }
            fclose($hFile);
        }

        return $sRet;
    }

    /**
     * Prepares image file name
     *
     * @param object $sValue     uploadable file name
     * @param string $sType      image type
     * @param object $blDemo     if true = whecks if file type is defined in \OxidEsales\Eshop\Core\UtilsFile::_aAllowedFiles
     * @param string $sImagePath final image file location
     * @param bool   $blUnique   if TRUE - generates unique file name
     *
     * @return string
     */
    protected function _prepareImageName($sValue, $sType, $blDemo, $sImagePath, $blUnique = true)
    {
        if ($sValue) {
            // add type to name
            $aFilename = explode(".", $sValue);

            $sFileType = trim($aFilename[count($aFilename) - 1]);

            if (isset($sFileType)) {
                $oStr = getStr();

                // unallowed files ?
                if (in_array($sFileType, $this->_aBadFiles) || ($blDemo && !in_array($sFileType, $this->_aAllowedFiles))) {
                    \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("File didn't pass our allowed files filter.");
                }

                // removing file type
                if (count($aFilename) > 0) {
                    unset($aFilename[count($aFilename) - 1]);
                }

                $sFName = '';
                if (isset($aFilename[0])) {
                    $sFName = $oStr->preg_replace('/[^a-zA-Z0-9()_\.-]/', '', implode('.', $aFilename));
                }

                $sValue = $this->_getUniqueFileName($sImagePath, "{$sFName}", $sFileType, "", $blUnique);
            }
        }

        return $sValue;
    }

    /**
     * Returns image storage path
     *
     * @param string $sType image type
     *
     * @return string
     */
    protected function _getImagePath($sType)
    {
        $sFolder = array_key_exists($sType, $this->_aTypeToPath) ? $this->_aTypeToPath[$sType] : '0';

        return $this->normalizeDir($this->getConfig()->getPictureDir(false)) . "{$sFolder}/";
    }

    /**
     * Returns array of sizes which are used to resize images. If size is not
     * defined - NULL will be returned
     *
     * @param string $sImgType image type (TH, TC, ICO etc), can be useful for modules
     * @param int    $iImgNum  number of image (e.g. numper of ZOOM1 is 1)
     * @param string $sImgConf config parameter name, which keeps size info
     *
     * @return array | null
     */
    protected function _getImageSize($sImgType, $iImgNum, $sImgConf)
    {
        $myConfig = $this->getConfig();

        switch ($sImgConf) {
            case 'aDetailImageSizes':
                $aDetailImageSizes = $myConfig->getConfigParam($sImgConf);
                $sSize = $myConfig->getConfigParam('sDetailImageSize');
                if (isset($aDetailImageSizes['oxpic' . $iImgNum])) {
                    $sSize = $aDetailImageSizes['oxpic' . $iImgNum];
                }
                break;
            default:
                $sSize = $myConfig->getConfigParam($sImgConf);
                break;
        }
        if ($sSize) {
            return explode('*', $sSize);
        }
    }

    /**
     * Copy file from source to target location
     *
     * @param string $sSource file location
     * @param string $sTarget file location
     *
     * @return bool
     */
    protected function _copyFile($sSource, $sTarget)
    {
        if (!is_dir(dirname($sTarget))) {
            mkdir(dirname($sTarget), 0744, true);
        }

        $blDone = true;
        if ($sSource !== $sTarget) {
            $blDone = copy($sSource, $sTarget);
        }

        if ($blDone) {
            $blDone = @chmod($sTarget, 0644);
        }

        return $blDone;
    }

    /**
     * Moves image from source to target location
     *
     * @param string $sSource image location
     * @param string $sTarget image copy location
     *
     * @return bool
     */
    protected function _moveImage($sSource, $sTarget)
    {
        if (!is_dir(dirname($sTarget))) {
            mkdir(dirname($sTarget), 0744, true);
        }

        $blDone = true;
        if ($sSource !== $sTarget) {
            $blDone = move_uploaded_file($sSource, $sTarget);
        }

        if ($blDone) {
            $blDone = @chmod($sTarget, 0644);
        }

        return $blDone;
    }

    /**
     * Uploaded file processor (filters, etc), sets configuration parameters to
     * passed object and returns it.
     *
     * @param object $oObject          object, that parameters are modified according to passed files
     * @param array  $aFiles           name of files to process
     * @param bool   $blUseMasterImage use master image as source for processing
     * @param bool   $blUnique         TRUE - forces new file creation with unique name
     *
     * @return object
     */
    public function processFiles($oObject = null, $aFiles = [], $blUseMasterImage = false, $blUnique = true)
    {
        $aFiles = $aFiles ? $aFiles : $_FILES;
        if (isset($aFiles['myfile']['name'])) {
            $oConfig = $this->getConfig();

            // A. protection for demoshops - strictly defining allowed file extensions
            $blDemo = (bool) $oConfig->isDemoShop();

            // folder where images will be processed
            $sTmpFolder = $oConfig->getConfigParam("sCompileDir");

            $iNewFilesCounter = 0;
            $aSource = $aFiles['myfile']['tmp_name'];
            $aError = $aFiles['myfile']['error'];

            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            // process all files
            foreach ($aFiles['myfile']['name'] as $sKey => $sValue) {
                $sSource = $aSource[$sKey];
                $iError = $aError[$sKey];
                $aFiletype = explode("@", $sKey);
                $sKey = $aFiletype[1];
                $sType = $aFiletype[0];

                $sValue = strtolower($sValue);
                $sImagePath = $this->_getImagePath($sType);

                // Should translate error to user if file was uploaded
                if (UPLOAD_ERR_OK !== $iError && UPLOAD_ERR_NO_FILE !== $iError) {
                    $sErrorsDescription = $this->translateError($iError);
                    $oEx->setMessage($sErrorsDescription);
                    \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false);
                }

                // checking file type and building final file name
                if ($sSource && ($sValue = $this->_prepareImageName($sValue, $sType, $blDemo, $sImagePath, $blUnique))) {
                    // moving to tmp folder for processing as safe mode or spec. open_basedir setup
                    // usually does not allow file modification in php's temp folder
                    $sProcessPath = $sTmpFolder . basename($sSource);

                    if ($sProcessPath) {
                        if ($blUseMasterImage) {
                            //using master image as source, so only copying it to
                            $blMoved = $this->_copyFile($sSource, $sImagePath . $sValue);
                        } else {
                            $blMoved = $this->_moveImage($sSource, $sImagePath . $sValue);
                        }

                        if ($blMoved) {
                            // New image successfully add.
                            $iNewFilesCounter++;
                            // assign the name
                            if ($oObject && isset($oObject->$sKey)) {
                                $oObject->{$sKey}->setValue($sValue);
                            }
                        }
                    }
                }
            }

            $this->_setNewFilesCounter($iNewFilesCounter);
        }

        return $oObject;
    }

    /**
     * Checks if passed file exists and may be opened for reading. Returns true
     * on success.
     *
     * @param string $sFile Name of file to check
     *
     * @return bool
     */
    public function checkFile($sFile)
    {
        $aCheckCache = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("checkcache");

        if (isset($aCheckCache[$sFile])) {
            return $aCheckCache[$sFile];
        }

        $blRet = true;
        if (!is_readable($sFile)) {
            $blRet = $this->urlValidate($sFile);
        }

        $aCheckCache[$sFile] = $blRet;
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("checkcache", $aCheckCache);

        return $blRet;
    }

    /**
     * Checks if given URL is accessible (HTTP-Code: 200)
     *
     * @param string $sLink given link
     *
     * @return boolean
     */
    public function urlValidate($sLink)
    {
        $aUrlParts = @parse_url($sLink);
        $sHost = (isset($aUrlParts["host"]) && $aUrlParts["host"]) ? $aUrlParts["host"] : null;

        if ($sHost) {
            $sDocumentPath = (isset($aUrlParts["path"]) && $aUrlParts["path"]) ? $aUrlParts["path"] : '/';
            $sDocumentPath .= (isset($aUrlParts["query"]) && $aUrlParts["query"]) ? '?' . $aUrlParts["query"] : '';

            $sPort = (isset($aUrlParts["port"]) && $aUrlParts["port"]) ? $aUrlParts["port"] : '80';

            // Now (HTTP-)GET $documentpath at $sHost";
            if (($oConn = @fsockopen($sHost, $sPort, $iErrNo, $sErrStr, 30))) {
                fwrite($oConn, "HEAD {$sDocumentPath} HTTP/1.0\r\nHost: {$sHost}\r\n\r\n");
                $sResponse = fgets($oConn, 22);
                fclose($oConn);

                if (preg_match("/200 OK/", $sResponse)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Process uploaded files. Returns unique file name, on fail false
     *
     * @param string $sFileName   form file item name
     * @param string $sUploadPath RELATIVE (to config sShopDir parameter) path for uploaded file to be copied
     *
     * @throws oxException if file is not valid
     *
     * @return string
     */
    public function processFile($sFileName, $sUploadPath)
    {
        $aFileInfo = $_FILES[$sFileName];

        $sBasePath = $this->getConfig()->getConfigParam('sShopDir');

        //checking params
        if (!isset($aFileInfo['name']) || !isset($aFileInfo['tmp_name'])) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_NOFILE');
        }

        //wrong chars in file name?
        if (!getStr()->preg_match('/^[\-_a-z0-9\.]+$/i', $aFileInfo['name'])) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_FILENAMEINVALIDCHARS');
        }

        // error uploading file ?
        if (isset($aFileInfo['error']) && $aFileInfo['error']) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_FILEUPLOADERROR_' . ((int) $aFileInfo['error']));
        }

        $aPathInfo = pathinfo($aFileInfo['name']);

        $sExt = $aPathInfo['extension'];
        $sFileName = $aPathInfo['filename'];

        $aAllowedUploadTypes = (array) $this->getConfig()->getConfigParam('aAllowedUploadTypes');
        $aAllowedUploadTypes = array_map("strtolower", $aAllowedUploadTypes);

        if (!in_array(strtolower($sExt), $aAllowedUploadTypes)) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_NOTALLOWEDTYPE');
        }

        $sFileName = $this->_getUniqueFileName($sBasePath . $sUploadPath, $sFileName, $sExt);

        if ($this->_moveImage($aFileInfo['tmp_name'], $sBasePath . $sUploadPath . "/" . $sFileName)) {
            return $sFileName;
        }

        return false;
    }

    /**
     * Checks if file with same name does not exist, if exists - addes number prefix
     * to file name Returns unique file name.
     *
     * @param string $sFilePath file storage path/folder (e.g. /htdocs/out/img/)
     * @param string $sFileName name of file (e.g. picture1)
     * @param string $sFileExt  file extension (e.g. gif)
     * @param string $sSufix    file name sufix (e.g. _ico)
     * @param bool   $blUnique  TRUE - generates unique file name, FALSE - just glues given parts of file name
     *
     * @return string
     */
    protected function _getUniqueFileName($sFilePath, $sFileName, $sFileExt, $sSufix = "", $blUnique = true)
    {
        $sFilePath = $this->normalizeDir($sFilePath);
        $iFileCounter = 0;
        $sTempFileName = $sFileName;
        $oStr = getStr();

        //file exists ?
        while ($blUnique && file_exists($sFilePath . "/" . $sFileName . $sSufix . "." . $sFileExt)) {
            $iFileCounter++;

            //removing "(any digit)" from file name end
            $sTempFileName = $oStr->preg_replace("/\(" . $iFileCounter . "\)/", "", $sTempFileName);

            $sFileName = $sTempFileName . "($iFileCounter)";
        }

        return $sFileName . $sSufix . "." . $sFileExt;
    }

    /**
     * Returns image storage path
     *
     * @param string $sType       image type
     * @param bool   $blGenerated generated image dir.
     *
     * @return string
     */
    public function getImageDirByType($sType, $blGenerated = false)
    {
        $sFolder = array_key_exists($sType, $this->_aTypeToPath) ? $this->_aTypeToPath[$sType] : '0';
        $sDir = $this->normalizeDir($sFolder);

        if ($blGenerated === true) {
            $sDir = str_replace('master/', 'generated/', $sDir);
        }

        return $sDir;
    }

    /**
     * Translate php file upload errors to user readable format.
     *
     * @param integer $iError php file upload error number
     *
     * @return string
     */
    public function translateError($iError)
    {
        $message = '';
        // Translate only if translation exist
        if ($iError > 0 && $iError < 9 && 5 !== $iError) {
            $message = 'EXCEPTION_FILEUPLOADERROR_' . ((int) $iError);
        }

        return $message;
    }
}
