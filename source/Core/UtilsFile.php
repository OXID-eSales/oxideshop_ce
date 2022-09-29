<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Exception\ExceptionToDisplay;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Bridge\MasterImageHandlerBridgeInterface;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

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
                                    'MPICO' => 'master/manufacturer/promo_icon',
                                    'TM'    => 'master/manufacturer/thumb',
                                    'MPIC'  => 'master/manufacturer/picture',
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
    protected $_aAllowedFiles = ['gif', 'jpg', 'jpeg', 'png', 'webp', 'pdf'];

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
        $myConfig = Registry::getConfig();

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
    protected function setNewFilesCounter($iNewFilesCounter)
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
        $oStr = Str::getStr();
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
    protected function prepareImageName($sValue, $sType, $blDemo, $sImagePath, $blUnique = true)
    {
        if ($sValue) {
            // add type to name
            $aFilename = explode(".", $sValue);

            $sFileType = trim($aFilename[count($aFilename) - 1]);

            if (isset($sFileType)) {
                // unallowed files ?
                if (in_array($sFileType, $this->_aBadFiles) || ($blDemo && !in_array($sFileType, $this->_aAllowedFiles))) {
                    Registry::getUtils()->showMessageAndExit("File didn't pass our allowed files filter.");
                }

                // removing file type
                if (count($aFilename) > 0) {
                    unset($aFilename[count($aFilename) - 1]);
                }

                $sFName = '';
                if (isset($aFilename[0])) {
                    $sFName = Str::getStr()->preg_replace('/[^a-zA-Z0-9()_\.-]/', '', implode('.', $aFilename));
                }

                $sValue = $this->getUniqueFileName($sImagePath, "{$sFName}", $sFileType, "", $blUnique);
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
    protected function getImagePath($sType)
    {
        $sFolder = array_key_exists($sType, $this->_aTypeToPath) ? $this->_aTypeToPath[$sType] : '0';

        return $this->normalizeDir(Registry::getConfig()->getPictureDir(false)) . "{$sFolder}/";
    }

    /**
     * Returns array of sizes which are used to resize images. If size is not
     * defined - NULL will be returned
     *
     * @param string $sImgType image type (TH, TC, ICO etc), can be useful for modules
     * @param int    $iImgNum  number of image (e.g. numper of ZOOM1 is 1)
     * @param string $sImgConf config parameter name, which keeps size info
     *
     * @return array|null
     */
    protected function getImageSize($sImgType, $iImgNum, $sImgConf)
    {
        $myConfig = Registry::getConfig();

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
            $oConfig = Registry::getConfig();

            // A. protection for demoshops - strictly defining allowed file extensions
            $blDemo = (bool) $oConfig->isDemoShop();

            // folder where images will be processed
            $sTmpFolder = $oConfig->getConfigParam("sCompileDir");

            $iNewFilesCounter = 0;
            $aSource = $aFiles['myfile']['tmp_name'];
            $aError = $aFiles['myfile']['error'];

            $oEx = oxNew(ExceptionToDisplay::class);
            // process all files
            foreach ($aFiles['myfile']['name'] as $sKey => $sValue) {
                $sSource = $aSource[$sKey];
                $iError = $aError[$sKey];
                $aFiletype = explode("@", $sKey);
                $sKey = $aFiletype[1];
                $sType = $aFiletype[0];

                $sValue = strtolower($sValue);
                $sImagePath = $this->getImagePath($sType);

                // Should translate error to user if file was uploaded
                if (UPLOAD_ERR_OK !== $iError && UPLOAD_ERR_NO_FILE !== $iError) {
                    $sErrorsDescription = $this->translateError($iError);
                    $oEx->setMessage($sErrorsDescription);
                    Registry::getUtilsView()->addErrorToDisplay($oEx, false);
                }

                // checking file type and building final file name
                if ($sSource && ($sValue = $this->prepareImageName($sValue, $sType, $blDemo, $sImagePath, $blUnique))) {
                    // moving to tmp folder for processing as safe mode or spec. open_basedir setup
                    // usually does not allow file modification in php's temp folder
                    $sProcessPath = $sTmpFolder . basename($sSource);

                    if ($sProcessPath) {
                        $destination = Path::join("$sImagePath$sValue");
                        $blMoved = $blUseMasterImage
                            ? $this->copyMasterImage($sSource, $destination)
                            : $this->uploadMasterImage($sSource, $destination);

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

            $this->setNewFilesCounter($iNewFilesCounter);
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
        $aCheckCache = Registry::getSession()->getVariable("checkcache");

        if (isset($aCheckCache[$sFile])) {
            return $aCheckCache[$sFile];
        }

        $blRet = true;
        if (!is_readable($sFile)) {
            $blRet = $this->urlValidate($sFile);
        }

        $aCheckCache[$sFile] = $blRet;
        Registry::getSession()->setVariable("checkcache", $aCheckCache);

        return $blRet;
    }

    /**
     * Checks if given URL is accessible (HTTP-Code: 200)
     *
     * @param string $url
     *
     * @return boolean
     */
    public function urlValidate($url)
    {
        return $this->isUrlSchemaValid($url)
            && $this->isUrlAccessible($url);
    }

    /**
     * Process uploaded files. Returns unique file name, on fail false
     *
     * @param string $sFileName   form file item name
     * @param string $sUploadPath RELATIVE (to config sShopDir parameter) path for uploaded file to be copied
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException if file is not valid
     *
     * @return string
     */
    public function processFile($sFileName, $sUploadPath)
    {
        $aFileInfo = $_FILES[$sFileName];

        $sBasePath = Registry::getConfig()->getConfigParam('sShopDir');

        //checking params
        if (!isset($aFileInfo['name']) || !isset($aFileInfo['tmp_name'])) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_NOFILE');
        }

        //wrong chars in file name?
        if (!Str::getStr()->preg_match('/^[\-_a-z0-9\.]+$/i', $aFileInfo['name'])) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_FILENAMEINVALIDCHARS');
        }

        // error uploading file ?
        if (isset($aFileInfo['error']) && $aFileInfo['error']) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_FILEUPLOADERROR_' . ((int) $aFileInfo['error']));
        }

        $aPathInfo = pathinfo($aFileInfo['name']);

        $sExt = $aPathInfo['extension'];
        $sFileName = $aPathInfo['filename'];

        $aAllowedUploadTypes = (array) Registry::getConfig()->getConfigParam('aAllowedUploadTypes');
        $aAllowedUploadTypes = array_map("strtolower", $aAllowedUploadTypes);

        if (!in_array(strtolower($sExt), $aAllowedUploadTypes)) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_NOTALLOWEDTYPE');
        }

        $sFileName = $this->getUniqueFileName($sBasePath . $sUploadPath, $sFileName, $sExt);

        $destination = Path::join($sBasePath, $sUploadPath, $sFileName);
        if ($this->uploadMasterImage($aFileInfo['tmp_name'], $destination)) {
            return $sFileName;
        }

        return false;
    }

    /**
     * @param string $directory
     * @param string $filename
     * @param string $extension
     * @param string $suffix
     * @param bool $unique
     * @return string
     */
    protected function getUniqueFileName($directory, $filename, $extension, $suffix = "", $unique = true)
    {
        if (!$unique) {
            return "$filename$suffix.$extension";
        }
        $directory = $this->normalizeDir($directory);
        $fileCounter = 0;
        $temporaryName = $filename;
        $stringHandler = Str::getStr();
        $masterImageHandler = $this->getContainer()->get(MasterImageHandlerBridgeInterface::class);
        while (
            $masterImageHandler->exists(
                $this->makePathRelativeToShopSource(Path::join($directory, "$filename$suffix.$extension"))
            )
        ) {
            $fileCounter++;
            //removing "(any digit)" from file name end
            $temporaryName = $stringHandler->preg_replace("/\($fileCounter\)/", '', $temporaryName);
            $filename = "{$temporaryName}($fileCounter)";
        }
        return "$filename$suffix.$extension";
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

    /**
     * @param string $url
     * @return bool
     */
    private function isUrlSchemaValid(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) === false ? false : true;
    }

    /**
     * @param string $url
     * @return bool
     */
    private function isUrlAccessible(string $url): bool
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_NOBODY, true);

        $result = curl_exec($curl);

        if ($result !== false) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode === 200) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     */
    private function copyMasterImage(string $source, string $destination): bool
    {
        $copied = false;
        try {
            $this->getContainer()->get(MasterImageHandlerBridgeInterface::class)
                ->copy(
                    $source,
                    $this->makePathRelativeToShopSource($destination)
                );
            $copied = true;
        } catch (\Throwable $exception) {
            $this->addErrorMessageToDisplay($exception->getMessage());
        }
        return $copied;
    }

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     */
    private function uploadMasterImage(string $source, string $destination): bool
    {
        $uploaded = false;
        try {
            $this->getContainer()->get(MasterImageHandlerBridgeInterface::class)
                ->upload(
                    $source,
                    $this->makePathRelativeToShopSource($destination)
                );
            $uploaded = true;
        } catch (\Throwable $exception) {
            $this->addErrorMessageToDisplay($exception->getMessage());
        }
        return $uploaded;
    }

    private function addErrorMessageToDisplay($message): void
    {
        $exception = oxNew(ExceptionToDisplay::class);
        $exception->setMessage($message);
        Registry::getUtilsView()->addErrorToDisplay($exception, false);
    }

    /**
     * @param string $path
     * @return string
     */
    private function makePathRelativeToShopSource(string $path): string
    {
        return Path::makeRelative($path, (new Facts())->getSourcePath());
    }
}
