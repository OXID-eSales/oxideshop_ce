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

/**
 * class for pictures processing
 * @package core
 */
class oxPictureHandler extends oxSuperCfg
{
    /**
     * oxUtils class instance.
     *
     * @var oxutils
     */
    private static $_instance = null;

    /**
     * Returns object instance
     *
     * @deprecated since v5.0 (2012-08-10); Use Registry getter instead - oxRegistry::get("oxPictureHandler");
     *
     * @return oxPictureHandler
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxPictureHandler");
    }

    /**
     * Deletes master picture and all images generated from it.
     * If third parameter is false, skips master image delete, only
     * all generated images will be deleted.
     *
     * @param oxArticle $oObject               article object
     * @param int       $iIndex                master picture index
     * @param bool      $blDeleteMasterPicture delete master picture, default is true
     *
     * @return null
     */
    public function deleteArticleMasterPicture( $oObject, $iIndex, $blDeleteMasterPicture = true )
    {
        $myConfig   = $this->getConfig();
        $myUtilsPic = oxRegistry::get("oxUtilsPic");
        $oUtilsFile = oxRegistry::get("oxUtilsFile");
        $blGeneratedImagesOnly = !$blDeleteMasterPicture;

        $sAbsDynImageDir = $myConfig->getPictureDir(false);
        $sMasterImage = basename( $oObject->{"oxarticles__oxpic".$iIndex}->value );
        if ( !$sMasterImage || $sMasterImage == "nopic.jpg" ) {
            return;
        }

        $aPic = array("sField"    => "oxpic".$iIndex,
                      "sDir"      => $oUtilsFile->getImageDirByType( "M".$iIndex, $blGeneratedImagesOnly ),
                      "sFileName" => $sMasterImage);

        $blDeleted = $myUtilsPic->safePictureDelete( $aPic["sFileName"], $sAbsDynImageDir . $aPic["sDir"], "oxarticles", $aPic["sField"] );
        if ( $blDeleted ) {
            $this->deleteZoomPicture( $oObject, $iIndex );

            $aDelPics = array();
            if ( $iIndex == 1 ) {
                // deleting generated main icon picture if custom main icon
                // file name not equal with generated from master picture
                if ( $this->getMainIconName( $sMasterImage ) != basename($oObject->oxarticles__oxicon->value) ) {
                    $aDelPics[] = array("sField"    => "oxpic1",
                                        "sDir"      => $oUtilsFile->getImageDirByType( "ICO", $blGeneratedImagesOnly ),
                                        "sFileName" => $this->getMainIconName( $sMasterImage ));
                }

                // deleting generated thumbnail picture if custom thumbnail
                // file name not equal with generated from master picture
                if ( $this->getThumbName( $sMasterImage ) != basename($oObject->oxarticles__oxthumb->value) ) {
                    $aDelPics[] = array("sField"    => "oxpic1",
                                        "sDir"      => $oUtilsFile->getImageDirByType( "TH", $blGeneratedImagesOnly ),
                                        "sFileName" => $this->getThumbName( $sMasterImage ));
                }
            }

            foreach ( $aDelPics as $aPic ) {
                $myUtilsPic->safePictureDelete( $aPic["sFileName"], $sAbsDynImageDir . $aPic["sDir"], "oxarticles", $aPic["sField"] );
            }
        }

        //deleting custom zoom pic (compatibility mode)
        if ( $oObject->{"oxarticles__oxzoom".$iIndex}->value ) {
            if ( basename($oObject->{"oxarticles__oxzoom".$iIndex}->value) !== "nopic.jpg" ) {
                // deleting old zoom picture
                $this->deleteZoomPicture( $oObject, $iIndex );
            }
        }

    }

    /**
     * Deletes custom main icon, which name is specified in oxicon field.
     *
     * @param oxArticle $oObject article object
     *
     * @return null
     */
    public function deleteMainIcon( $oObject )
    {
        if ( ( $sMainIcon = $oObject->oxarticles__oxicon->value ) ) {
            $sPath = $this->getConfig()->getPictureDir( false ) . oxRegistry::get("oxUtilsFile")->getImageDirByType( "ICO" );
            oxRegistry::get("oxUtilsPic")->safePictureDelete( $sMainIcon, $sPath, "oxarticles", "oxicon" );
        }
    }

    /**
     * Deletes custom thumbnail, which name is specified in oxthumb field.
     *
     * @param oxArticle $oObject article object
     *
     * @return null
     */
    public function deleteThumbnail( $oObject )
    {
        if ( ( $sThumb = $oObject->oxarticles__oxthumb->value ) ) {
            // deleting article main icon and thumb picture
            $sPath = $this->getConfig()->getPictureDir( false ) . oxRegistry::get("oxUtilsFile")->getImageDirByType( "TH" );
            oxRegistry::get("oxUtilsPic")->safePictureDelete( $sThumb, $sPath, "oxarticles", "oxthumb" );
        }
    }

    /**
     * Deletes custom zoom picture, which name is specified in oxzoom field.
     *
     * @param oxArticle $oObject article object
     * @param int       $iIndex  zoom picture index
     *
     * @return null
     */
    public function deleteZoomPicture( $oObject, $iIndex )
    {
        // checking if oxzoom field exists
        $oDbHandler = oxNew( "oxDbMetaDataHandler" );
        $iZoomPicCount = (int) $this->getConfig()->getConfigParam( 'iZoomPicCount' );

        if ( $iIndex > $iZoomPicCount || !$oDbHandler->fieldExists( "oxzoom".$iIndex, "oxarticles" ) ) {
            if ( $sZoomPicName = $this->getZoomName( $oObject->{"oxarticles__oxpic".$iIndex}->value, $iIndex ) ) {
                $sFieldToCheck = "oxpic".$iIndex;
            } else {
                return;
            }
        } else {
            $sZoomPicName = basename( $oObject->{"oxarticles__oxzoom".$iIndex}->value );
            $sFieldToCheck = "oxzoom".$iIndex;
        }

        if ( $sZoomPicName && $sZoomPicName != "nopic.jpg" ) {
            // deleting zoom picture
            $sPath = $this->getConfig()->getPictureDir( false ) . oxRegistry::get("oxUtilsFile")->getImageDirByType( "Z" . $iIndex );
            oxRegistry::get("oxUtilsPic")->safePictureDelete( $sZoomPicName, $sPath, "oxarticles", $sFieldToCheck );
        }
    }

    /**
     * Returns article picture icon name for selected article picture
     *
     * @param string $sFilename file name
     *
     * @return string
     */
    public function getIconName( $sFilename )
    {
        return $sFilename;
    }

    /**
     * Returns article main icon name generated from master picture
     *
     * @param string $sMasterImageFile master image file name
     *
     * @return string
     */
    public function getMainIconName( $sMasterImageFile )
    {
        return $this->_getBaseMasterImageFileName( $sMasterImageFile );
    }

    /**
     * Returns thumb image name generated from master picture
     *
     * @param string $sMasterImageFile master image file name
     *
     * @return string
     */
    public function getThumbName( $sMasterImageFile )
    {
        return basename( $sMasterImageFile );
    }

    /**
     * Returns zoom image name generated from master picture
     *
     * @param string $sMasterImageFile master image file name
     * @param string $iIndex           master image index
     *
     * @return string
     */
    public function getZoomName( $sMasterImageFile, $iIndex )
    {
        return basename( $sMasterImageFile );
    }

    /**
     * Gets master image file name and removes suffics (e.g. _p1) from file end.
     *
     * @param string $sMasterImageFile master image file name
     *
     * @return null
     */
    protected function _getBaseMasterImageFileName( $sMasterImageFile )
    {
        return basename( $sMasterImageFile );
    }

    /**
     * Returns image sizes from provided config array
     *
     * @param mixed  $aImgSizes array or string of sizes in format x*y
     * @param string $sIndex    index in array
     *
     * @return array
     */
    public function getImageSize($aImgSizes, $sIndex = null)
    {
        if (isset($sIndex) && is_array($aImgSizes) && isset($aImgSizes[$sIndex])) {
            $aSize = explode('*', $aImgSizes[$sIndex]);
        } elseif (is_string ($aImgSizes)) {
            $aSize = explode('*', $aImgSizes);
        }
        if (2 == count($aSize)) {
            $x = (int)$aSize[0];
            $y = (int)$aSize[1];
            if ($x && $y) {
                return $aSize;
            }
        }
        return null;
    }

    /**
     * Returns dir/url info for given image file
     *
     * @param string $sFilePath path to file
     * @param string $sFile     filename in pictures dir
     * @param bool   $blAdmin   is admin mode ?
     * @param bool   $blSSL     is ssl ?
     * @param int    $iLang     language id
     * @param int    $iShopId   shop id
     *
     * @return array
     */
    protected function _getPictureInfo( $sFilePath, $sFile, $blAdmin = false, $blSSL = null, $iLang = null, $iShopId = null )
    {
        // custom server as image storage?
        if ( $sAltUrl = $this->getAltImageUrl($sFilePath, $sFile, $blSSL) ) {
            return array( 'path' => false, 'url'=> $sAltUrl );
        }

        $oConfig = $this->getConfig();
        $sPath = $oConfig->getPicturePath( $sFilePath . $sFile, $blAdmin, $iLang, $iShopId );
        if ( !$sPath ) {
            return array( 'path'=> false, 'url' => false );
        }

        $sDirPrefix = $oConfig->getOutDir();
        $sUrlPrefix = $oConfig->getOutUrl( $blSSL, $blAdmin, $oConfig->getConfigParam( 'blNativeImages' ) );

        return array( 'path' => $sPath, 'url'=> str_replace( $sDirPrefix, $sUrlPrefix, $sPath ) );
    }

    /**
     * Returns alternative image url
     *
     * @param string $sFilePath path to file
     * @param string $sFile     filename in pictures dir
     * @param bool   $blSSL     is ssl ?
     *
     * @return string
     */
    public function getAltImageUrl($sFilePath, $sFile, $blSSL = null)
    {
        $oConfig = $this->getConfig();

        $sAltUrl = $oConfig->getConfigParam('sAltImageUrl');
        if ( !$sAltUrl ) {
            $sAltUrl = $oConfig->getConfigParam('sAltImageDir');
        }

        if ( $sAltUrl ) {
            if ( (is_null($blSSL) && $oConfig->isSsl()) || $blSSL) {

                $sSslAltUrl = $oConfig->getConfigParam('sSSLAltImageUrl');
                if ( !$sSslAltUrl ) {
                    $sSslAltUrl = $oConfig->getConfigParam('sSSLAltImageDir');
                }

                if ( $sSslAltUrl ) {
                    $sAltUrl = $sSslAltUrl;
                }
            }

            if ( !is_null( $sFile ) ) {
                $sAltUrl .= '/'.$sFilePath.$sFile;
            }
        }

        return $sAltUrl;
    }

    /**
     * Returns requested picture url. If image is not available - returns false
     *
     * @param string $sPath    path from pictures/master/
     * @param string $sFile    picture file name
     * @param string $sSize    picture sizes (x, y)
     * @param string $sIndex   picture index [optional]
     * @param string $sAltPath alternative picture path [optional]
     * @param bool   $bSsl     Whether to force SSL
     *
     * @return string | bool
     */
    public function getPicUrl( $sPath, $sFile, $sSize, $sIndex = null, $sAltPath = false, $bSsl = null )
    {
        $sUrl = null;
        if ( $sPath && $sFile && ( $aSize = $this->getImageSize( $sSize, $sIndex ) ) ) {

            $aPicInfo = $this->_getPictureInfo( "master/" . ( $sAltPath ? $sAltPath : $sPath ), $sFile, $this->isAdmin(), $bSsl );
            if ( $aPicInfo['url'] && $aSize[0] && $aSize[1] ) {
                $sDirName = "{$aSize[0]}_{$aSize[1]}_" . $this->getConfig()->getConfigParam( 'sDefaultImageQuality' );
                $sUrl = str_replace( "/master/" . ( $sAltPath ? $sAltPath : $sPath ), "/generated/{$sPath}{$sDirName}/", $aPicInfo['url'] );
            }
        }
        return $sUrl;
    }

    /**
     * Returns requested product picture url. If image is not available - returns url to nopic.jpg
     *
     * @param string $sPath  path from pictures/master/
     * @param string $sFile  picture file name
     * @param string $sSize  picture sizes (x, y)
     * @param string $sIndex picture index [optional]
     * @param bool   $bSsl   Whether to force SSL
     *
     * @return string | bool
     */
    public function getProductPicUrl( $sPath, $sFile, $sSize, $sIndex = null, $bSsl = null )
    {
        $sUrl = null;
        if ( !$sFile || !( $sUrl = $this->getPicUrl( $sPath, $sFile, $sSize, $sIndex, false, $bSsl ) ) ) {
            $sUrl = $this->getPicUrl( $sPath, "nopic.jpg", $sSize, $sIndex, "/", $bSsl );
        }
        return $sUrl;
    }
}
