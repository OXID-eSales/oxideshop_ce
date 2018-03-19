<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\EshopCommunity\Internal\ServiceFactory\FacadeServiceFactory;

/**
 * Article rate manager.
 * Performs loading, updating, inserting of article rates.
 *
 */
class Rating extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Shop control variable
     *
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxrating';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxratings');
    }

    /**
     * Checks if user can rate product.
     *
     * @param string $sUserId   user id
     * @param string $sType     object type
     * @param string $sObjectId object id
     *
     * @return bool
     */
    public function allowRating($sUserId, $sType, $sObjectId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $myConfig = $this->getConfig();

        if ($iRatingLogsTimeout = $myConfig->getConfigParam('iRatingLogsTimeout')) {
            $sExpDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - $iRatingLogsTimeout * 24 * 60 * 60);
            $oDb->execute("delete from oxratings where oxtimestamp < '$sExpDate'");
        }
        $sSelect = "select oxid from oxratings where oxuserid = " . $oDb->quote($sUserId) . " and oxtype=" . $oDb->quote($sType) . " and oxobjectid = " . $oDb->quote($sObjectId);
        if ($oDb->getOne($sSelect)) {
            return false;
        }

        return true;
    }


    /**
     * calculates and return objects rating
     *
     * @param string $sObjectId           object id
     * @param string $sType               object type
     * @param array  $aIncludedObjectsIds array of ids
     *
     * @return float
     */
    public function getRatingAverage($sObjectId, $sType, $aIncludedObjectsIds = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQuerySnipet = '';
        if (is_array($aIncludedObjectsIds) && count($aIncludedObjectsIds) > 0) {
            $sQuerySnipet = " AND ( `oxobjectid` = " . $oDb->quote($sObjectId) . " OR `oxobjectid` in ('" . implode("', '", $aIncludedObjectsIds) . "') )";
        } else {
            $sQuerySnipet = " AND `oxobjectid` = " . $oDb->quote($sObjectId);
        }

        $sSelect = "
            SELECT
                AVG(`oxrating`)
            FROM `oxreviews`
            WHERE `oxrating` > 0
                 AND `oxtype` = " . $oDb->quote($sType)
                   . $sQuerySnipet . "
            LIMIT 1";

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        if ($fRating = $database->getOne($sSelect)) {
            $fRating = round($fRating, 1);
        }

        return $fRating;
    }

    /**
     * calculates and return objects rating count
     *
     * @param string $sObjectId           object id
     * @param string $sType               object type
     * @param array  $aIncludedObjectsIds array of ids
     *
     * @return integer
     */
    public function getRatingCount($sObjectId, $sType, $aIncludedObjectsIds = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQuerySnipet = '';
        if (is_array($aIncludedObjectsIds) && count($aIncludedObjectsIds) > 0) {
            $sQuerySnipet = " AND ( `oxobjectid` = " . $oDb->quote($sObjectId) . " OR `oxobjectid` in ('" . implode("', '", $aIncludedObjectsIds) . "') )";
        } else {
            $sQuerySnipet = " AND `oxobjectid` = " . $oDb->quote($sObjectId);
        }

        $sSelect = "
            SELECT
                COUNT(*)
            FROM `oxreviews`
            WHERE `oxrating` > 0
                AND `oxtype` = " . $oDb->quote($sType)
                   . $sQuerySnipet . "
            LIMIT 1";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $iCount = $masterDb->getOne($sSelect);

        return $iCount;
    }

    /**
     * Retuns review object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->oxratings__oxtype->value;
    }

    /**
     * Retuns review object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->oxratings__oxobjectid->value;
    }

    /**
     * Delete this object from the database, returns true if entry was deleted.
     *
     * @param string $oxid Object ID(default null)
     *
     * @return bool
     */
    public function delete($oxid = null)
    {
        $isProductRating = $this->isProductObjectType();

        $isDeleted = parent::delete($oxid);

        if ($isProductRating) {
            $this->updateProductRating();
        }

        return $isDeleted;
    }


    /**
     * Returns true if Rating belongs to Product.
     *
     * @return bool
     */
    private function isProductObjectType()
    {
        return $this->getObjectType() === 'oxarticle';
    }

    /**
     * Updates Product rating.
     */
    private function updateProductRating()
    {
        $this
            ->getFacadeServiceFactory()
            ->getProductRatingFacade()
            ->updateProductRating($this->getObjectId());
    }

    /**
     * @return FacadeServiceFactory
     */
    private function getFacadeServiceFactory()
    {
        return FacadeServiceFactory::getInstance();
    }
}
