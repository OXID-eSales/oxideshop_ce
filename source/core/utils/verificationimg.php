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

// #1428C - spam spider prevention
if (isset($_GET['e_mac'])) {
    $sEMac = $_GET['e_mac'];
} else {
    return;
}

require_once '../oxfunctions.php';

if ( !function_exists( 'generateVerificationImg' ) ) {

    /**
     * Generates image
     *
     * @param string $sMac verification code
     *
     * @return null
     */
    function generateVerificationImg( $sMac )
    {
        $iWidth = 80;
        $iHeight = 18;
        $iFontSize = 14;

        if ( function_exists( 'imagecreatetruecolor' ) ) {
            // GD2
            $oImage = imagecreatetruecolor( $iWidth, $iHeight );
        } elseif ( function_exists( 'imagecreate' ) ) {
            // GD1
            $oImage = imagecreate( $iWidth, $iHeight );
        } else {
            // GD not found
            return;
        }

        $iTextX = ( $iWidth - strlen($sMac)*imagefontwidth($iFontSize))/2;
        $iTextY = ( $iHeight - imagefontheight($iFontSize) )/2;

        $aColors = array();
        $aColors["text"] = imagecolorallocate($oImage, 0, 0, 0);
        $aColors["shadow1"] = imagecolorallocate($oImage, 200, 200, 200);
        $aColors["shadow2"] = imagecolorallocate($oImage, 100, 100, 100);
        $aColors["background"] = imagecolorallocate($oImage, 255, 255, 255);
        $aColors["border"] = imagecolorallocate($oImage, 0, 0, 0);

        imagefill($oImage, 0, 0, $aColors["background"]);
        imagerectangle ( $oImage, 0, 0, $iWidth-1, $iHeight-1, $aColors["border"] );
        imagestring( $oImage, $iFontSize, $iTextX + 1, $iTextY + 0, $sMac, $aColors["shadow2"] );
        imagestring( $oImage, $iFontSize, $iTextX + 0, $iTextY + 1, $sMac, $aColors["shadow1"] );
        imagestring( $oImage, $iFontSize, $iTextX, $iTextY, $sMac, $aColors["text"] );

        header( 'Content-type: image/png' );
        imagepng( $oImage );
        imagedestroy($oImage );
    }
}

if ( !function_exists( 'strRem' ) ) {
    /**
     * OXID specific string manipulation method
     *
     * @param string $sVal string
     *
     * @return string
     */
    function strRem( $sVal)
    {
        $oCfg = new oxConfKey();

        $sKey = $oCfg->sConfigKey;
        $sKey = str_repeat( $sKey, strlen( $sVal ) / strlen( $sKey ) + 5 );

        $sVal = substr( $sVal, 3 );
        $sVal = str_replace( '!', '=', $sVal );
        $sVal = base64_decode( $sVal );
        $sVal = $sVal ^ $sKey;
        $sVal = str_rot13($sVal);

        return substr( $sVal, 2, -2 );
    }
}

/**
 * Simple class returning config key.
 */
class oxConfKey
{
    /**
     * @var $sConfigKey string
     */
    public $sConfigKey;

    /**
     * Config class constructor.
     */
    function __construct()
    {
        include_once '../oxconfk.php';
    }

    /**
     * Config key getter.
     *
     * @return string
     */
    function get()
    {
        return $this->sConfigKey;
    }
}

$sMac = strRem($sEMac);

generateVerificationImg($sMac);