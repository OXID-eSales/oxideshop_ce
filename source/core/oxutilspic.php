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
 * Including pictures generator functions file
 */
require_once getShopBasePath() . "core/utils/oxpicgenerator.php";

/**
 * Image manipulation class
 */
class oxUtilsPic extends oxSuperCfg
{
    /**
     * Image types 'enum'
     *
     * @var array
     */
    protected $_aImageTypes = array("GIF" => IMAGETYPE_GIF, "JPG" => IMAGETYPE_JPEG, "PNG" => IMAGETYPE_PNG, "JPEG" => IMAGETYPE_JPEG);

    /**
     * oxUtils class instance.
     *
     * @var oxutilspic
     */
    private static $_instance = null;

    /**
     * Returns image utils instance
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxUtilsPic") instead
     *
     * @return oxUtilsPic
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxUtilsPic");
    }


    /**
     * Resizes image to desired width and height, returns true on success.
     *
     * @param string $sSrc           Source of image file
     * @param string $sTarget        Target to write resized image file
     * @param mixed  $iDesiredWidth  Width of resized image
     * @param mixed  $iDesiredHeight Height of resized image
     *
     * @return bool
     */
    public function resizeImage( $sSrc, $sTarget, $iDesiredWidth, $iDesiredHeight )
    {
        $blResize = false;

        // use this GD Version
        if ( ( $iUseGDVersion = getGdVersion() ) && function_exists( 'imagecreate' ) &&
             file_exists( $sSrc ) && ( $aImageInfo = @getimagesize( $sSrc ) ) ) {

            $myConfig = $this->getConfig();
            list( $iWidth, $iHeight ) = calcImageSize( $iDesiredWidth, $iDesiredHeight, $aImageInfo[0], $aImageInfo[1] );
            $blResize = $this->_resize( $aImageInfo, $sSrc, null, $sTarget, $iWidth, $iHeight, $iUseGDVersion, $myConfig->getConfigParam( 'blDisableTouch' ), $myConfig->getConfigParam( 'sDefaultImageQuality' ) );
        }
        return $blResize;
    }


    /**
     * deletes the given picutre and checks before if the picture is deletable
     *
     * @param string $sPicName        Name of picture file
     * @param string $sAbsDynImageDir the absolute image diectory, where to delete the given image ($myConfig->getPictureDir(false))
     * @param string $sTable          in which table
     * @param string $sField          table field value
     *
     * @return bool
     */
    public function safePictureDelete( $sPicName, $sAbsDynImageDir, $sTable, $sField )
    {
        $blDelete = false;
        if ( $this->_isPicDeletable( $sPicName, $sTable, $sField ) ) {
            $blDelete = $this->_deletePicture( $sPicName, $sAbsDynImageDir );
        }
        return $blDelete;
    }

    /**
     * Removes picture file from disk.
     *
     * @param string $sPicName        name of picture
     * @param string $sAbsDynImageDir the absolute image diectory, where to delete the given image ($myConfig->getPictureDir(false))
     *
     * @return null
     */
    protected function _deletePicture( $sPicName, $sAbsDynImageDir )
    {
        $blDeleted = false;
        $myConfig  = $this->getConfig();

        if ( !$myConfig->isDemoShop() && ( strpos( $sPicName, 'nopic.jpg' ) === false ||
             strpos( $sPicName, 'nopic_ico.jpg' ) === false ) ) {

            $sFile = "$sAbsDynImageDir/$sPicName";

            if ( file_exists( $sFile ) && is_file( $sFile ) ) {
                $blDeleted = unlink( $sFile );
            }

            if ( !$myConfig->getConfigParam( 'sAltImageUrl' ) ) {
                // deleting various size generated images
                $sGenPath = str_replace( '/master/', '/generated/', $sAbsDynImageDir );
                $aFiles = glob( "{$sGenPath}*/{$sPicName}" );
                if ( is_array($aFiles) ) {
                    foreach ( $aFiles as $sFile ) {
                        $blDeleted = unlink( $sFile );
                    }
                }
            }
        }
        return $blDeleted;
    }


    /**
     * Checks if current picture file is used in more than one table entry, returns
     * true if one, false if more than one.
     *
     * @param string $sPicName Name of picture file
     * @param string $sTable   in which table
     * @param string $sField   table field value
     *
     * @return bool
     */
    protected function _isPicDeletable( $sPicName, $sTable, $sField )
    {
        if ( !$sPicName || strpos( $sPicName, 'nopic.jpg' ) !== false || strpos( $sPicName, 'nopic_ico.jpg' ) !== false ) {
            return false;
        }

        $oDb = oxDb::getDb();
        $iCountUsed = $oDb->getOne( "select count(*) from $sTable where $sField = ".$oDb->quote( $sPicName ). " group by $sField ", false, false);
        return $iCountUsed > 1 ? false : true;
    }

    /**
     * Deletes picture if new is uploaded or changed
     *
     * @param object $oObject         in whitch obejct search for old values
     * @param string $sPicTable       pictures table
     * @param string $sPicField       where picture are stored
     * @param string $sPicType        how does it call in $_FILE array
     * @param string $sPicDir         directory of pic
     * @param array  $aParams         new input text array
     * @param string $sAbsDynImageDir the absolute image diectory, where to delete the given image ($myConfig->getPictureDir(false))
     *
     * @return null
     */
    public function overwritePic( $oObject, $sPicTable, $sPicField, $sPicType, $sPicDir, $aParams, $sAbsDynImageDir )
    {
        $blDelete = false;
        $sPic = $sPicTable.'__'.$sPicField;
        if ( isset( $oObject->{$sPic} ) &&
             ( $_FILES['myfile']['size'][$sPicType.'@'.$sPic] > 0 || $aParams[$sPic] != $oObject->{$sPic}->value ) ) {

            $sImgDir = $sAbsDynImageDir . oxRegistry::get("oxUtilsFile")->getImageDirByType($sPicType);
            $blDelete = $this->safePictureDelete($oObject->{$sPic}->value, $sImgDir, $sPicTable, $sPicField );
        }

        return $blDelete;
    }

    /**
     * Resizes and saves GIF image. This method was separated due to GIF transparency problems.
     *
     * @param string $sSrc            image file
     * @param string $sTarget         destination file
     * @param int    $iNewWidth       new width
     * @param int    $iNewHeight      new height
     * @param int    $iOriginalWidth  original width
     * @param int    $iOriginalHeigth original height
     * @param int    $iGDVer          GD packet version
     * @param bool   $blDisableTouch  false if "touch()" should be called
     *
     * @return bool
     */
    protected function _resizeGif( $sSrc, $sTarget, $iNewWidth, $iNewHeight, $iOriginalWidth, $iOriginalHeigth, $iGDVer, $blDisableTouch )
    {
        return resizeGif( $sSrc, $sTarget, $iNewWidth, $iNewHeight, $iOriginalWidth, $iOriginalHeigth, $iGDVer, $blDisableTouch );
    }

    /**
     * type dependant image resizing
     *
     * @param array  $aImageInfo        Contains information on image's type / width / height
     * @param string $sSrc              source image
     * @param string $hDestinationImage Destination Image
     * @param string $sTarget           Resized Image target
     * @param int    $iNewWidth         Resized Image's width
     * @param int    $iNewHeight        Resized Image's height
     * @param mixed  $iGdVer            used GDVersion, if null or false returns false
     * @param bool   $blDisableTouch    false if "touch()" should be called for gif resizing
     * @param string $iDefQuality       quality for "imagejpeg" function
     *
     * @return bool
     */
    protected function _resize( $aImageInfo, $sSrc, $hDestinationImage, $sTarget, $iNewWidth, $iNewHeight, $iGdVer, $blDisableTouch, $iDefQuality )
    {
        startProfile("PICTURE_RESIZE");

        $blSuccess = false;
        switch ( $aImageInfo[2] ) { //Image type
            case ( $this->_aImageTypes["GIF"] ):
                //php does not process gifs until 7th July 2004 (see lzh licensing)
                if ( function_exists( "imagegif" ) ) {
                    $blSuccess = resizeGif( $sSrc, $sTarget, $iNewWidth, $iNewHeight, $aImageInfo[0], $aImageInfo[1], $iGdVer );
                }
                break;
            case ( $this->_aImageTypes["JPEG"] ):
            case ( $this->_aImageTypes["JPG"] ):
                $blSuccess = resizeJpeg( $sSrc, $sTarget, $iNewWidth, $iNewHeight, $aImageInfo, $iGdVer, $hDestinationImage, $iDefQuality );
                break;
            case ( $this->_aImageTypes["PNG"] ):
                $blSuccess = resizePng( $sSrc, $sTarget, $iNewWidth, $iNewHeight, $aImageInfo, $iGdVer, $hDestinationImage );
                break;
        }

        if ( $blSuccess && !$blDisableTouch ) {
            @touch( $sTarget );
        }

        stopProfile("PICTURE_RESIZE");

        return $blSuccess;
    }

    /**
     * create and copy the resized image
     *
     * @param string $sDestinationImage file + path of destination
     * @param string $sSourceImage      file + path of source
     * @param int    $iNewWidth         new width of the image
     * @param int    $iNewHeight        new height of the image
     * @param array  $aImageInfo        additional info
     * @param string $sTarget           target file path
     * @param int    $iGdVer            used gd version
     * @param bool   $blDisableTouch    wether Touch() should be called or not
     *
     * @return null
     */
    protected function _copyAlteredImage( $sDestinationImage, $sSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer, $blDisableTouch )
    {
        $blSuccess = copyAlteredImage( $sDestinationImage, $sSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer );
        if ( !$blDisableTouch && $blSuccess ) {
            @touch( $sTarget );
        }
        return $blSuccess;
    }

}
