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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

// checks if GD library version getter does not exist
if (!function_exists("getGdVersion")) {
    /**
     * Returns GD library version
     *
     * @return int
     */
    function getGdVersion()
    {
        return oxRegistry::getConfig()->getConfigParam('iUseGDVersion');
    }
}

// checks if image creation function does not exist
if (!function_exists("copyAlteredImage")) {
    /**
     * Creates and copies the resized image
     *
     * @param string $sDestinationImage file + path of destination
     * @param string $sSourceImage      file + path of source
     * @param int    $iNewWidth         new width of the image
     * @param int    $iNewHeight        new height of the image
     * @param array  $aImageInfo        additional info
     * @param string $sTarget           target file path
     * @param int    $iGdVer            used gd version
     *
     * @return bool
     */
    function copyAlteredImage($sDestinationImage, $sSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer)
    {
        if ($iGdVer == 1) {
            $blSuccess = imagecopyresized($sDestinationImage, $sSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $aImageInfo[0], $aImageInfo[1]);
        } else {
            $blSuccess = imagecopyresampled($sDestinationImage, $sSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $aImageInfo[0], $aImageInfo[1]);
        }

        return $blSuccess;
    }
}

// checks if image size calculator does nor exist
if (!function_exists("calcImageSize")) {
    /**
     * Calculates proportional new image size
     *
     * @param int $iDesiredWidth  expected image width
     * @param int $iDesiredHeight expected image height
     * @param int $iPrefWidth     original image width
     * @param int $iPrefHeight    original image height
     *
     * @return array
     */
    function calcImageSize($iDesiredWidth, $iDesiredHeight, $iPrefWidth, $iPrefHeight)
    {
        // #1837/1177M - do not resize smaller pictures
        if ($iDesiredWidth < $iPrefWidth || $iDesiredHeight < $iPrefHeight) {
            if ($iPrefWidth >= $iPrefHeight * ((float) ($iDesiredWidth / $iDesiredHeight))) {
                $iNewHeight = round(($iPrefHeight * (float) ($iDesiredWidth / $iPrefWidth)), 0);
                $iNewWidth = $iDesiredWidth;
            } else {
                $iNewHeight = $iDesiredHeight;
                $iNewWidth = round(($iPrefWidth * (float) ($iDesiredHeight / $iPrefHeight)), 0);
            }
        } else {
            $iNewWidth = $iPrefWidth;
            $iNewHeight = $iPrefHeight;
        }

        return array($iNewWidth, $iNewHeight);
    }
}

// sets 0755 permissions for given file
if (!function_exists("makeReadable")) {
    /**
     * Sets 0755 permissions for given file and returns name of affected file
     *
     * @param string $sTarget name of file
     *
     * @return string
     */
    function makeReadable($sTarget)
    {
        $blChmodState = false;
        if (file_exists($sTarget) && is_readable($sTarget)) {
            $blChmodState = @chmod($sTarget, 0755);
            if (defined('OXID_PHP_UNIT')) {
                @chmod($sTarget, 0777);
            }
        }

        return $blChmodState ? $sTarget : false;
    }
}

if (!function_exists("checkSizeAndCopy")) {
    /**
     * Checks if preferred image dimensions size matches defined in config;
     * in case it matches - copies original image to new location, returns
     * copying state - TRUE/FALSe else - returns array with new dimensions
     * array( $iNewWidth, $iNewHeight );
     *
     * @param string $sSrc        image source file name
     * @param string $sTarget     target location
     * @param int    $iWidth      preferred width
     * @param int    $iHeight     preferred height
     * @param int    $iOrigWidth  original width
     * @param int    $iOrigHeight preferred height
     *
     * @return mixed
     */
    function checkSizeAndCopy($sSrc, $sTarget, $iWidth, $iHeight, $iOrigWidth, $iOrigHeight)
    {
        list($iNewWidth, $iNewHeight) = calcImageSize($iWidth, $iHeight, $iOrigWidth, $iOrigHeight);
        if ($iNewWidth == $iOrigWidth && $iNewHeight == $iOrigHeight) {
            return copy($sSrc, $sTarget);
        } else {
            return array($iNewWidth, $iNewHeight);
        }
    }
}

// checks if GIF resizer does not exist
if (!function_exists("resizeGif")) {
    /**
     * Creates resized GIF image. Returns path of new file if creation
     * succeed. On error returns FALSE
     *
     * @param string $sSrc            GIF source
     * @param string $sTarget         new image location
     * @param int    $iWidth          new width
     * @param int    $iHeight         new height
     * @param int    $iOriginalWidth  original width
     * @param int    $iOriginalHeight original height
     * @param int    $iGDVer          GD library version
     *
     * @return string | false
     */
    function resizeGif($sSrc, $sTarget, $iWidth, $iHeight, $iOriginalWidth, $iOriginalHeight, $iGDVer)
    {
        $aResult = checkSizeAndCopy($sSrc, $sTarget, $iWidth, $iHeight, $iOriginalWidth, $iOriginalHeight);
        if (is_array($aResult)) {
            list($iNewWidth, $iNewHeight) = $aResult;
            $hDestinationImage = ($iGDVer == 1) ? imagecreate($iNewWidth, $iNewHeight) : imagecreatetruecolor($iNewWidth, $iNewHeight);
            // Correction for Bug:6291 - When the source is not in GIF format, imagecreatefromgif will not work.
            $hSourceImage = imagecreatefromfile($sSrc);
            $iTransparentColor = imagecolorresolve($hSourceImage, 255, 255, 255);
            $iFillColor = imagecolorresolve($hDestinationImage, 255, 255, 255);
            imagefill($hDestinationImage, 0, 0, $iFillColor);
            imagecolortransparent($hSourceImage, $iTransparentColor);
            if ($iGDVer == 1) {
                imagecopyresized($hDestinationImage, $hSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOriginalWidth, $iOriginalHeight);
            } else {
                imagecopyresampled($hDestinationImage, $hSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOriginalWidth, $iOriginalHeight);
            }
            imagecolortransparent($hDestinationImage, $iFillColor);
            imagegif($hDestinationImage, $sTarget);
            imagedestroy($hDestinationImage);
            imagedestroy($hSourceImage);
        }

        return makeReadable($sTarget);
    }
}

// checks if PNG resizer does not exist
if (!function_exists("resizePng")) {
    /**
     * Creates resized PNG image. Returns path of new file if creation
     * succeded. On error returns FALSE
     *
     * @param string   $sSrc              JPG source
     * @param string   $sTarget           new image location
     * @param int      $iWidth            new width
     * @param int      $iHeight           new height
     * @param int      $aImageInfo        original width
     * @param int      $iGdVer            GD library version
     * @param resource $hDestinationImage destination image handle
     *
     * @return string | false
     */
    function resizePng($sSrc, $sTarget, $iWidth, $iHeight, $aImageInfo, $iGdVer, $hDestinationImage)
    {
        $aResult = checkSizeAndCopy($sSrc, $sTarget, $iWidth, $iHeight, $aImageInfo[0], $aImageInfo[1]);
        if (is_array($aResult)) {
            list($iNewWidth, $iNewHeight) = $aResult;
            if ($hDestinationImage === null) {
                $hDestinationImage = $iGdVer == 1 ? imagecreate($iNewWidth, $iNewHeight) : imagecreatetruecolor($iNewWidth, $iNewHeight);
            }
            // Correction for Bug:6291 - When the source is not in PNG format, imagecreatefrompng will not work.
            $hSourceImage = imagecreatefromfile($sSrc);
            if (!imageistruecolor($hSourceImage)) {
                $hDestinationImage = imagecreate($iNewWidth, $iNewHeight);
                // fix for transparent images sets image to transparent
                $imgWhite = imagecolorallocate($hDestinationImage, 255, 255, 255);
                imagefill($hDestinationImage, 0, 0, $imgWhite);
                imagecolortransparent($hDestinationImage, $imgWhite);
                //end of fix
            } else {
                imagealphablending($hDestinationImage, false);
                imagesavealpha($hDestinationImage, true);
            }
            if (copyAlteredImage($hDestinationImage, $hSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer)) {
                imagepng($hDestinationImage, $sTarget);
                imagedestroy($hDestinationImage);
                imagedestroy($hSourceImage);
            }
        }

        return makeReadable($sTarget);
    }
}

// checks if JPG resizer does not exist
if (!function_exists("resizeJpeg")) {
    /**
     * Creates resized JPG image. Returns path of new file if creation
     * succeed. On error returns FALSE
     *
     * @param string   $sSrc              JPG source
     * @param string   $sTarget           new image location
     * @param int      $iWidth            new width
     * @param int      $iHeight           new height
     * @param int      $aImageInfo        original width
     * @param int      $iGdVer            GD library version
     * @param resource $hDestinationImage destination image handle
     * @param int      $iDefQuality       new image quality
     *
     * @return string | false
     */
    function resizeJpeg($sSrc, $sTarget, $iWidth, $iHeight, $aImageInfo, $iGdVer, $hDestinationImage, $iDefQuality)
    {
        $aResult = checkSizeAndCopy($sSrc, $sTarget, $iWidth, $iHeight, $aImageInfo[0], $aImageInfo[1]);
        if (is_array($aResult)) {
            list($iNewWidth, $iNewHeight) = $aResult;
            if ($hDestinationImage === null) {
                $hDestinationImage = $iGdVer == 1 ? imagecreate($iNewWidth, $iNewHeight) : imagecreatetruecolor($iNewWidth, $iNewHeight);
            }
            // Correction for Bug:6291 - When the source is not in JPEG format, imagecreatefromjpeg will not work.
            $hSourceImage = imagecreatefromfile($sSrc);
            if (copyAlteredImage($hDestinationImage, $hSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer)) {
                imagejpeg($hDestinationImage, $sTarget, $iDefQuality);
                imagedestroy($hDestinationImage);
                imagedestroy($hSourceImage);
            }
        }

        return makeReadable($sTarget);
    }
}

if (!function_exists("imagecreatefromfile")) {
    /**
     * Create a new image from file or URL
     *
     * @deprecated since v5.3 (2017-03-27); imagecreatefromfile will not be used in v6.
     *
     * @return resource an image resource identifier on success, false on errors.
     */
    function imagecreatefromfile($sSrc)
    {
        $hSourceImage = null;

        $sSrcType = preg_replace("/.*\.(png|jp(e)?g|gif)$/", "\\1", $sSrc);
        switch ($sSrcType) {
            case "png":
                $hSourceImage = imagecreatefrompng($sSrc);
                break;
            case "jpg":
            case "jpeg":
                $hSourceImage = imagecreatefromjpeg($sSrc);
                break;
            case "gif":
                $hSourceImage = imagecreatefromgif($sSrc);
                break;
        }

        return $hSourceImage;
    }
}
