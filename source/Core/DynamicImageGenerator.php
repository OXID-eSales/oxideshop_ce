<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace {

    use OxidEsales\Eshop\Core\DynamicImageGenerator;

    /** Checks if instance name getter does not exist */
    if (!function_exists("getGeneratorInstanceName")) {
        /**
         * Returns image generator instance name
         *
         * @return string
         */
        function getGeneratorInstanceName()
        {
            return DynamicImageGenerator::class;
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

    use OxidEsales\Eshop\Core\DatabaseProvider;
    use OxidEsales\Eshop\Core\Exception\StandardException;
    use OxidEsales\Eshop\Core\Exception\SystemComponentException;
    use OxidEsales\Eshop\Core\Registry;
    use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
    use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
    use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
    use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
    use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
    use Symfony\Component\Filesystem\Path;

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
        protected $_aHeaders = [];

        /**
         * Allowed image types
         *
         * @var array
         */
        protected $_aAllowedImgTypes = ["jpg", "jpeg", "png", "gif", "webp"];

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
        protected array $resolutionConfigParameters = [
            "iconSize",
            "thumbnailSize",
            "zoomImageSize",
            "detailImageSize",
            "manufacturerIconSize",
            "manufacturerPictureSize",
            "manufacturerThumbnailSize",
            "manufacturerPromotionSize",
            "categoryThumbnailSize",
            "categoryIconSize",
            "categoryPromotionSize"
        ];

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
         * @param array  $arguments Argument array
         * @return false|mixed
         * @throws SystemComponentException
         */
        public function __call($method, $arguments)
        {
            if (method_exists($this, $method)) {
                return call_user_func_array([& $this, $method], $arguments);
            }
            throw new SystemComponentException(
                "Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL
            );
        }

        /**
         * Returns shops base path
         *
         * @return string
         */
        protected function getShopBasePath()
        {
            return ContainerFacade::getParameter('oxid_esales.shop_source_directory') . DIRECTORY_SEPARATOR;
        }

        /**
         * Returns requested image uri
         *
         * @return string
         */
        protected function getImageUri()
        {
            if ($this->_sImageUri === null) {
                $this->_sImageUri = "";
                $reqPath = 'out/pictures/generated';

                $reqImg = isset($_SERVER["REQUEST_URI"]) ? urldecode($_SERVER["REQUEST_URI"]) : "";
                $reqImg = str_replace('//', '/', $reqImg);
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
        protected function getImageName()
        {
            return basename($this->getImageUri());
        }

        /**
         * Returns path to possible master image
         *
         * @return string
         */
        protected function getImageMasterPath()
        {
            $uri = $this->getImageUri();
            $path = false;

            if ($uri && ($path = dirname(dirname($uri)))) {
                $path = preg_replace("/\/([^\/]*)\/([^\/]*)\/([^\/]*)$/", "/master/\\2/\\3/", $path);
            }

            return $path;
        }

        private function parseMediaPathFromUrl(): string
        {
            $uri = $this->getImageUri();

            $originalImageDirectory = preg_replace('~(^|/)generated/~', '$1', dirname($uri, 2));

            return Path::join(
                $this->getShopBasePath(),
                $originalImageDirectory,
                $this->getImageName()
            );
        }

        /**
         * Returns image info array
         *
         * @return array
         */
        protected function getImageInfo()
        {
            $info = [0, 0, 0];
            if (($uri = $this->getImageUri())) {
                $info = explode($this->_sImageInfoSep, basename(dirname($uri)));
            }

            return $info;
        }

        /**
         * Returns full requested image path on file system
         *
         * @return string
         */
        protected function getImageTarget()
        {
            return $this->getShopBasePath() . $this->getImageUri();
        }

        /**
         * Nopic image path
         *
         * @return string
         */
        protected function getNopicImageTarget()
        {
            $path = $this->getShopBasePath() . $this->getImageUri();

            return str_replace($this->getImageName(), $this->getNopicFilename(), $path);
        }

        private function getNopicFilename(): string
        {
            if (Registry::getConfig()->getConfigParam('blConvertImagesToWebP')) {
                return 'nopic.webp';
            }

            return 'nopic.jpg';
        }

        /**
         * Returns image type used for image generation and header setting
         *
         * @return string
         */
        protected function getImageType()
        {
            $fileExtension = strtolower(pathinfo($this->getImageName(), PATHINFO_EXTENSION));
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
        protected function generatePng($source, $target, $width, $height)
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
        protected function generateJpg($source, $target, $width, $height, $quality)
        {
            return resizeJpeg(
                $source,
                $target,
                $width,
                $height,
                @getimagesize($source),
                getGdVersion(),
                null,
                $quality
            );
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
        protected function generateGif($source, $target, $width, $height)
        {
            $imageInfo = @getimagesize($source);

            return resizeGif(
                $source,
                $target,
                $width,
                $height,
                $imageInfo[0],
                $imageInfo[1],
                $this->validateGdVersion()
            );
        }

        protected function generateWebp(string $source, string $target, int $width, int $height, int $quality): string
        {
            return resizeWebp($source, $target, $width, $height, $quality);
        }

        /**
         * Checks if requested image path is valid. If path is valid
         * but is not created - creates directory structure
         *
         * @param string $path image path name to check
         *
         * @return bool
         */
        protected function isTargetPathValid($path)
        {
            $valid = true;
            $dir = dirname(trim($path));

            // first time folder access?
            if (!is_dir($dir) && ($valid = $this->isValidPath($dir))) {
                // creating missing folders
                $valid = $this->createFolders($dir);
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
        protected function createFolders($dir)
        {
            $config = Registry::getConfig();
            $picFolderPath = dirname($config->getMasterPictureDir());

            $done = false;
            if ($picFolderPath && is_dir($picFolderPath)) {
                // if its in main path..
                if (Path::isBasePath($picFolderPath, $dir)) {
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

        protected function isValidPath($path)
        {
            list($width, $height, $quality) = $this->getImageInfo();
            if ($width && $height && $quality) {
                $checkSize = "$width*$height";

                $shopIds = DatabaseProvider::getDb()->getCol(
                    "select oxshopid from oxconfig where oxvarname = 'sDefaultImageQuality' and oxvarvalue = :quality",
                    ['quality' => $quality]
                );

                foreach ($shopIds as $shopId) {
                    if ($this->isSizeConfiguredForShop((int) $shopId, $checkSize)) {
                        return true;
                    }
                }

                return $this->isSizeAllowed($checkSize);
            }

            return false;
        }

        private function isSizeConfiguredForShop(int $shopId, string $checkSize): bool
        {
            try {
                $themeId = ContainerFacade::get(ThemeStateServiceInterface::class)->getActiveThemeId($shopId);
                $configuration = ContainerFacade::get(ThemeConfigurationResolverInterface::class)
                    ->resolve($themeId, $shopId);
            } catch (ActiveThemeNotFoundException | ThemeConfigurationNotFoundException) {
                return false;
            }

            foreach ($this->resolutionConfigParameters as $paramName) {
                if ($checkSize === (string) $configuration->getSettingByName($paramName)?->getValue()) {
                    return true;
                }
            }

            return false;
        }

        private function isSizeAllowed(string $checkSize): bool
        {
            return in_array(
                $checkSize,
                ContainerFacade::getParameter('oxid_esales.theme.media.allowed_image_sizes'),
                true
            );
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
        protected function generateImage($imageSource, $imageTarget)
        {
            $generatedImagePath = false;
            list($targetWidth, $targetHeight, $targetQuality) = $this->getImageInfo();

            $fileExtensionSource = strtolower(pathinfo($imageSource, PATHINFO_EXTENSION));
            $fileExtensionTarget = strtolower(pathinfo($imageTarget, PATHINFO_EXTENSION));

            // Do some validation and return false on failure
            if (
                !$this->validateGdVersion()
                || !$this->validateFileExist($imageSource)
                || !$this->isTargetPathValid($imageTarget)
                || !$this->validateImageFileExtension($fileExtensionSource)
                || !$this->validateImageFileExtension($fileExtensionTarget)
                || $fileExtensionSource !== $fileExtensionTarget
            ) {
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
            if ($this->lock($imageTarget)) {
                // extracting image info - size/quality
                switch ($fileExtensionSource) {
                    case "png":
                        $generatedImagePath = $this->generatePng(
                            $imageSource,
                            $imageTarget,
                            $targetWidth,
                            $targetHeight
                        );
                        break;
                    case "jpeg":
                    case "jpg":
                        $generatedImagePath = $this->generateJpg(
                            $imageSource,
                            $imageTarget,
                            $targetWidth,
                            $targetHeight,
                            $targetQuality
                        );
                        break;
                    case "gif":
                        $generatedImagePath = $this->generateGif(
                            $imageSource,
                            $imageTarget,
                            $targetWidth,
                            $targetHeight
                        );
                        break;
                    case "webp":
                        $generatedImagePath = $this->generateWebp(
                            $imageSource,
                            $imageTarget,
                            $targetWidth,
                            $targetHeight,
                            $targetQuality
                        );
                        break;
                }
                // target must always be unlocked, no matter what the result of the former image generation was.
                $this->unlock($imageTarget);
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
        protected function getLockName($name)
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
        protected function lock($source)
        {
            $locked = false;
            $lockName = $this->getLockName($source);

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
        protected function unlock($source)
        {
            if (is_resource($this->_hLockHandle)) {
                flock($this->_hLockHandle, LOCK_UN);
                fclose($this->_hLockHandle);
                $this->_hLockHandle = null;
                unlink($this->getLockName($source));
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
                $this->_sImageUri = str_replace($this->getShopBasePath(), "", $absPath);
            }

            $imagePath = false;
            $masterPath = $this->getImageMasterPath();

            // building base path + extracting image name + extracting master image path
            $masterImagePath = $this->getShopBasePath() . $masterPath . $this->getImageName();

            if (!file_exists($masterImagePath)) {
                $masterImagePath = $this->parseMediaPathFromUrl();
            }

            if (
                Registry::getConfig()->getConfigParam('blConvertImagesToWebP') &&
                !file_exists($masterImagePath)
            ) {
                $this->convertImageIfOriginalExists($masterImagePath);
            }

            if (file_exists($masterImagePath)) {
                $genImagePath = $this->getImageTarget();
            } else {
                // nopic master path
                $masterImagePath = $this->getShopBasePath() . dirname($masterPath, 2) . "/" . $this->getNopicFilename();
                $genImagePath = $this->getNopicImageTarget();

                // 404 header for nopic
                $this->setHeader("HTTP/1.1 404 Not Found");
            }

            // checking if master image is accessible
            if (file_exists($genImagePath)) {
                $imagePath = $genImagePath;
            } elseif (file_exists($masterImagePath)) {
                // generating image
                $imagePath = $this->generateImage($masterImagePath, $genImagePath);
            }

            if ($this->validateFileExist($imagePath)) {
                // image Content-Type
                $contentType = mime_content_type($imagePath);
                $this->setHeader("Content-Type: $contentType;");
            } else {
                // unable to output any file
                $this->setHeader("HTTP/1.1 404 Not Found");
            }

            return $imagePath;
        }

        private function convertImageIfOriginalExists(string $desiredFilename): void
        {
            $pathParts = pathinfo($desiredFilename);
            $originalFilename = $pathParts['dirname'] . '/' . $pathParts['filename'];

            $sourceImage = false;
            switch (pathinfo($originalFilename, PATHINFO_EXTENSION)) {
                case 'png':
                    $sourceImage = imagecreatefrompng($originalFilename);
                    break;
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($originalFilename);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($originalFilename);
                    break;
            }

            if ($sourceImage) {
                $quality = Registry::getConfig()->getConfigParam('sDefaultImageQuality');
                imagewebp($sourceImage, $desiredFilename, $quality);
            }
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
            $headers = $this->getHeaders();
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
        protected function setHeader($header)
        {
            $this->_aHeaders[] = $header;
        }

        /**
         * Return headers array
         *
         * @return array
         */
        protected function getHeaders()
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
         * @param string $filePath
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
         * @param string $imageFilePath
         *
         * @return array
         */
        protected function getImageDimensions($imageFilePath)
        {
            try {
                list($width, $height) = getimagesize($imageFilePath);
                $imageDimensions = [$width, $height];
            } catch (\Exception $exception) {
                $imageDimensions = [0,0];
            }

            return $imageDimensions;
        }
    }
}
