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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once dirname(__FILE__) . "/../bootstrap.php";

// Checks if base path getter does not exist
if ( !function_exists( "getShopBasePath" ) ) {
    /**
     * Returns shop installation path
     *
     * @return string
     */
    function getShopBasePath()
    {
        return realpath( dirname( __FILE__ ) . '/..' ) . '/';
    }
}


// disables admin
if ( !function_exists( 'isAdmin' )) {
    /**
     * Returns false.
     *
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

// starts shop framework and returns config instance
if ( !function_exists( "getConfig" ) ) {
    /**
     * Starts shop framework and returns config instance
     *
     * @return oxconfig
     */
    function getConfig()
    {
        // custom functions file
        include_once getShopBasePath() . 'modules/functions.php';

        // Generic utility method file
        include_once getShopBasePath() . 'core/oxfunctions.php';

        // initializes singleton config class
        return oxRegistry::getConfig();
    }
}

// Checks if instance name getter does not exist
if ( !function_exists( "getGeneratorInstanceName" ) ) {
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

// checks if GD library version getter does not exist
if ( !function_exists( "getGdVersion" ) ) {
    /**
     * Returns GD library version
     *
     * @return int
     */
    function getGdVersion()
    {
        static $iVersion = null;

        if ( $iVersion === null ) {
            $iVersion = false;
            if ( function_exists( "gd_info" ) ) {
                // extracting GD version from php
                $aInfo = gd_info();
                if ( isset( $aInfo["GD Version"] ) ) {
                    $iVersion = version_compare( preg_replace( "/[^0-9\.]/", "", $aInfo["GD Version"] ), 1, '>' ) ? 2 : 1;
                }
            }

        }
        return $iVersion;
    }
}

// checks if image utils file loader does not exist
if ( !function_exists( "includeImageUtils" ) ) {
    /**
     * Includes image utils
     *
     * @return null
     */
    function includeImageUtils()
    {
        include_once getShopBasePath() . "core/utils/oxpicgenerator.php";
    }
}

/**
 * Image generator class
 */
class oxDynImgGenerator
{
    /**
     * Generator instance
     * @var oxDynImgGenerator
     */
    protected static $_oInstance = null;

    /**
     * Custom headers
     * @var array
     */
    protected $_aHeaders = array();

    /**
     * Allowed image types
     * @var array
     */
    protected $_aAllowedImgTypes = array( "jpg", "jpeg", "png", "gif" );

    /**
     * Image info like size and quality is defined in directory
     * name e.g. 160_160_75, this means width_height_quality
     * @var string
     */
    protected $_sImageInfoSep = "_";

    /**
     * Lockable file handle
     * @var resource
     */
    protected $_hLockHandle = null;

    /**
     * Requested image uri
     * @var string
     */
    protected $_sImageUri = null;

    /**
     * Map of config parameter to requested image path
     * @var array
     */
    protected $_aConfParamToPath = array( // ** product
                                          "sIconsize"             => '/.*\/generated\/product\/(icon|\d+)\/\d+\_\d+\_\d+$/',  // Icon size
                                          "sThumbnailsize"        => '/.*\/generated\/product\/(thumb|\d+)\/\d+\_\d+\_\d+$/', // Thumbnail size
                                          "sZoomImageSize"        => '/.*\/generated\/product\/\d+\/\d+\_\d+\_\d+$/',         // Zoom picture size
                                          "aDetailImageSizes"     => '/.*\/generated\/product\/\d+\/\d+\_\d+\_\d+$/',         // Product picture size

                                          // ** manufacturer/vendor
                                          "sManufacturerIconsize" => '/.*\/generated\/(manufacturer|vendor)\/icon\/\d+\_\d+\_\d+$/', // Manufacturer's|brand logo size

                                          // ** category
                                          "sCatThumbnailsize"     => '/.*\/generated\/category\/thumb\/\d+\_\d+\_\d+$/',     // Category picture size
                                          "sCatIconsize"          => '/.*\/generated\/category\/icon\/\d+\_\d+\_\d+$/',      // Size of a subcategory's picture
                                          "sCatPromotionsize"     => '/.*\/generated\/category\/promo_icon\/\d+\_\d+\_\d+$/' // Category picture size for promotion on startpage
                                        );

    /**
     * Creates and returns picture generator instance
     *
     * @return oxDynImgGenerator
     */
    public static function getInstance()
    {
        if ( self::$_oInstance === null ) {
            $sInstanceName = getGeneratorInstanceName();
            self::$_oInstance = new $sInstanceName();
        }
        return self::$_oInstance;
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call( $sMethod, $aArgs )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( substr( $sMethod, 0, 4) == "UNIT" ) {
                $sMethod = str_replace( "UNIT", "_", $sMethod );
            }
            if ( method_exists( $this, $sMethod)) {
                return call_user_func_array( array( & $this, $sMethod ), $aArgs );
            }
        }

        throw new oxSystemComponentException( "Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ")".PHP_EOL);
    }

    /**
     * Returns shops base path
     *
     * @return string
     */
    protected function _getShopBasePath()
    {
        return getConfig()->getConfigParam( "sShopDir" );
    }

    /**
     * Returns requested image uri
     *
     * @return string
     */
    protected function _getImageUri()
    {
        if ( $this->_sImageUri === null ) {

            $this->_sImageUri = "";
            $sReqPath = "out/pictures/generated";


            $sReqImg = isset( $_SERVER["REQUEST_URI"] ) ? urldecode($_SERVER["REQUEST_URI"]) : "";
            if ( ( $iPos = strpos( $sReqImg, $sReqPath ) ) !== false ) {
                $this->_sImageUri = substr( $sReqImg, $iPos );
            }

            $this->_sImageUri = trim( $this->_sImageUri, "/" );
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
        return basename( $this->_getImageUri() );
    }

    /**
     * Returns path to possible master image
     *
     * @return string
     */
    protected function _getImageMasterPath()
    {
        $sUri  = $this->_getImageUri();
        $sPath = false;

        if ( $sUri && ( $sPath = dirname( dirname( $sUri ) ) ) ) {
            $sPath = preg_replace( "/\/([^\/]*)\/([^\/]*)\/([^\/]*)$/", "/master/\\2/\\3/", $sPath );
        }

        return $sPath;
    }

    /**
     * Returns image info array
     *
     * @return array
     */
    protected function _getImageInfo()
    {
        $aInfo = array();
        if ( ( $sUri = $this->_getImageUri() ) ) {
            $aInfo = explode( $this->_sImageInfoSep, basename( dirname( $sUri ) ) );
        }

        return $aInfo;
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
        $sPath = $this->_getShopBasePath() . $this->_getImageUri();
        return str_replace( $this->_getImageName(), "nopic.jpg", $sPath );
    }

    /**
     * Returns image type used for image generation and header setting
     *
     * @return string
     */
    protected function _getImageType()
    {
        $sType = preg_replace( "/.*\.(png|jp(e)?g|gif)$/", "\\1", $this->_getImageName() );
        $sType = ( strcmp( $sType, "jpg") == 0 ) ? "jpeg" : $sType;
        return in_array( $sType, $this->_aAllowedImgTypes ) ? $sType : false;
    }

    /**
     * Generates PNG type image and returns its location on file system
     *
     * @param string $sSource image source
     * @param string $sTarget image target
     * @param int    $iWidth  image width
     * @param int    $iHeight image height
     *
     * @return string
     */
    protected function _generatePng( $sSource, $sTarget, $iWidth, $iHeight )
    {
        return resizePng( $sSource, $sTarget, $iWidth, $iHeight, @getimagesize( $sSource ), getGdVersion(), null );
    }

    /**
     * Generates JPG type image and returns its location on file system
     *
     * @param string $sSource  image source
     * @param string $sTarget  image target
     * @param int    $iWidth   image width
     * @param int    $iHeight  image height
     * @param int    $iQuality new image quality
     *
     * @return string
     */
    protected function _generateJpg( $sSource, $sTarget, $iWidth, $iHeight, $iQuality )
    {
        return resizeJpeg( $sSource, $sTarget, $iWidth, $iHeight, @getimagesize( $sSource ), getGdVersion(), null, $iQuality );
    }

    /**
     * Generates GIF type image and returns its location on file system
     *
     * @param string $sSource image source
     * @param string $sTarget image target
     * @param int    $iWidth  image width
     * @param int    $iHeight image height
     *
     * @return string
     */
    protected function _generateGif( $sSource, $sTarget, $iWidth, $iHeight )
    {
        $aImageInfo = @getimagesize( $sSource );
        return resizeGif( $sSource, $sTarget, $iWidth, $iHeight, $aImageInfo[0], $aImageInfo[1], getGdVersion() );
    }

    /**
     * Checks if requested image path is valid. If path is valid
     * but is not created - creates directory structure
     *
     * @param string $sPath image path name to check
     *
     * @return bool
     */
    protected function _isTargetPathValid( $sPath )
    {
        $blValid = true;
        $sDir = dirname( trim( $sPath ) );

        // first time folder access?
        if ( ! is_dir( $sDir ) && ( $blValid = $this->_isValidPath( $sDir ) ) ) {
            // creating missing folders
            $blValid = $this->_createFolders( $sDir );
        }

        return $blValid;
    }

    /**
     * Checks if valid and creates missing needed folders
     *
     * @param string $sDir folder(s) to create
     *
     * @return bool
     */
    protected function _createFolders( $sDir )
    {
        $oConfig = getConfig();
        $sPicFolderPath = dirname( $oConfig->getMasterPictureDir() );

        $blDone = false;
        if ( $sPicFolderPath && is_dir( $sPicFolderPath ) ) {

            // if its in main path..
            if ( strcmp( $sPicFolderPath, substr( $sDir, 0, strlen( $sPicFolderPath ) ) ) == 0 ) {
                // folder does not exist yet?
                if ( ! ( $blDone = file_exists( $sDir ) ) ) {
                    clearstatcache();
                    // in case creation did not succeed, maybe another process allready created folder?
                    $iMode = 0755;
                    if ( defined( 'OXID_PHP_UNIT' ) ) {
                        $iMode = 0777;
                    }
                    $blDone = mkdir( $sDir, $iMode, true ) || file_exists( $sDir );
                }
            }
        }

        return $blDone;
    }

    /**
     * Checks if main folder matches requested
     *
     * @param string $sPath image path name to check
     *
     * @return bool
     */
    protected function _isValidPath( $sPath )
    {
        $blValid = false;

        list( $iWidth, $iHeight, $sQuality ) = $this->_getImageInfo();
        if ( $iWidth && $iHeight && $sQuality ) {

            $oConfig = getConfig();
            $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

            // parameter names
            $sNames = '';
            foreach ( $this->_aConfParamToPath as $sParamName => $sPathReg ) {
                if ( preg_match( $sPathReg, $sPath ) ) {
                    if ( $sNames ) {
                        $sNames .= ", ";
                    }
                    $sNames .= $oDb->quote( $sParamName );

                    if ( $sParamName == "sManufacturerIconsize" || $sParamName == "sCatIconsize" ) {
                        $sNames .= ", " . $oDb->quote( "sIconsize" );
                    }
                }
            }

            // any name matching path?
            if ( $sNames ) {

                $sDecodeField = $oConfig->getDecodeValueQuery();

                // selecting shop which image quality matches user given
                $sQ = "select oxshopid from oxconfig where oxvarname = 'sDefaultImageQuality' and
                       {$sDecodeField} = " . $oDb->quote( $sQuality );

                $aShopIds = $oDb->getAll( $sQ );

                // building query:
                // shop id
                $sShopIds = '';
                foreach ( $aShopIds as $aShopId ) {

                    // probably here we can resolve and check shop id to shorten check?


                    if ( $sShopIds ) {
                        $sShopIds .= ", ";
                    }
                    $sShopIds .= $oDb->quote( $aShopId["oxshopid"] );
                }

                // any shop matching quality
                if ( $sShopIds ) {

                    //
                    $sCheckSize = "$iWidth*$iHeight";

                    // selecting config variables to check
                    $sQ = "select oxvartype, {$sDecodeField} as oxvarvalue from oxconfig
                           where oxvarname in ( {$sNames} ) and oxshopid in ( {$sShopIds} ) order by oxshopid";

                    $aValues = $oDb->getAll( $sQ );
                    foreach ( $aValues as $aValue ) {
                        $aConfValues = (array) $oConfig->decodeValue( $aValue["oxvartype"], $aValue["oxvarvalue"] );
                        foreach ( $aConfValues as $sValue ) {
                            if ( strcmp( $sCheckSize, $sValue ) == 0 ) {
                                $blValid = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $blValid;
    }

    /**
     * Generates requested image
     *
     * @param string $sImageSource image source
     * @param string $sImageTarget image target
     *
     * @return string
     */
    protected function _generateImage( $sImageSource, $sImageTarget )
    {
        $sPath = false;

        if ( getGdVersion() !== false && $this->_isTargetPathValid( $sImageTarget ) && ( $sImageType = $this->_getImageType() ) ) {

            // including generator files
            includeImageUtils();

            // in case lock file creation failed should check if another process did not created image yet
            if ( $this->_lock( $sImageTarget ) ) {

                // extracting image info - size/quality
                list( $iWidth, $iHeight, $iQuality ) = $this->_getImageInfo();
                switch ( $sImageType ) {
                    case "png":
                        $sPath = $this->_generatePng( $sImageSource, $sImageTarget, $iWidth, $iHeight );
                        break;
                    case "jpeg":
                        $sPath = $this->_generateJpg( $sImageSource, $sImageTarget, $iWidth, $iHeight, $iQuality );
                        break;
                    case "gif":
                        $sPath = $this->_generateGif( $sImageSource, $sImageTarget, $iWidth, $iHeight );
                        break;
                }

                // releasing..
                if ( $sPath ) {
                    $this->_unlock( $sImageTarget );
                }
            } else {
                // assuming that image was created by another process
                $sPath = file_exists( $sImageTarget ) ? $sImageTarget : false;
            }
        }

        return $sPath;
    }

    /**
     * Returns lock file name
     *
     * @param string $sName original file name
     *
     * @return string
     */
    protected function _getLockName( $sName )
    {
        return "$sName.lck";
    }

    /**
     * Locks file and returns locking state
     *
     * @param string $sSource source file which should be locked
     *
     * @return bool
     */
    protected function _lock( $sSource )
    {
        $blLocked  = false;
        $sLockName = $this->_getLockName( $sSource );

        // creating lock file
        $this->_hLockHandle = @fopen( $this->_getLockName( $sSource ), "w" );
        if ( $this->_hLockHandle ) {
             if ( !( $blLocked = flock( $this->_hLockHandle, LOCK_EX ) ) ) {
                // on failure - closing
                 fclose( $rHandle );
             }
        }

        // in case system does not support file lockings
        if ( !$blLocked ) {
            // start a blank file to inform other processes we are dealing with it.
            if (!( file_exists( $this->_getLockName( $sSource ) ) && abs( time() - filectime( $this->_getLockName( $sSource ) ) < 40 ) ) ) {
                if ( $this->_hLockHandle = @fopen( $this->_getLockName( $sSource ), "w" ) ) {
                    $blLocked = true;
                }
            }
        }

        return $blLocked;
    }

    /**
     * Deletes lock file
     *
     * @param string $sSource source file which should be locked
     *
     * @return null
     */
    protected function _unlock( $sSource )
    {
        if ( $this->_hLockHandle ) {
            flock( $this->_hLockHandle, LOCK_UN );
            fclose( $this->_hLockHandle );
            unlink( $this->_getLockName( $sSource ) );
        }
    }

    /**
     * Returns path to image file which needs should be rendered. If file cannot
     * be found - return false
     *
     * @param string $sAbsPath absolute requested image path (not url, but real path on file system)
     *
     * @return string | false
     */
    public function getImagePath( $sAbsPath = false )
    {
        if ( $sAbsPath ) {
            $this->_sImageUri = str_replace( $this->_getShopBasePath(), "", $sAbsPath );
        }

        $sImagePath  = false;
        $sMasterPath = $this->_getImageMasterPath();

        // building base path + extracting image name + extracting master image path
        $sMasterImagePath = $this->_getShopBasePath() . $sMasterPath . $this->_getImageName();

        if ( file_exists( $sMasterImagePath ) ) {
            $sGenImagePath = $this->_getImageTarget();
        } else {
            // nopic master path
            $sMasterImagePath = $this->_getShopBasePath() . dirname( dirname( $sMasterPath ) ) . "/nopic.jpg";
            $sGenImagePath    = $this->_getNopicImageTarget();

            // 404 header for nopic
            $this->_setHeader( "HTTP/1.0 404 Not Found" );
        }

        // checking if master image is accessible
        if ( file_exists( $sGenImagePath ) ) {
            $sImagePath = $sGenImagePath;
        } elseif ( file_exists( $sMasterImagePath ) ) {
            // generating image
            $sImagePath = $this->_generateImage( $sMasterImagePath, $sGenImagePath );
        }

        if ( $sImagePath ) {
            // image type header
            $this->_setHeader( "Content-Type: image/" . $this->_getImageType() );
        } else {
            // unable to output any file
            $this->_setHeader( "HTTP/1.0 404 Not Found" );
        }

        return $sImagePath;
    }

    /**
     * Creates and outputs requested image. If source file was not found -
     * tries to render related "nopic.jpg". If "nopic.jpg" is not available -
     * sends 404 header to browser
     *
     * @return null
     */
    public function outputImage()
    {
        $blBuffer = true;
        if ( defined( 'OXID_PHP_UNIT' ) ) {
           $blBuffer = false;
        }

        // starting output buffering
        if ( $blBuffer ) {
           ob_start();
        }

        //
        $sImgPath = $this->getImagePath();

        // cleaning extra output
        if ( $blBuffer ) {
            ob_clean();
        }

        // outputting headers
        $aHeaders = $this->_getHeaders();
        foreach ( $aHeaders as $sHeader ) {
            header( $sHeader );
        }

        // sending headers
        if ( $blBuffer ) {
            ob_end_flush();
        }

        // file is generated?
        if ( $sImgPath ) {
            // outputting file
            @readfile( $sImgPath );
        }
    }

    /**
     * Custom header setter
     *
     * @param string $sHeader header
     *
     * @return null
     */
    protected function _setHeader( $sHeader )
    {
        $this->_aHeaders[] = $sHeader;
    }

    /**
     * Returs headers array
     *
     * @return array
     */
    protected function _getHeaders()
    {
        return $this->_aHeaders;
    }
}