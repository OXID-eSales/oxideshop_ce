<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;

/**
 * Article file link manager.
 *
 */
class OrderFile extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTable = 'oxorderfiles';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxorderfile';


    /**
     * Initialises the instance
     *
     * @return oxOrderFile
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxorderfiles');
    }

    /**
     * reset order files downloadcount and / or expration times
     */
    public function reset()
    {
        $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
        $oArticleFile->load($this->oxorderfiles__oxfileid->value);
        if (file_exists($oArticleFile->getStoreLocation())) {
            $this->oxorderfiles__oxdownloadcount = new \OxidEsales\Eshop\Core\Field(0);
            $this->oxorderfiles__oxfirstdownload = new \OxidEsales\Eshop\Core\Field('0000-00-00 00:00:00');
            $this->oxorderfiles__oxlastdownload = new \OxidEsales\Eshop\Core\Field('0000-00-00 00:00:00');
            $iExpirationTime = $this->oxorderfiles__oxlinkexpirationtime->value * 3600;
            $sNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
            $sDate = date('Y-m-d H:i:s', $sNow + $iExpirationTime);
            $this->oxorderfiles__oxvaliduntil = new \OxidEsales\Eshop\Core\Field($sDate);
            $this->oxorderfiles__oxresetcount = new \OxidEsales\Eshop\Core\Field($this->oxorderfiles__oxresetcount->value + 1);
        }
    }

    /**
     * set order id
     *
     * @param string $sOrderId - order id
     */
    public function setOrderId($sOrderId)
    {
        $this->oxorderfiles__oxorderid = new \OxidEsales\Eshop\Core\Field($sOrderId);
    }

    /**
     * set order article id
     *
     * @param string $sOrderArticleId - order article id
     */
    public function setOrderArticleId($sOrderArticleId)
    {
        $this->oxorderfiles__oxorderarticleid = new \OxidEsales\Eshop\Core\Field($sOrderArticleId);
    }

    /**
     * set shop id
     *
     * @param string $sShopId - shop id
     */
    public function setShopId($sShopId)
    {
        $this->oxorderfiles__oxshopid = new \OxidEsales\Eshop\Core\Field($sShopId);
    }

    /**
     * Set file and download options
     *
     * @param string $sFileName               file name
     * @param string $sFileId                 file id
     * @param int    $iMaxDownloadCounts      max download count
     * @param int    $iExpirationTime         main download time after order in times
     * @param int    $iExpirationDownloadTime download time after first download in hours
     */
    public function setFile($sFileName, $sFileId, $iMaxDownloadCounts, $iExpirationTime, $iExpirationDownloadTime)
    {
        $sNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $sDate = date('Y-m-d G:i', $sNow + $iExpirationTime * 3600);

        $this->oxorderfiles__oxfileid = new \OxidEsales\Eshop\Core\Field($sFileId);
        $this->oxorderfiles__oxfilename = new \OxidEsales\Eshop\Core\Field($sFileName);
        $this->oxorderfiles__oxmaxdownloadcount = new \OxidEsales\Eshop\Core\Field($iMaxDownloadCounts);
        $this->oxorderfiles__oxlinkexpirationtime = new \OxidEsales\Eshop\Core\Field($iExpirationTime);
        $this->oxorderfiles__oxdownloadexpirationtime = new \OxidEsales\Eshop\Core\Field($iExpirationDownloadTime);
        $this->oxorderfiles__oxvaliduntil = new \OxidEsales\Eshop\Core\Field($sDate);
    }

    /**
     * Returns downloadable file size in bytes.
     *
     * @return int
     */
    public function getFileSize()
    {
        $oFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
        $oFile->load($this->oxorderfiles__oxfileid->value);

        return $oFile->getSize();
    }

    /**
     * returns long name
     *
     * @param string $sFieldName - field name
     *
     * @return string
     */
    protected function _getFieldLongName($sFieldName)
    {
        $aFieldNames = [
            'oxorderfiles__oxarticletitle',
            'oxorderfiles__oxarticleartnum',
            'oxorderfiles__oxordernr',
            'oxorderfiles__oxorderdate',
            'oxorderfiles__oxispaid',
            'oxorderfiles__oxpurchasedonly'
        ];

        if (in_array($sFieldName, $aFieldNames)) {
            return $sFieldName;
        }

        return parent::_getFieldLongName($sFieldName);
    }

    /**
     * Checks if order file is still available to download
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->oxorderfiles__oxmaxdownloadcount->value || ($this->oxorderfiles__oxdownloadcount->value < $this->oxorderfiles__oxmaxdownloadcount->value)) {
            if (!$this->oxorderfiles__oxlinkexpirationtime->value && !$this->oxorderfiles__oxdownloadxpirationtime->value) {
                return true;
            } else {
                $sNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
                $iTimestamp = strtotime($this->oxorderfiles__oxvaliduntil->value);
                if (!$iTimestamp || ($iTimestamp > $sNow)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * returns state payed or not the order
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->oxorderfiles__oxispaid->value;
    }

    /**
     * returns date ant time
     *
     * @return bool
     */
    public function getValidUntil()
    {
        return substr($this->oxorderfiles__oxvaliduntil->value, 0, 16);
    }

    /**
     * returns date ant time
     *
     * @return bool
     */
    public function getLeftDownloadCount()
    {
        $iLeft = $this->oxorderfiles__oxmaxdownloadcount->value - $this->oxorderfiles__oxdownloadcount->value;
        if ($iLeft < 0) {
            $iLeft = 0;
        }

        return $iLeft;
    }

    /**
     * Checks if download link is valid, changes count, if first download changes valid until
     *
     * @return bool
     */
    public function processOrderFile()
    {
        if ($this->isValid()) {
            //first download
            if (!$this->oxorderfiles__oxdownloadcount->value) {
                $this->oxorderfiles__oxdownloadcount = new \OxidEsales\Eshop\Core\Field(1);

                $iExpirationTime = $this->oxorderfiles__oxdownloadexpirationtime->value * 3600;
                $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
                $this->oxorderfiles__oxvaliduntil = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', $iTime + $iExpirationTime));

                $this->oxorderfiles__oxfirstdownload = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', $iTime));
                $this->oxorderfiles__oxlastdownload = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', $iTime));
            } else {
                $this->oxorderfiles__oxdownloadcount = new \OxidEsales\Eshop\Core\Field($this->oxorderfiles__oxdownloadcount->value + 1);

                $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
                $this->oxorderfiles__oxlastdownload = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', $iTime));
            }
            $this->save();

            return $this->oxorderfiles__oxfileid->value;
        }

        return false;
    }

    /**
     * Gets field id.
     *
     * @return mixed
     */
    public function getFileId()
    {
        return $this->oxorderfiles__oxfileid->value;
    }
}
