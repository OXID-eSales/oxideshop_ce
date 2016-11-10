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

namespace {

    /** Checks if instance name getter does not exist */
    if (!function_exists("getGeneratorInstanceName")) {
        /**
         * Returns image generator instance name
         *
         * @return string
         */
        function getGeneratorInstanceName()
        {
            return "oxdynimggenerator";
        }
    }

    /** Checks if GD library version getter does not exist */
    if (!function_exists("getGdVersion")) {
        /**
         * Returns GD library version
         *
         * @return int
         */
        function getGdVersion()
        {
            static $version = null;

            if ($version === null) {
                $version = false;
                if (function_exists("gd_info")) {
                    // extracting GD version from php
                    $info = gd_info();
                    if (isset($info["GD Version"])) {
                        $version = version_compare(preg_replace("/[^0-9\.]/", "", $info["GD Version"]), 1, '>') ? 2 : 1;
                    }
                }

            }

            return $version;
        }
    }

    /** Checks if image utils file loader does not exist */
    if (!function_exists("includeImageUtils")) {
        /**
         * Includes image utils
         */
        function includeImageUtils()
        {
            include_once __DIR__ . "/utils/oxpicgenerator.php";
        }
    }
}

namespace OxidEsales\EshopCommunity\Core {

    use oxDb;
    use OxidEsales\EshopCommunity\Core\Exception\StandardException;
    use oxSystemComponentException;

    /**
     * Image generator class
     */
    class DynamicImageGenerator
    {
        /**
         * Generator instance
         *
         * @var DynamicImageGenerator
         */
        protected static $_oInstance = null;

        /**
         * Custom headers
         *
         * @var array
         */
        protected $_aHeaders = array();

        /**
         * Allowed image types
         *
         * @var array
         */
        protected $_aAllowedImgTypes = array("jpg", "jpeg", "png", "gif");

        /**
         * Image info like size and quality is defined in directory
         * name e.g. 160_160_75, this means width_height_quality
         *
         * @var string
         */
        protected $_sImageInfoSep = "_";

        /**
         * Lockable file handle
         *
         * @var resource
         */
        protected $_hLockHandle = null;

        /**
         * Requested image uri
         *
         * @var string
         */
        protected $_sImageUri = null;

        /**
         * Map of config parameter to requested image path
         *
         * @var array
         */
        protected $_aConfParamToPath = array( // ** product
            "sIconsize"             => '/.*\/generated\/product\/(icon|\d+)\/\d+\_\d+\_\d+$/', // Icon size
            "sThumbnailsize"        => '/.*\/generated\/product\/(thumb|\d+)\/\d+\_\d+\_\d+$/', // Thumbnail size
            "sZoomImageSize"        => '/.*\/generated\/product\/\d+\/\d+\_\d+\_\d+$/', // Zoom picture size
            "aDetailImageSizes"     => '/.*\/generated\/product\/\d+\/\d+\_\d+\_\d+$/', // Product picture size

            // ** manufacturer/vendor
            "sManufacturerIconsize" => '/.*\/generated\/(manufacturer|vendor)\/icon\/\d+\_\d+\_\d+$/', // Manufacturer's|brand logo size

            // ** category
            "sCatThumbnailsize"     => '/.*\/generated\/category\/thumb\/\d+\_\d+\_\d+$/', // Category picture size
            "sCatIconsize"          => '/.*\/generated\/category\/icon\/\d+\_\d+\_\d+$/', // Size of a subcategory's picture
            "sCatPromotionsize"     => '/.*\/generated\/category\/promo_icon\/\d+\_\d+\_\d+$/' // Category picture size for promotion on startpage
        );

        /**
         * Creates and returns picture generator instance
         *
         * @return DynamicImageGenerator
         */
        public static function getInstance()
        {
            if (self::$_oInstance === null) {
                $instanceName = getGeneratorInstanceName();
                self::$_oInstance = new $instanceName();
            }

            return self::$_oInstance;
        }

        /**
         * Only used for convenience in UNIT tests by doing so we avoid
         * writing extended classes for testing protected or private methods
         *
         * @param string $method Methods name
         * @param array  $args   Argument array
         *
         * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
         *
         * @return string
         */
        public function __call($method, $args)
        {
            if (defined('OXID_PHP_UNIT')) {
                if (substr($method, 0, 4) == "UNIT") {
                    $method = str_replace("UNIT", "_", $method);
                }
                if (method_exists($this, $method)) {
                    return call_user_func_array(array(& $this, $method), $args);
                }
            }

            throw new oxSystemComponentException("Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL);
        }

        /**
         * Returns shops base path
         *
         * @return string
         */
        protected function _getShopBasePath()
        {
            return Registry::getConfig()->getConfigParam("sShopDir");
        }

        /**
         * Returns requested image uri
         *
         * @return string
         */
        protected function _getImageUri()
        {
            if ($this->_sImageUri === null) {
                $this->_sImageUri = "";
                $reqPath = 'out/pictures/generated';

                $reqImg = isset($_SERVER["REQUEST_URI"]) ? urldecode($_SERVER["REQUEST_URI"]) : "";
                if (($pos = strpos($reqImg, $reqPath)) !== false) {
                    $this->_sImageUri = substr($reqImg, $pos);
                }

                $this->_sImageUri = trim($this->_sImageUri, "/");
            }

            return $this->_sImageUri;
        }

        /**
         * Returns requested image name
         *
         * @return string
         */
        protected function _getImageName()
        {
            return basename($this->_getImageUri());
        }

        /**
         * Returns path to possible master image
         *
         * @return string
         */
        protected function _getImageMasterPath()
        {
            $uri = $this->_getImageUri();
            $path = false;

            if ($uri && ($path = dirname(dirname($uri)))) {
                $path = preg_replace("/\/([^\/]*)\/([^\/]*)\/([^\/]*)$/", "/master/\\2/\\3/", $path);
            }

            return $path;
        }

        /**
         * Returns image info array
         *
         * @return array
         */
        protected function _getImageInfo()
        {
            $info = [];
            if (($uri = $this->_getImageUri())) {
                $info = explode($this->_sImageInfoSep, basename(dirname($uri)));
            }

            return $info;
        }

        /**
         * Returns full requested image path on file system
         *
         * @return string
         */
        protected function _getImageTarget()
        {
            return $this->_getShopBasePath() . $this->_getImageUri();
        }

        /**
         * Nopic image path
         *
         * @return string
         */
        protected function _getNopicImageTarget()
        {
            $path = $this->_getShopBasePath() . $this->_getImageUri();

            return str_replace($this->_getImageName(), "nopic.jpg", $path);
        }

        /**
         * Returns image type used for image generation and header setting
         *
         * @return string
         */
        protected function _getImageType()
        {
            $fileExtension = strtolower(pathinfo($this->_getImageName(), PATHINFO_EXTENSION));
            if (!$this->validateImageFileExtension($fileExtension)) {
                return false;
            }

            if ('jpg' == $fileExtension) {
                $type = 'jpeg';
            } else {
                $type = $fileExtension;
            }

            return $type;
        }

        /**
         * Generates PNG type image and returns its location on file system
         *
         * @param string $source image source
         * @param string $target image target
         * @param int    $width  image width
         * @param int    $height image height
         *
         * @return string
         */
        protected function _generatePng($source, $target, $width, $height)
        {
            return resizePng($source, $target, $width, $height, @getimagesize($source), getGdVersion(), null);
        }

        /**
         * Generates JPG type image and returns its location on file system
         *
         * @param string $source  image source
         * @param string $target  image target
         * @param int    $width   image width
         * @param int    $height  image height
         * @param int    $quality new image quality
         *
         * @return string
         */
        protected function _generateJpg($source, $target, $width, $height, $quality)
        {
            return resizeJpeg($source, $target, $width, $height, @getimagesize($source), getGdVersion(), null, $quality);
        }

        /**
         * Generates GIF type image and returns its location on file system
         *
         * @param string $source image source
         * @param string $target image target
         * @param int    $width  image width
         * @param int    $height image height
         *
         * @return string
         */
        protected function _generateGif($source, $target, $width, $height)
        {
            $imageInfo = @getimagesize($source);

            return resizeGif($source, $target, $width, $height, $imageInfo[0], $imageInfo[1], $this->validateGdVersion());
        }

        /**
         * Checks if requested image path is valid. If path is valid
         * but is not created - creates directory structure
         *
         * @param string $path image path name to check
         *
         * @return bool
         */
        protected function _isTargetPathValid($path)
        {
            $valid = true;
            $dir = dirname(trim($path));

            // first time folder access?
            if (!is_dir($dir) && ($valid = $this->_isValidPath($dir))) {
                // creating missing folders
                $valid = $this->_createFolders($dir);
            }

            return $valid;
        }

        /**
         * Checks if valid and creates missing needed folders
         *
         * @param string $dir folder(s) to create
         *
         * @return bool
         */
        protected function _createFolders($dir)
        {
            $config = Registry::getConfig();
            $picFolderPath = dirname($config->getMasterPictureDir());

            $done = false;
            if ($picFolderPath && is_dir($picFolderPath)) {
                // if its in main path..
                if (strcmp($picFolderPath, substr($dir, 0, strlen($picFolderPath))) == 0) {
                    // folder does not exist yet?
                    if (!($done = file_exists($dir))) {
                        clearstatcache();
                        // in case creation did not succeed, maybe another process allready created folder?
                        $mode = 0755;
                        $done = mkdir($dir, $mode, true) || file_exists($dir);
                    }
                }
            }

            return $done;
        }

        /**
         * Checks if main folder matches requested
         *
         * @param string $path image path name to check
         *
         * @return bool
         */
        protected function _isValidPath($path)
        {
            $valid = false;

            list($width, $height, $quality) = $this->_getImageInfo();
            if ($width && $height && $quality) {
                $config = Registry::getConfig();
                $db = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

                // parameter names
                $names = '';
                foreach ($this->_aConfParamToPath as $paramName => $pathReg) {
                    if (preg_match($pathReg, $path)) {
                        if ($names) {
                            $names .= ", ";
                        }
                        $names .= $db->quote($paramName);

                        if ($paramName == "sManufacturerIconsize" || $paramName == "sCatIconsize") {
                            $names .= ", " . $db->quote("sIconsize");
                        }
                    }
                }

                // any name matching path?
                if ($names) {
                    $decodeField = $config->getDecodeValueQuery();

                    // selecting shop which image quality matches user given
                    $q = "select oxshopid from oxconfig where oxvarname = 'sDefaultImageQuality' and
                       {$decodeField} = " . $db->quote($quality);

                    $shopIdsArray = $db->getAll($q);

                    // building query:
                    // shop id
                    $shopIds = '';
                    foreach ($shopIdsArray as $shopId) {
                        // probably here we can resolve and check shop id to shorten check?


                        if ($shopIds) {
                            $shopIds .= ", ";
                        }
                        $shopIds .= $db->quote($shopId["oxshopid"]);
                    }

                    // any shop matching quality
                    if ($shopIds) {
                        //
                        $checkSize = "$width*$height";

                        // selecting config variables to check
                        $q = "select oxvartype, {$decodeField} as oxvarvalue from oxconfig
                           where oxvarname in ( {$names} ) and oxshopid in ( {$shopIds} ) order by oxshopid";

                        $values = $db->getAll($q);
                        foreach ($values as $value) {
                            $confValues = (array) $config->decodeValue($value["oxvartype"], $value["oxvarvalue"]);
                            foreach ($confValues as $confValue) {
                                if (strcmp($checkSize, $confValue) == 0) {
                                    $valid = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            return $valid;
        }

        /**
         * Converts a given source image into a target image
         *
         * @param string $imageSource File path of the source image
         * @param string $imageTarget File path of the image to be generated
         *
         * @throws StandardException If the path of imageTarget and generated image are not the same
         *
         * @return bool|string Return false on failure or file path of the generated image on success
         */
        protected function _generateImage($imageSource, $imageTarget)
        {
            $generatedImagePath = false;
            list($targetWidth, $targetHeight, $targetQuality) = $this->_getImageInfo();

            $fileExtensionSource = strtolower(pathinfo($imageSource, PATHINFO_EXTENSION));
            $fileExtensionTarget = strtolower(pathinfo($imageTarget, PATHINFO_EXTENSION));

            // Do some validation and return false on failure
            if (!$this->validateGdVersion()) {
                return false;
            }
            if (!$this->validateFileExist($imageSource)) {
                return false;
            }
            if (!$this->_isTargetPathValid($imageTarget)) {
                return false;
            }
            if (!$this->validateImageFileExtension($fileExtensionSource)) {
                return false;
            }
            if (!$this->validateImageFileExtension($fileExtensionTarget)) {
                return false;
            }
            if ($fileExtensionSource !== $fileExtensionTarget) {
                return false;
            }

            if ($this->validateFileExist($imageTarget)) {
                list($currentWidth, $currentHeight) = $this->getImageDimensions($imageTarget);
                if (($currentWidth == $targetWidth) && ($currentHeight == $targetHeight)) {
                    return $imageTarget;
                }

            }

            // including generator files
            includeImageUtils();

            /**
             * There may be a different process trying to generate this image at the same moment.
             * Get a lock in order not to write at the same file at the same time.
             */
            if ($this->_lock($imageTarget)) {
                // extracting image info - size/quality
                switch ($fileExtensionSource) {
                    case "png":
                        $generatedImagePath = $this->_generatePng($imageSource, $imageTarget, $targetWidth, $targetHeight);
                        break;
                    case "jpeg":
                    case "jpg":
                        $generatedImagePath = $this->_generateJpg($imageSource, $imageTarget, $targetWidth, $targetHeight, $targetQuality);
                        break;
                    case "gif":
                        $generatedImagePath = $this->_generateGif($imageSource, $imageTarget, $targetWidth, $targetHeight);
                        break;
                }
                // target must always be unlocked, no matter what the result of the former image generation was.
                $this->_unlock($imageTarget);
            }
            if ($generatedImagePath && $generatedImagePath != $imageTarget) {
                throw new StandardException('imageTarget path and generatedImage path differ');
            }

            return $generatedImagePath;
        }

        /**
         * Returns lock file name
         *
         * @param string $name original file name
         *
         * @return string
         */
        protected function _getLockName($name)
        {
            return "$name.lck";
        }

        /**
         * Locks file and returns locking state
         *
         * @param string $source source file which should be locked
         *
         * @return bool
         */
        protected function _lock($source)
        {
            $locked = false;
            $lockName = $this->_getLockName($source);

            // creating lock file
            $this->_hLockHandle = @fopen($lockName, "w");
            if (is_resource($this->_hLockHandle)) {
                if (!($locked = flock($this->_hLockHandle, LOCK_EX))) {
                    // on failure - closing
                    fclose($this->_hLockHandle);
                    $this->_hLockHandle = null;
                }
            }

            // in case system does not support file lockings
            if (!$locked) {
                // start a blank file to inform other processes we are dealing with it.
                if (!(file_exists($lockName) && abs(time() - filectime($lockName) < 40))) {
                    if ($this->_hLockHandle = @fopen($lockName, "w")) {
                        $locked = true;
                    }
                }
            }

            return $locked;
        }

        /**
         * Deletes lock file
         *
         * @param string $source source file which should be locked
         */
        protected function _unlock($source)
        {
            if (is_resource($this->_hLockHandle)) {
                flock($this->_hLockHandle, LOCK_UN);
                fclose($this->_hLockHandle);
                $this->_hLockHandle = null;
                unlink($this->_getLockName($source));
            }
        }

        /**
         * Returns the file path of an image as requested by self::_getImageUri().
         * If the requested image does not exist, if will be rendered from the master image.
         * If the master image does not exist, a nopic image in the same directory as the requested image is shown.
         * If the nopic image does not exist, it will be generated in with the same dimensions and quality as the requested
         * image.
         * If the nopic image does not exist, the method returns false.
         *
         * @param bool $absPath absolute requested image path (not url, but real path on file system)
         *
         * @return string|false
         */
        public function getImagePath($absPath = false)
        {
            if ($absPath) {
                $this->_sImageUri = str_replace($this->_getShopBasePath(), "", $absPath);
            }

            $imagePath = false;
            $masterPath = $this->_getImageMasterPath();

            // building base path + extracting image name + extracting master image path
            $masterImagePath = $this->_getShopBasePath() . $masterPath . $this->_getImageName();

            if (file_exists($masterImagePath)) {
                $genImagePath = $this->_getImageTarget();
            } else {
                // nopic master path
                $masterImagePath = $this->_getShopBasePath() . dirname(dirname($masterPath)) . "/nopic.jpg";
                $genImagePath = $this->_getNopicImageTarget();

                // 404 header for nopic
                $this->_setHeader("HTTP/1.1 404 Not Found");
            }

            // checking if master image is accessible
            if (file_exists($genImagePath)) {
                $imagePath = $genImagePath;
            } elseif (file_exists($masterImagePath)) {
                // generating image
                $imagePath = $this->_generateImage($masterImagePath, $genImagePath);
            }

            if ($this->validateFileExist($imagePath)) {
                // image Content-Type
                $contentType = mime_content_type($imagePath);
                $this->_setHeader("Content-Type: $contentType;");
            } else {
                // unable to output any file
                $this->_setHeader("HTTP/1.1 404 Not Found");
            }

            return $imagePath;
        }

        /**
         * Creates and outputs requested image. If source file was not found -
         * tries to render related "nopic.jpg". If "nopic.jpg" is not available -
         * sends 404 header to browser
         */
        public function outputImage()
        {
            $buffer = true;

            // starting output buffering
            if ($buffer) {
                ob_start();
            }

            //
            $imgPath = $this->getImagePath();

            // cleaning extra output
            if ($buffer) {
                ob_clean();
            }

            // outputting headers
            $headers = $this->_getHeaders();
            foreach ($headers as $header) {
                header($header);
            }

            // sending headers
            if ($buffer) {
                ob_end_flush();
            }

            // file is generated?
            if ($imgPath) {
                // outputting file
                @readfile($imgPath);
            }
        }

        /**
         * @param string $fileExtension Extension to be validated. Validation is case insensitive.
         *
         * @return bool
         */
        protected function validateImageFileExtension($fileExtension)
        {
            return in_array(strtolower($fileExtension), $this->_aAllowedImgTypes);
        }

        /**
         * Custom header setter
         *
         * @param string $header header
         */
        protected function _setHeader($header)
        {
            $this->_aHeaders[] = $header;
        }

        /**
         * Return headers array
         *
         * @return array
         */
        protected function _getHeaders()
        {
            return $this->_aHeaders;
        }

        /**
         * Return true, if the version of the gd library is correct
         *
         * @return bool
         */
        protected function validateGdVersion()
        {
            return getGdVersion() !== false;
        }

        /**
         * Return true, if a given file path exists.
         *
         * @param $filePath
         *
         * @return bool
         */
        protected function validateFileExist($filePath)
        {
            return file_exists($filePath);
        }

        /**
         * Return an array with the dimensions (width x height) of an image file.
         * returns array (0,0), if the dimensions could not be retrieved.
         *
         * @param $imageFilePath
         */
        protected function getImageDimensions($imageFilePath)
        {
            try {
                list($width, $height) = getimagesize($imageFilePath);
                $imageDimensions = array ($width, $height);
            } catch (\Exception $exception) {
                $imageDimensions = array (0,0);
            }

            return $imageDimensions;
        }
    }
}
