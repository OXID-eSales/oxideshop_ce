<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   utils
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Returns path to main script files.
 *
 * @return string
 */
function getShopBasePath()
{
    //file location [shoproot]/core/utils/verificationimg.php
    return realpath(dirname(__FILE__).'/../..').'/';
}


// skip session start for this file
$_GET['skipSession'] = 1;

/**
 * Includes utils class
 */
require_once getShopBasePath() . 'modules/functions.php';

require_once getShopBasePath() . 'core/oxfunctions.php' ;

// Including main ADODB include
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

if ( !function_exists('generateVerificationImg')) {

    /**
     * Genrates image
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
        $aColors["blacground"] = imagecolorallocate($oImage, 255, 255, 255);
        $aColors["border"] = imagecolorallocate($oImage, 0, 0, 0);

        imagefill($oImage, 0, 0, $aColors["blacground"]);
        imagerectangle ( $oImage, 0, 0, $iWidth-1, $iHeight-1, $aColors["border"] );
        imagestring( $oImage, $iFontSize, $iTextX + 1, $iTextY + 0, $sMac, $aColors["shadow2"] );
        imagestring( $oImage, $iFontSize, $iTextX + 0, $iTextY + 1, $sMac, $aColors["shadow1"] );
        imagestring( $oImage, $iFontSize, $iTextX, $iTextY, $sMac, $aColors["text"] );

        header( 'Content-type: image/png' );
        imagepng( $oImage );
        imagedestroy($oImage );
    }
}
// #1428C - spam spider prevension
if (isset($_GET['e_mac'])) {
    $sEMac = $_GET['e_mac'];
} else {
    return;
}

$sMac = oxUtils::getInstance()->strRem($sEMac);

generateVerificationImg($sMac);
