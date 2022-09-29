<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Registry;

/**
 * class for pictures processing
 */
class PictureHandler extends Base
{
    /**
     * Deletes master picture and all images generated from it.
     * If third parameter is false, skips master image delete, only
     * all generated images will be deleted.
     *
     * @param Article $oObject               article object
     * @param int     $iIndex                master picture index
     * @param bool    $blDeleteMasterPicture delete master picture, default is true
     *
     * @return void
     */
    public function deleteArticleMasterPicture($oObject, $iIndex, $blDeleteMasterPicture = true)
    {
        $myConfig = Registry::getConfig();
        $myUtilsPic = Registry::getUtilsPic();
        $oUtilsFile = Registry::getUtilsFile();
        $blGeneratedImagesOnly = !$blDeleteMasterPicture;

        $sAbsDynImageDir = $myConfig->getPictureDir(false);
        $sMasterImage = basename($oObject->{"oxarticles__oxpic" . $iIndex}->value);
        if (!$sMasterImage || $sMasterImage == $this->getNopicFilename()) {
            return;
        }

        $aPic = [
            "sField"    => "oxpic" . $iIndex,
            "sDir"      => $oUtilsFile->getImageDirByType("M" . $iIndex, $blGeneratedImagesOnly),
            "sFileName" => $sMasterImage
        ];

        $blDeleted = $myUtilsPic->safePictureDelete(
            $aPic["sFileName"],
            $sAbsDynImageDir . $aPic["sDir"],
            "oxarticles",
            $aPic["sField"]
        );

        if ($blDeleted) {
            $this->deleteZoomPicture($oObject, $iIndex);

            $aDelPics = [];
            if ($iIndex == 1) {
                // deleting generated main icon picture if custom main icon
                // file name not equal with generated from master picture
                if ($this->getMainIconName($sMasterImage) != basename($oObject->oxarticles__oxicon->value)) {
                    $aDelPics[] = [
                        "sField"    => "oxpic1",
                        "sDir"      => $oUtilsFile->getImageDirByType("ICO", $blGeneratedImagesOnly),
                        "sFileName" => $this->getMainIconName($sMasterImage)
                    ];
                }

                // deleting generated thumbnail picture if custom thumbnail
                // file name not equal with generated from master picture
                if ($this->getThumbName($sMasterImage) != basename($oObject->oxarticles__oxthumb->value)) {
                    $aDelPics[] = [
                        "sField"    => "oxpic1",
                        "sDir"      => $oUtilsFile->getImageDirByType("TH", $blGeneratedImagesOnly),
                        "sFileName" => $this->getThumbName($sMasterImage)
                    ];
                }
            }

            foreach ($aDelPics as $aPic) {
                $myUtilsPic->safePictureDelete(
                    $aPic["sFileName"],
                    $sAbsDynImageDir . $aPic["sDir"],
                    "oxarticles",
                    $aPic["sField"]);
            }
        }

        //deleting custom zoom pic (compatibility mode)
        if ($oObject->{"oxarticles__oxzoom" . $iIndex}->value) {
            if (basename($oObject->{"oxarticles__oxzoom" . $iIndex}->value) !== $this->getNopicFilename()) {
                // deleting old zoom picture
                $this->deleteZoomPicture($oObject, $iIndex);
            }
        }
    }

    /**
     * Deletes custom main icon, which name is specified in oxicon field.
     *
     * @param Article $oObject article object
     */
    public function deleteMainIcon($oObject)
    {
        if (($sMainIcon = $oObject->oxarticles__oxicon->value)) {
            $sPath = Registry::getConfig()->getPictureDir(false) . Registry::getUtilsFile()->getImageDirByType("ICO");
            Registry::getUtilsPic()->safePictureDelete($sMainIcon, $sPath, "oxarticles", "oxicon");
        }
    }

    /**
     * Deletes custom thumbnail, which name is specified in oxthumb field.
     *
     * @param Article $oObject article object
     */
    public function deleteThumbnail($oObject)
    {
        if (($sThumb = $oObject->oxarticles__oxthumb->value)) {
            // deleting article main icon and thumb picture
            $sPath = Registry::getConfig()->getPictureDir(false) . Registry::getUtilsFile()->getImageDirByType("TH");
            Registry::getUtilsPic()->safePictureDelete($sThumb, $sPath, "oxarticles", "oxthumb");
        }
    }

    /**
     * Deletes custom zoom picture, which name is specified in oxzoom field.
     *
     * @param Article $oObject article object
     * @param int     $iIndex  zoom picture index
     *
     * @return null
     */
    public function deleteZoomPicture($oObject, $iIndex)
    {
        // checking if oxzoom field exists
        $oDbHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $iZoomPicCount = (int) Registry::getConfig()->getConfigParam('iZoomPicCount');

        if ($iIndex > $iZoomPicCount || !$oDbHandler->fieldExists("oxzoom" . $iIndex, "oxarticles")) {
            if ($sZoomPicName = $this->getZoomName($oObject->{"oxarticles__oxpic" . $iIndex}->value, $iIndex)) {
                $sFieldToCheck = "oxpic" . $iIndex;
            } else {
                return;
            }
        } else {
            $sZoomPicName = basename($oObject->{"oxarticles__oxzoom" . $iIndex}->value);
            $sFieldToCheck = "oxzoom" . $iIndex;
        }

        if ($sZoomPicName && $sZoomPicName != $this->getNopicFilename()) {
            // deleting zoom picture
            $sPath = Registry::getConfig()->getPictureDir(false) . Registry::getUtilsFile()->getImageDirByType("Z" . $iIndex);
            Registry::getUtilsPic()->safePictureDelete($sZoomPicName, $sPath, "oxarticles", $sFieldToCheck);
        }
    }

    /**
     * Returns article picture icon name for selected article picture
     *
     * @param string $sFilename file name
     *
     * @return string
     */
    public function getIconName($sFilename)
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
    public function getMainIconName($sMasterImageFile)
    {
        return $this->getBaseMasterImageFileName($sMasterImageFile);
    }

    /**
     * Returns thumb image name generated from master picture
     *
     * @param string $sMasterImageFile master image file name
     *
     * @return string
     */
    public function getThumbName($sMasterImageFile)
    {
        return basename($sMasterImageFile);
    }

    /**
     * Returns zoom image name generated from master picture
     *
     * @param string $sMasterImageFile master image file name
     * @param string $iIndex           master image index
     *
     * @return string
     */
    public function getZoomName($sMasterImageFile, $iIndex)
    {
        return basename($sMasterImageFile);
    }

    /**
     * Gets master image file name and removes suffics (e.g. _p1) from file end.
     *
     * @param string $sMasterImageFile master image file name
     *
     * @return null
     */
    protected function getBaseMasterImageFileName($sMasterImageFile)
    {
        return basename($sMasterImageFile);
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
        $aSize = [];
        if (isset($sIndex) && is_array($aImgSizes) && isset($aImgSizes[$sIndex])) {
            $aSize = explode('*', $aImgSizes[$sIndex]);
        } elseif (is_string($aImgSizes)) {
            $aSize = explode('*', $aImgSizes);
        }
        if (is_array($aSize) && 2 == count($aSize)) {
            $x = (int) $aSize[0];
            $y = (int) $aSize[1];
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
    protected function getPictureInfo($sFilePath, $sFile, $blAdmin = false, $blSSL = null, $iLang = null, $iShopId = null)
    {
        // custom server as image storage?
        if ($sAltUrl = $this->getAltImageUrl($sFilePath, $sFile, $blSSL)) {
            return ['path' => false, 'url' => $sAltUrl];
        }

        $oConfig = Registry::getConfig();
        $sPath = $oConfig->getPicturePath($sFilePath . $sFile, $blAdmin, $iLang, $iShopId);
        if (!$sPath) {
            return ['path' => false, 'url' => false];
        }

        $sDirPrefix = $oConfig->getOutDir();
        $sUrlPrefix = $oConfig->getOutUrl($blSSL, $blAdmin, $oConfig->getConfigParam('blNativeImages'));

        return ['path' => $sPath, 'url' => str_replace($sDirPrefix, $sUrlPrefix, $sPath)];
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
        $oConfig = Registry::getConfig();

        $sAltUrl = $oConfig->getConfigParam('sAltImageUrl');
        if (!$sAltUrl) {
            $sAltUrl = $oConfig->getConfigParam('sAltImageDir');
        }

        if ($sAltUrl) {
            if ((is_null($blSSL) && $oConfig->isSsl()) || $blSSL) {
                $sSslAltUrl = $oConfig->getConfigParam('sSSLAltImageUrl');
                if (!$sSslAltUrl) {
                    $sSslAltUrl = $oConfig->getConfigParam('sSSLAltImageDir');
                }

                if ($sSslAltUrl) {
                    $sAltUrl = $sSslAltUrl;
                }
            }

            if (!is_null($sFile)) {
                $sAltUrl = Registry::getUtils()->checkUrlEndingSlash($sAltUrl) . $sFilePath . $sFile;
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
     * @param string|false $sAltPath alternative picture path [optional]
     * @param bool   $bSsl     Whether to force SSL
     *
     * @return string|bool
     */
    public function getPicUrl($sPath, $sFile, $sSize, $sIndex = null, $sAltPath = false, $bSsl = null)
    {
        $sUrl = null;
        if ($sPath && $sFile && ($aSize = $this->getImageSize($sSize, $sIndex))) {
            $aPicInfo = $this->getPictureInfo("master/" . ($sAltPath ?: $sPath), $sFile, $this->isAdmin(), $bSsl);
            if ($aPicInfo['url'] && $aSize[0] && $aSize[1]) {
                $sDirName = "{$aSize[0]}_{$aSize[1]}_" . Registry::getConfig()->getConfigParam('sDefaultImageQuality');
                $sUrl = str_replace("/master/" . ($sAltPath ?: $sPath), "/generated/{$sPath}{$sDirName}/", $aPicInfo['url']);
            }
        }

        // Add webp extension if automatic conversion is enabled
        if (
            $sUrl !== null &&
            Registry::getConfig()->getConfigParam('blConvertImagesToWebP', false) &&
            pathinfo($sUrl, PATHINFO_EXTENSION) != 'webp'
        ) {
            $sUrl .= '.webp';
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
     * @return string|bool
     */
    public function getProductPicUrl($sPath, $sFile, $sSize, $sIndex = null, $bSsl = null)
    {
        $sUrl = null;
        if (!$sFile || !($sUrl = $this->getPicUrl($sPath, $sFile, $sSize, $sIndex, false, $bSsl))) {
            $sUrl = $this->getPicUrl($sPath, $this->getNopicFilename(), $sSize, $sIndex, "/", $bSsl);
        }

        return $sUrl;
    }

    private function getNopicFilename(): string
    {
        if (Registry::getConfig()->getConfigParam('blConvertImagesToWebP')) {
            return 'nopic.webp';
        }

        return 'nopic.jpg';
    }
}
