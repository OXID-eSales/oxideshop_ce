<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('iUseGDVersion');
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
     * @param string $sTarget           target file path @deprecated
     * @param int    $iGdVer            used gd version @deprecated
     *
     * @return bool
     */
    function copyAlteredImage($sDestinationImage, $sSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer)
    {
        return imagecopyresampled($sDestinationImage, $sSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $aImageInfo[0], $aImageInfo[1]);
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

        return [$iNewWidth, $iNewHeight];
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
            return [$iNewWidth, $iNewHeight];
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
     * @param int    $iGDVer          GD library version @deprecated
     *
     * @return string | false
     */
    function resizeGif($sSrc, $sTarget, $iWidth, $iHeight, $iOriginalWidth, $iOriginalHeight, $iGDVer)
    {
        $aResult = checkSizeAndCopy($sSrc, $sTarget, $iWidth, $iHeight, $iOriginalWidth, $iOriginalHeight);
        if (is_array($aResult)) {
            list($iNewWidth, $iNewHeight) = $aResult;
            $hDestinationImage = imagecreatetruecolor($iNewWidth, $iNewHeight);
            $hSourceImage = imagecreatefromgif($sSrc);

            $iFillColor = imagecolorresolve($hDestinationImage, 255, 255, 255);
            imagefill($hDestinationImage, 0, 0, $iFillColor);
            imagecolortransparent($hDestinationImage, $iFillColor);

            imagecopyresampled($hDestinationImage, $hSourceImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOriginalWidth, $iOriginalHeight);

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
     * @param int      $iGdVer            GD library version @deprecated
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
                $hDestinationImage = imagecreatetruecolor($iNewWidth, $iNewHeight);
            }
            $hSourceImage = imagecreatefrompng($sSrc);
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
     * @param int      $iGdVer            GD library version @deprecated
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
                $hDestinationImage = imagecreatetruecolor($iNewWidth, $iNewHeight);
            }
            $hSourceImage = imagecreatefromstring(file_get_contents($sSrc));
            if (copyAlteredImage($hDestinationImage, $hSourceImage, $iNewWidth, $iNewHeight, $aImageInfo, $sTarget, $iGdVer)) {
                imagejpeg($hDestinationImage, $sTarget, $iDefQuality);
                imagedestroy($hDestinationImage);
                imagedestroy($hSourceImage);
            }
        }

        return makeReadable($sTarget);
    }
}
